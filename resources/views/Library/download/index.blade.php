@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-120px)] overflow-hidden">
    <x-animated-wave-bg />

    <div class="relative z-10 flex flex-col items-center px-6 py-8">

        <div class="flex gap-6 flex-wrap justify-center">
            <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm border border-white/40">
                Library
            </div>

            <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm border border-white/40">
                Download
            </div>
        </div>

        <div class="mt-5 text-gray-600 text-center max-w-2xl">
            Pilih jenis file yang ingin diunduh.
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-2 gap-10 w-full max-w-5xl">
            <a href="{{ route('library.downloadWorksheet') }}"
               class="group bg-white/80 backdrop-blur-sm rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 border border-white/60">
                <div class="relative">
                    <img src="{{ asset('images/excel.png') }}"
                         class="w-full h-[280px] object-cover opacity-70 group-hover:scale-105 group-hover:opacity-90 transition duration-300">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-4xl md:text-5xl font-extrabold text-black">Lembar Kerja</div>
                        <div class="mt-3 text-sm bg-white/80 px-4 py-2 rounded-full shadow">
                            Format Excel
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('library.downloadReport') }}"
               class="group bg-white/80 backdrop-blur-sm rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 border border-white/60">
                <div class="relative">
                    <img src="{{ asset('images/pdf.png') }}"
                         class="w-full h-[280px] object-cover opacity-70 group-hover:scale-105 group-hover:opacity-90 transition duration-300">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-4xl md:text-5xl font-extrabold text-black">Laporan</div>
                        <div class="mt-3 text-sm bg-white/80 px-4 py-2 rounded-full shadow">
                            Format PDF
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection