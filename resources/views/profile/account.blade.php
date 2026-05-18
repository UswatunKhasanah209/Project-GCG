@extends('layouts.app')

@section('content')
@php
    $avatarUrl = $user->avatar
        ? asset('storage/' . $user->avatar) . '?v=' . optional($user->updated_at)->timestamp
        : null;
@endphp

<div class="min-h-screen bg-white px-6 py-6">
    <div class="max-w-3xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('profile.index') }}" class="text-[#06496b] hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h1 class="text-2xl font-bold text-[#06496b]">Akun</h1>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="mb-6 rounded-2xl bg-green-50 border border-green-200 text-green-700 px-5 py-4 font-semibold">
                Profil berhasil diperbarui.
            </div>
        @endif

        <div class="flex flex-col items-center mb-10">
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
                            d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5a7.5 7.5 0 0115 0"/>
                    </svg>
                </div>
            @endif

            <a href="{{ route('profile.edit') }}" class="mt-3 text-[#06496b] font-semibold hover:underline">
                Edit
            </a>
        </div>

        <div class="max-w-xl mx-auto space-y-9">

            <div class="grid grid-cols-[70px_1fr] items-center gap-5">
                <div class="text-[#2e7892] flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/>
                        <path d="M3 22a9 9 0 0118 0H3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-[#2e7892]">Nama</div>
                    <div class="mt-1 text-lg font-semibold text-black">{{ $user->name ?? '-' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-[70px_1fr] items-center gap-5">
                <div class="text-[#2e7892] flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3 6 6 .9-4.5 4.3 1.1 6.1L12 16.4 6.4 19.3l1.1-6.1L3 8.9 9 8l3-6z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-[#2e7892]">Role</div>
                    <div class="mt-1 text-lg font-semibold text-black">{{ ucfirst($user->role ?? '-') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-[70px_1fr] items-center gap-5">
                <div class="text-[#2e7892] flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-[#2e7892]">Divisi</div>
                    <div class="mt-1 text-lg font-semibold text-black">{{ $user->division_name ?? '-' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-[70px_1fr] items-center gap-5">
                <div class="text-[#06496b] flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-[#2e7892]">Email</div>
                    <div class="mt-1 text-lg font-semibold text-black break-all">{{ $user->email ?? '-' }}</div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection