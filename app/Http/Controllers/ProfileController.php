<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\DownloadHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('divisionData');

        return view('profile.index', compact('user'));
    }

    public function account(Request $request): View
    {
        $user = $request->user()->load('divisionData');

        return view('profile.account', compact('user'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user()->load('divisionData');

        return view('profile.edit', compact('user'));
    }

    public function history(Request $request): View
    {
        $user = $request->user()->load('divisionData');

        $histories = DownloadHistory::with('document')
            ->where('user_id', $user->id)
            ->latest('downloaded_at')
            ->paginate(10);

        return view('profile.history', compact('user', 'histories'));
    }

    public function info(Request $request): View
    {
        $user = $request->user()->load('divisionData');

        return view('profile.info', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('cropped_avatar')) {
            $image = $request->input('cropped_avatar');

            if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                $image = substr($image, strpos($image, ',') + 1);
                $image = base64_decode($image);

                if ($image !== false) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    $fileName = 'avatars/avatar_' . $user->id . '_' . time() . '.jpg';

                    Storage::disk('public')->put($fileName, $image);

                    $user->avatar = $fileName;
                }
            }
        }

        $user->save();

        $user->load('divisionData');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => ucfirst($user->role ?? '-'),
                    'division' => $user->division_name,
                    'avatar_url' => $user->avatar
                        ? asset('storage/' . $user->avatar) . '?v=' . $user->updated_at?->timestamp
                        : null,
                ],
            ]);
        }

        return Redirect::route('profile.account')->with('status', 'profile-updated');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}