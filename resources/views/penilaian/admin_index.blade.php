@extends('layouts.app')

@section('content')

    <div class="rounded-3xl p-6" style="background:#edf2fb; border:2px solid #ccdbfd;">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-extrabold">
                Penilaian Admin (Global)
            </h1>

            {{-- breadcrumb klikable (biar ga bingung) --}}
            <div class="flex gap-3">
                <a href="{{ route('library.index') }}" class="px-6 py-2 rounded-full font-semibold"
                    style="background:#d7e3fc;">
                    Library
                </a>
                <a href="{{ route('library.downloadIndex') }}" class="px-6 py-2 rounded-full font-semibold"
                    style="background:#c1d3fe;">
                    Download
                </a>
                <div class="px-6 py-2 rounded-full font-semibold" style="background:#abc4ff;">
                    Penilaian
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl mb-4" style="background:#d7e3fc; border:1px solid #ccdbfd;">
                <span class="font-semibold">✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-xl mb-4 bg-red-100 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.penilaian.index') }}" class="rounded-2xl p-5 mb-8"
            style="background:#d7e3fc; border:2px solid #ccdbfd;">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="font-semibold">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}"
                        class="w-full rounded-xl p-3 mt-1 outline-none"
                        style="background:#edf2fb; border:2px solid #ccdbfd;">
                </div>

                <div>
                    <label class="font-semibold">Aspek</label>
                    <select name="aspek_id" class="w-full rounded-xl p-3 mt-1 outline-none"
                        style="background:#edf2fb; border:2px solid #ccdbfd;">
                        <option value="">-- pilih --</option>
                        @foreach ($aspeks as $a)
                            <option value="{{ $a->id }}" @selected($aspekId == $a->id)>
                                {{ $a->display_name }} - {{ $a->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-semibold">Indikator</label>
                    <select name="indikator_id" class="w-full rounded-xl p-3 mt-1 outline-none"
                        style="background:#edf2fb; border:2px solid #ccdbfd;">
                        <option value="">-- pilih --</option>
                        @foreach ($indikators as $i)
                            <option value="{{ $i->id }}" @selected($indikatorId == $i->id)>
                                {{ $i->id }} - {{ $i->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-semibold">Parameter</label>
                    <select name="parameter_id" class="w-full rounded-xl p-3 mt-1 outline-none"
                        style="background:#edf2fb; border:2px solid #ccdbfd;">
                        <option value="">-- pilih --</option>
                        @foreach ($parameters as $p)
                            <option value="{{ $p->id }}" @selected($parameterId == $p->id)>
                                {{ $p->id }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-4">
                <button class="px-8 py-3 rounded-full font-bold shadow-sm" style="background:#abc4ff;">
                    Tampilkan
                </button>
            </div>
        </form>

        {{-- HASIL NILAI --}}
        @if ($parameterId)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

                <div class="rounded-2xl p-5 shadow-sm" style="background:#edf2fb; border:2px solid #ccdbfd;">
                    <div class="text-sm text-gray-600 mb-1">Nilai Parameter</div>
                    <div class="text-2xl font-bold">
                        {{ $parameterResult !== null ? number_format($parameterResult, 4) : '-' }}
                    </div>
                </div>

                <div class="rounded-2xl p-5 shadow-sm" style="background:#edf2fb; border:2px solid #ccdbfd;">
                    <div class="text-sm text-gray-600 mb-1">Nilai Indikator</div>
                    <div class="text-2xl font-bold">
                        {{ $indikatorResult !== null ? number_format($indikatorResult, 4) : '-' }}
                    </div>
                </div>

                <div class="rounded-2xl p-5 shadow-sm" style="background:#edf2fb; border:2px solid #ccdbfd;">
                    <div class="text-sm text-gray-600 mb-1">Nilai Aspek</div>
                    <div class="text-2xl font-bold">
                        {{ $aspekResult !== null ? number_format($aspekResult, 4) : '-' }}
                    </div>
                </div>

                <div class="rounded-2xl p-5 shadow-sm" style="background:#edf2fb; border:2px solid #ccdbfd;">
                    <div class="text-sm text-gray-600 mb-1">Total Nilai GCG ({{ $year }})</div>
                    <div class="text-2xl font-bold">
                        {{ $totalAspekScore !== null ? number_format($totalAspekScore, 4) : '-' }}
                    </div>
                </div>

            </div>
        @endif

        {{-- TREE --}}
        @if ($parameterId && $rootFuks->count())
            <div class="rounded-2xl p-6" style="background:#d7e3fc; border:2px solid #ccdbfd;">
                <h2 class="font-bold mb-6">
                    Pemenuhan FUK - Parameter {{ $parameterId }}
                </h2>

                @foreach ($rootFuks as $fuk)
                    @include('penilaian.partials.fuk_tree', [
                        'fuk' => $fuk,
                        'year' => $year,
                        'existingScores' => $existingScores,
                        'docsByFuk' => $docsByFuk,
                    ])
                @endforeach
            </div>
        @elseif($parameterId)
            <div class="text-gray-600">
                Tidak ada FUK untuk parameter ini.
            </div>
        @else
            <div class="text-gray-600">
                Pilih Aspek → Indikator → Parameter dulu.
            </div>
        @endif

    </div>

@endsection
