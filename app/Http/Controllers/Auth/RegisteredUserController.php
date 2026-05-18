<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $divisions = Division::orderBy('name')->get();

        return view('auth.register', compact('divisions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'division_id' => ['required', 'exists:divisions,id'],
            'bagian' => ['required', 'string', 'in:tata kelola,other'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $bagian = strtolower(trim($request->bagian));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'division_id' => $request->division_id,
            'password' => Hash::make($request->password),
            'role' => $bagian === 'tata kelola' ? 'admin' : 'user',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}