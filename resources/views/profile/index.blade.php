@extends('layouts.app')

@section('content')
@php
    $avatarUrl = $user->avatar
        ? asset('storage/' . $user->avatar) . '?v=' . optional($user->updated_at)->timestamp
        : null;
@endphp

<div class="min-h-screen bg-white px-6 py-6">
    <div class="max-w-3xl mx-auto">

        <div class="flex items-center gap-4 mb-10">
            <a href="{{ route('dashboard') }}" class="text-[#06496b] hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <h1 class="text-2xl font-bold text-[#06496b]">Profil</h1>
        </div>

        <div class="flex justify-center mb-14">
            @if($avatarUrl)
                <img
                    src="{{ $avatarUrl }}"
                    class="w-36 h-36 rounded-full object-cover border-[4px] border-[#2e7892] shadow-sm"
                    alt="Avatar"
                >
            @else
                <div class="w-36 h-36 rounded-full border-[4px] border-[#2e7892] flex items-center justify-center text-[#2e7892]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-28 h-28" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0115 0" />
                    </svg>
                </div>
            @endif
        </div>

        <div class="max-w-lg mx-auto space-y-7">
            <a href="{{ route('profile.account') }}" class="flex items-center gap-8 text-[#2e7892] group">
                <div class="w-16 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 group-hover:scale-105 transition"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/>
                        <path d="M3 22a9 9 0 0118 0H3z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold">Akun</span>
            </a>

            <a href="{{ route('profile.history') }}" class="flex items-center gap-8 text-[#2e7892] group">
                <div class="w-16 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 group-hover:scale-105 transition"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 109-9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4v5h5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                    </svg>
                </div>
                <span class="text-xl font-bold">Histori Download</span>
            </a>

            <a href="{{ route('profile.info') }}" class="flex items-center gap-8 text-[#2e7892] group">
                <div class="w-16 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 group-hover:scale-105 transition"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M12 15v-4m0-4h.01" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 22a10 10 0 100-20 10 10 0 000 20z" />
                    </svg>
                </div>
                <span class="text-xl font-bold">Info Website</span>
            </a>
        </div>

        <div class="text-center mt-20 space-y-7">
            <form method="POST" action="{{ route('profile.logout') }}">
                @csrf
                <button type="submit" class="block mx-auto text-xl font-bold text-[#2e7892] hover:opacity-80">
                    Beralih akun
                </button>
            </form>

            <form method="POST" action="{{ route('profile.logout') }}">
                @csrf
                <button type="submit" class="text-xl font-bold text-[#2e7892] hover:opacity-80">
                    Log Out
                </button>
            </form>
        </div>

    </div>
</div>
@endsection