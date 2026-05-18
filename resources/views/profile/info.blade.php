@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white px-6 py-6">
    <div class="max-w-3xl mx-auto">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('profile.index') }}" class="text-[#06496b] hover:opacity-80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-[#06496b]">Info Website</h1>
        </div>

        <div class="rounded-3xl border border-gray-100 shadow-sm p-7 space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-[#2e7892] mb-2">Sistem GCG</h2>
                <p class="text-gray-600 leading-relaxed">
                    Website ini digunakan untuk mengelola dokumen, penilaian, laporan, dan monitoring progres GCG berdasarkan aspek, indikator, parameter, dan FUK.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-2xl bg-[#eef8fb] p-5">
                    <div class="text-[#2e7892] font-bold text-lg mb-1">Library</div>
                    <p class="text-sm text-gray-600">
                        Upload, review, download dokumen, kertas kerja Excel, dan laporan PDF.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#eef8fb] p-5">
                    <div class="text-[#2e7892] font-bold text-lg mb-1">Penilaian</div>
                    <p class="text-sm text-gray-600">
                        Admin dapat melakukan penilaian FUK dan melihat progres penilaian.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#eef8fb] p-5">
                    <div class="text-[#2e7892] font-bold text-lg mb-1">Dashboard</div>
                    <p class="text-sm text-gray-600">
                        Menampilkan progres dan grafik berdasarkan tahun yang dipilih.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#eef8fb] p-5">
                    <div class="text-[#2e7892] font-bold text-lg mb-1">Profil</div>
                    <p class="text-sm text-gray-600">
                        Pengguna dapat mengubah nama, email, dan foto profil secara cepat.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl bg-gray-50 p-5">
                <div class="font-bold text-[#06496b] mb-2">Bantuan</div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Jika terdapat data akun, divisi, dokumen, atau hasil penilaian yang tidak sesuai,
                    hubungi admin sistem agar dapat diperiksa dan diperbaiki.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection