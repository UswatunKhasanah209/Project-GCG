@extends('layouts.app')

@section('content')
    <div class="pt-28 px-10">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-extrabold">Aspek GCG (User)</h1>

            <form method="GET" action="{{ route('aspects.user') }}" class="flex items-center gap-3">
                <span class="font-semibold">Tahun:</span>
                <select name="year" onchange="this.form.submit()"
                    class="border-2 border-[#8FA8D6] px-6 py-2 rounded-full bg-white">
                    @for ($y = now()->year; $y >= 2000; $y--)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        @if (isset($message))
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6">
                {{ $message }}
            </div>
        @endif

        {{-- Aspek Cards --}}
        <div class="space-y-6">
            @foreach ($aspeks as $aspek)
                @php
                    $agg = $aspekAgg[$aspek->id] ?? ['status' => 'black', 'tooltip' => 'Dokumen belum ada / kosong'];
                    $badgeClass = match ($agg['status']) {
                        'green' => 'bg-green-100 text-green-700',
                        'yellow' => 'bg-yellow-100 text-yellow-700',
                        'red' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-900 text-white',
                    };
                @endphp

                <div class="bg-white rounded-2xl shadow p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xl font-bold">
                                {{ $aspek->id }} - {{ $aspek->name }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ $agg['tooltip'] }}
                            </div>
                        </div>

                        <span class="text-xs px-3 py-1 rounded-full {{ $badgeClass }}" title="{{ $agg['tooltip'] }}">
                            {{ strtoupper($agg['status']) }}
                        </span>
                    </div>

                    {{-- Indikator di dalam aspek --}}
                    <div class="mt-5 space-y-4">
                        @foreach ($tree[$aspek->id] ?? [] as $indikatorId => $paramGroup)
                            @php
                                $ind = $indikators->firstWhere('id', $indikatorId);
                                $iAgg = $indikatorAgg[$indikatorId] ?? [
                                    'status' => 'black',
                                    'tooltip' => 'Dokumen belum ada / kosong',
                                ];
                                $iClass = match ($iAgg['status']) {
                                    'green' => 'bg-green-100 text-green-700',
                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                    'red' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-900 text-white',
                                };
                            @endphp

                            <div class="border rounded-xl p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="font-semibold">
                                        {{ $ind->id ?? '-' }} - {{ $ind->name ?? '-' }}
                                        <div class="text-xs text-gray-500 mt-1">{{ $iAgg['tooltip'] }}</div>
                                    </div>

                                    <span class="text-xs px-3 py-1 rounded-full {{ $iClass }}"
                                        title="{{ $iAgg['tooltip'] }}">
                                        {{ strtoupper($iAgg['status']) }}
                                    </span>
                                </div>

                                {{-- Parameters --}}
                                <div class="mt-4 space-y-4">
                                    @foreach ($paramGroup as $paramId => $obj)
                                        @php
                                            $p = $obj['parameter'];
                                            $pAgg = $obj['statusAgg'];
                                            $pClass = match ($pAgg['status']) {
                                                'green' => 'bg-green-100 text-green-700',
                                                'yellow' => 'bg-yellow-100 text-yellow-700',
                                                'red' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-900 text-white',
                                            };
                                        @endphp

                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="font-semibold">
                                                    {{ $p->id }} - {{ $p->name }}
                                                    <div class="text-xs text-gray-500 mt-1">{{ $pAgg['tooltip'] }}</div>
                                                </div>
                                                <span class="text-xs px-3 py-1 rounded-full {{ $pClass }}"
                                                    title="{{ $pAgg['tooltip'] }}">
                                                    {{ strtoupper($pAgg['status']) }}
                                                </span>
                                            </div>

                                            {{-- FUK Tree --}}
                                            <div class="mt-4 space-y-3">
                                                @foreach ($obj['rootFuks'] as $rf)
                                                    @include('aspects.partials.user_fuk_tree', [
                                                        'fuk' => $rf,
                                                        'statusMap' => $statusMap,
                                                        'year' => $year,
                                                    ])
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endsection
