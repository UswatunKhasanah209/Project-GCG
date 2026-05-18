@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-120px)] overflow-hidden">
    <x-animated-wave-bg />

    <div class="relative z-10 flex flex-col items-center text-center px-6 py-8">

        <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm border border-white/40">
            Library
        </div>

        <div class="mt-5 text-gray-600 max-w-2xl">
            Kelola dokumen GCG dengan lebih cepat. Upload bukti, unduh lembar kerja, dan ambil laporan penilaian.
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-2 gap-10 w-full max-w-5xl">

            <a href="{{ route('library.uploadPage', request()->query()) }}"
               class="group bg-white/80 backdrop-blur-sm rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 border border-white/60">
                <div class="relative">
                    <img src="{{ asset('images/Upload.png') }}"
                         class="w-full h-[280px] object-cover opacity-70 group-hover:scale-105 group-hover:opacity-90 transition duration-300" alt="Upload">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-5xl font-extrabold text-black tracking-wide drop-shadow-sm">UPLOAD</div>
                        <div class="mt-3 text-sm bg-white/80 px-4 py-2 rounded-full shadow">
                            Tambah dan kelola dokumen eviden
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('library.downloadIndex', request()->query()) }}"
               class="group bg-white/80 backdrop-blur-sm rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 border border-white/60">
                <div class="relative">
                    <img src="{{ asset('images/Download.png') }}"
                         class="w-full h-[280px] object-cover opacity-70 group-hover:scale-105 group-hover:opacity-90 transition duration-300" alt="Download">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-5xl font-extrabold text-black tracking-wide drop-shadow-sm">DOWNLOAD</div>
                        <div class="mt-3 text-sm bg-white/80 px-4 py-2 rounded-full shadow">
                            Unduh lembar kerja dan laporan
                        </div>
                    </div>
                </div>
            </a>

        </div>

        @if(request('from') === 'aspek' && request('fuk_id'))
            <div class="mt-8 text-sm text-gray-700 bg-white/80 px-5 py-3 rounded-2xl shadow">
                Mode cepat upload aktif. Klik <b>UPLOAD</b> untuk lanjut upload FUK: <b>{{ request('fuk_id') }}</b>.
            </div>
        @endif
    </div>
</div>
@endsection