@extends('layouts.app')

@section('content')
<div class="pt-28 px-10">

    <div class="flex items-start justify-between gap-6 mb-6">

        <div>
            <a href="{{ route('aspects.index', ['year' => $year]) }}"
               class="text-blue-600 underline text-sm">
                ← Kembali
            </a>

            <h1 class="text-2xl font-extrabold mt-2">
                Detail Kelengkapan Dokumen — {{ $division->name }}
            </h1>
            <p class="text-gray-500 mt-1">
                Tahun {{ $year }}
            </p>
        </div>

        <div class="flex items-center gap-3">

            <form method="GET" action="{{ route('aspects.admin.division', $division->id) }}" class="flex items-center gap-3">
                <input type="hidden" name="show" value="{{ $show }}">
                <span class="font-semibold">Tahun:</span>
                <select name="year"
                        onchange="this.form.submit()"
                        class="border-2 border-[#8FA8D6] px-6 py-2 rounded-full bg-white">
                    @for($y = now()->year; $y >= 2000; $y--)
                        <option value="{{ $y }}" @selected($y==$year)>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <form method="GET" action="{{ route('aspects.admin.division', $division->id) }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <select name="show" onchange="this.form.submit()"
                        class="border px-4 py-2 rounded-lg bg-white">
                    <option value="missing" @selected($show==='missing')>Tampilkan yang kurang saja</option>
                    <option value="all" @selected($show==='all')>Tampilkan semua</option>
                </select>
            </form>

        </div>

    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Total FUK</div>
            <div class="text-2xl font-bold">{{ $summary['totalFuk'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">FUK yang masih kurang</div>
            <div class="text-2xl font-bold text-red-600">{{ $summary['missingFuk'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Total Dokumen Terupload</div>
            <div class="text-2xl font-bold text-emerald-600">{{ $summary['uploadedDocs'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Total Kekurangan Dokumen</div>
            <div class="text-2xl font-bold text-orange-600">{{ $summary['missingDocs'] }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-5 border-b">
            <h2 class="font-bold">Daftar FUK</h2>
            <p class="text-sm text-gray-500 mt-1">
                Status warna: Hitam (kosong), Kuning (kurang 1), Merah (kurang >1), Hijau (lengkap)
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left p-3">Aspek</th>
                        <th class="text-left p-3">Indikator</th>
                        <th class="text-left p-3">Parameter</th>
                        <th class="text-left p-3">FUK</th>
                        <th class="text-center p-3">Wajib</th>
                        <th class="text-center p-3">Upload</th>
                        <th class="text-center p-3">Kurang</th>
                        <th class="text-center p-3">Status</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($rows as $r)
                        @php
                            $badgeClass = match($r['status']) {
                                'green' => 'bg-green-100 text-green-700',
                                'yellow' => 'bg-yellow-100 text-yellow-700',
                                'red' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-900 text-white'
                            };
                        @endphp

                        <tr class="border-t align-top">
                            <td class="p-3">
                                {{ $r['aspek']->id ?? '-' }}<br>
                                <span class="text-xs text-gray-500">{{ $r['aspek']->name ?? '-' }}</span>
                            </td>
                            <td class="p-3">
                                {{ $r['indikator']->id ?? '-' }}<br>
                                <span class="text-xs text-gray-500">{{ $r['indikator']->name ?? '-' }}</span>
                            </td>
                            <td class="p-3">
                                {{ $r['parameter']->id ?? '-' }}<br>
                                <span class="text-xs text-gray-500">{{ $r['parameter']->name ?? '-' }}</span>
                            </td>
                            <td class="p-3">
                                <div class="font-semibold">{{ $r['fuk']->id }} - {{ $r['fuk']->name }}</div>

                                {{-- Dokumen list --}}
                                @if($r['docs']->count())
                                    <div class="mt-2 space-y-1">
                                        @foreach($r['docs'] as $d)
                                            <div class="text-xs bg-gray-50 p-2 rounded flex items-center justify-between gap-4">
                                                <a class="text-blue-600 underline"
                                                   href="{{ asset('storage/'.$d->file_path) }}"
                                                   target="_blank">
                                                    {{ $d->original_name }}
                                                </a>
                                                <span class="text-gray-500">
                                                    {{ $d->review_status ?? '-' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 mt-2">Belum ada dokumen.</div>
                                @endif
                            </td>

                            <td class="p-3 text-center font-semibold">{{ $r['required'] }}</td>
                            <td class="p-3 text-center font-semibold">{{ $r['uploaded'] }}</td>
                            <td class="p-3 text-center font-semibold">{{ $r['missing'] }}</td>

                            <td class="p-3 text-center">
                                <span class="inline-block text-xs px-3 py-1 rounded-full {{ $badgeClass }}"
                                      title="{{ $r['tooltip'] }}">
                                    {{ strtoupper($r['status']) }}
                                </span>
                                <div class="text-[11px] text-gray-500 mt-1">{{ $r['tooltip'] }}</div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-500">
                                Tidak ada data untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection