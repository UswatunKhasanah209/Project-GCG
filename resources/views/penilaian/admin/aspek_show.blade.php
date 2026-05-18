@extends('layouts.app')

@section('content')
    @php
        $aspekBox = $displayData['aspek'][$aspek->id] ?? [
            'bobot' => 0,
            'score' => 0,
            'percent' => 0,
        ];
    @endphp

    <div class="rounded-3xl p-6" style="background:#edf2fb;border:2px solid #ccdbfd;">

        <div class="flex items-center justify-between mb-8">
            <div class="flex gap-4">
                <a href="{{ route('admin.penilaian.index', ['year' => $year]) }}"
                    class="px-14 py-3 rounded-full text-black font-extrabold text-2xl"
                    style="background:#abc4ff;">
                    Penilaian
                </a>

                <div class="px-14 py-3 rounded-full text-black font-extrabold text-2xl"
                    style="background:#b8c8f0;">
                    {{ $aspek->display_name }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-56 h-4 rounded-full overflow-hidden" style="background:#8ce5ea;">
                    <div class="h-full rounded-full" style="width: {{ $progress }}%; background:#38b7d7;"></div>
                </div>
                <div class="text-5xl font-serif text-black">{{ $progress }}%</div>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white px-6 py-5" style="border:2px solid #214f7a;">
                <div class="text-sm font-bold text-gray-500 mb-2">Bobot Aspek</div>
                <div class="text-2xl font-extrabold">
                    {{ rtrim(rtrim(number_format($aspekBox['bobot'], 3, '.', ''), '0'), '.') }}
                </div>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5" style="border:2px solid #214f7a;">
                <div class="text-sm font-bold text-gray-500 mb-2">Skor Aspek</div>
                <div class="text-2xl font-extrabold">
                    {{ rtrim(rtrim(number_format($aspekBox['score'], 3, '.', ''), '0'), '.') }}
                </div>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5" style="border:2px solid #214f7a;">
                <div class="text-sm font-bold text-gray-500 mb-2">Persen Aspek</div>
                <div class="text-2xl font-extrabold">
                    {{ round($aspekBox['percent']) }}%
                </div>
            </div>
        </div>

        <div class="mb-2 text-xl font-bold text-gray-700">
            Tahun Penilaian: {{ $year }}
        </div>

        <h1 class="text-center text-4xl font-extrabold uppercase leading-tight mb-8">
            {{ $aspek->name }}
        </h1>

        @if (session('success'))
            <div class="mb-6 rounded-2xl px-5 py-4 bg-green-100 text-green-800 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl px-5 py-4 bg-red-100 text-red-700 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @foreach ($indikators as $indikator)
            @php
                $indikatorBox = $displayData['indikators'][$indikator->id] ?? [
                    'bobot' => 0,
                    'score' => 0,
                    'percent' => 0,
                ];
            @endphp

            <div class="mb-8">
                <div class="rounded-t-2xl px-6 py-4 text-white text-2xl font-bold" style="background:#164b7a;">
                    INDIKATOR {{ $indikator->id }}
                </div>

                <div class="border-2 border-t-0 rounded-b-2xl overflow-hidden" style="border-color:#6ea9e6;">
                    <div class="px-6 py-5 border-b bg-white" style="border-color:#6ea9e6;">
                        <div class="grid grid-cols-12 gap-5 items-start">
                            <div class="col-span-6 font-medium text-lg leading-relaxed">
                                {{ $indikator->name }}
                            </div>

                            <div class="col-span-2">
                                <div class="text-sm font-bold text-center mb-2">Bobot</div>
                                <div class="h-16 rounded-2xl flex items-center justify-center text-2xl font-bold"
                                    style="border:2px solid #214f7a;">
                                    {{ rtrim(rtrim(number_format($indikatorBox['bobot'], 3, '.', ''), '0'), '.') }}
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-sm font-bold text-center mb-2">Skor</div>
                                <div class="h-16 rounded-2xl flex items-center justify-center text-2xl font-bold"
                                    style="border:2px solid #214f7a;">
                                    {{ rtrim(rtrim(number_format($indikatorBox['score'], 3, '.', ''), '0'), '.') }}
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-sm font-bold text-center mb-2">%</div>
                                <div class="h-16 rounded-2xl flex items-center justify-center text-2xl font-bold"
                                    style="border:2px solid #214f7a;">
                                    {{ round($indikatorBox['percent']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($indikator->parameters as $parameter)
                        @php
                            $parameterBox = $displayData['parameters'][$parameter->id] ?? [
                                'bobot' => 0,
                                'score' => 0,
                                'percent' => 0,
                            ];
                        @endphp

                        <div class="border-b last:border-b-0" style="border-color:#c8dafc;">
                            <div class="px-6 py-6">
                                <div class="rounded-2xl overflow-hidden" style="border:2px solid #6ea9e6;">

                                    <div class="px-6 py-4 text-white text-2xl font-bold" style="background:#2b6ea8;">
                                        PARAMETER
                                    </div>

                                    <div class="px-5 py-4 bg-white border-b" style="border-color:#6ea9e6;">
                                        <div class="grid grid-cols-12 gap-5 items-start">
                                            <div class="col-span-6 font-medium text-base leading-relaxed">
                                                {{ $parameter->id }}. {{ $parameter->name }}
                                            </div>

                                            <div class="col-span-2">
                                                <div class="text-sm font-bold text-center mb-2">Bobot</div>
                                                <div class="h-14 rounded-2xl flex items-center justify-center text-xl font-bold"
                                                    style="border:2px solid #214f7a;">
                                                    {{ rtrim(rtrim(number_format($parameterBox['bobot'], 3, '.', ''), '0'), '.') }}
                                                </div>
                                            </div>

                                            <div class="col-span-2">
                                                <div class="text-sm font-bold text-center mb-2">Skor</div>
                                                <div class="h-14 rounded-2xl flex items-center justify-center text-xl font-bold"
                                                    style="border:2px solid #214f7a;">
                                                    {{ rtrim(rtrim(number_format($parameterBox['score'], 3, '.', ''), '0'), '.') }}
                                                </div>
                                            </div>

                                            <div class="col-span-1">
                                                <div class="text-sm font-bold text-center mb-2">%</div>
                                                <div class="h-14 rounded-2xl flex items-center justify-center text-xl font-bold"
                                                    style="border:2px solid #214f7a;">
                                                    {{ round($parameterBox['percent']) }}
                                                </div>
                                            </div>

                                            <div class="col-span-1">
                                                <div class="text-sm font-bold text-center mb-2">Action</div>
                                                <div class="h-14 rounded-2xl flex items-center justify-center text-xl font-bold text-white"
                                                    style="background:#164b7a;">
                                                    ▼
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 pt-5 pb-6 bg-[#edf2fb]">
                                        <div class="rounded-2xl overflow-hidden" style="border:2px solid #6ea9e6;">

                                            <div class="px-6 py-4 text-white text-2xl font-bold"
                                                style="background:#78aedd;">
                                                FUK
                                            </div>

                                            <div class="bg-white">
                                                @foreach ($parameter->fuks as $fuk)
                                                    @include('penilaian.admin.partials.fuk_rows', [
                                                        'fuk' => $fuk,
                                                        'displayData' => $displayData,
                                                        'year' => $year,
                                                        'level' => 0,
                                                    ])
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @endforeach

    </div>
@endsection