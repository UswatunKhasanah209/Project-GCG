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
                Lembar Kerja
            </div>
        </div>

        <div class="w-full max-w-4xl mt-10 bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg p-8 border border-white/50">
            <form method="GET" action="{{ route('library.downloadWorksheet') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
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
                        <label class="block text-sm font-semibold mb-2">Versi Kertas Kerja</label>
                        <select name="score_state" class="w-full border-2 border-[#B9C8F0] rounded-2xl px-5 py-3 bg-white">
                            <option value="unscored" {{ request('score_state', $scoreState ?? 'unscored') === 'unscored' ? 'selected' : '' }}>
                                Versi Kosong
                            </option>
                            <option value="scored" {{ request('score_state', $scoreState ?? 'unscored') === 'scored' ? 'selected' : '' }}>
                                Versi Sudah Ada Penilaian
                            </option>
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
            <a href="{{ route('library.downloadExcel', ['type' => 'all', 'year' => request('year', $year ?? date('Y')), 'score_state' => request('score_state', $scoreState ?? 'unscored')]) }}"
                class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                Keseluruhan
            </a>

            <a href="{{ route('library.downloadExcel', ['type' => 'aspek', 'year' => request('year', $year ?? date('Y')), 'score_state' => request('score_state', $scoreState ?? 'unscored')]) }}"
                class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                Aspek
            </a>

            <a href="{{ route('library.downloadExcel', ['type' => 'indikator', 'year' => request('year', $year ?? date('Y')), 'score_state' => request('score_state', $scoreState ?? 'unscored')]) }}"
                class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                Indikator
            </a>

            <a href="{{ route('library.downloadExcel', ['type' => 'parameter', 'year' => request('year', $year ?? date('Y')), 'score_state' => request('score_state', $scoreState ?? 'unscored')]) }}"
                class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                Parameter
            </a>

            <a href="{{ route('library.downloadExcel', ['type' => 'fuk', 'year' => request('year', $year ?? date('Y')), 'score_state' => request('score_state', $scoreState ?? 'unscored')]) }}"
                class="bg-white/85 backdrop-blur-sm border-2 border-[#B9C8F0] rounded-2xl py-6 text-center text-2xl font-bold hover:bg-[#B9C8F0]/20 hover:-translate-y-1 transition">
                FUK
            </a>
        </div>
    </div>
</div>
@endsection