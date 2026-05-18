@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-120px)] overflow-hidden">
    <x-animated-wave-bg />

    <div class="relative z-10 flex flex-col items-center px-6 py-8">

        <div class="flex gap-6 flex-wrap justify-center">
            <a href="{{ route('library.index') }}"
                class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm hover:opacity-90 border border-white/40">
                Library
            </a>

            <a href="{{ route('library.downloadIndex') }}"
                class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm hover:opacity-90 border border-white/40">
                Download
            </a>

            <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm opacity-80 border border-white/40">
                Laporan
            </div>
        </div>

        <div class="w-full max-w-4xl mt-10 bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg p-8 border border-white/50">
            <form method="GET" action="{{ route('library.downloadReport') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <div>
                    <label class="block text-sm font-semibold mb-2">Tahun</label>
                    <input type="number"
                           name="year"
                           value="{{ request('year', $year ?? date('Y')) }}"
                           class="w-full border-2 border-[#B9C8F0] rounded-2xl px-5 py-3 bg-white"
                           placeholder="Masukkan tahun">
                </div>

                @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-sm font-semibold mb-2">Divisi</label>
                        <select name="division_id"
                                class="w-full border-2 border-[#B9C8F0] rounded-2xl px-5 py-3 bg-white">
                            <option value="">Semua</option>
                            @foreach($divisions ?? collect() as $div)
                                <option value="{{ $div->id }}" @selected((string) request('division_id') === (string) $div->id)>
                                    {{ $div->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="md:col-span-2 flex justify-end">
                    <button class="bg-[#B9C8F0] hover:brightness-95 transition text-black font-bold px-8 py-3 rounded-2xl shadow">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>

        <div class="w-full max-w-4xl mt-10 flex flex-col gap-6">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('library.downloadPDF', ['type' => 'all', 'year' => request('year', $year ?? date('Y'))]) }}"
                    class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                    Laporan Keseluruhan
                </a>

                <a href="{{ route('library.downloadPDF', ['type' => 'division', 'year' => request('year', $year ?? date('Y')), 'division_id' => request('division_id')]) }}"
                    class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                    Laporan Per Divisi
                </a>
            @else
                <a href="{{ route('library.downloadPDF', ['type' => 'division', 'year' => request('year', $year ?? date('Y'))]) }}"
                    class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                    Laporan Divisi Saya
                </a>
            @endif
        </div>
    </div>
</div>
@endsection