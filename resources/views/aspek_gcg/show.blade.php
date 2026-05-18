@extends('layouts.app')

@section('content')
<div class="flex flex-col">

    <div class="flex gap-6 justify-center mb-8">
        <a href="{{ route('aspek.gcg.index', ['year'=>$year, 'division_id'=>$divisionId]) }}"
           class="bg-[#BDDaf2] text-black font-semibold px-20 py-3 rounded-full shadow-sm hover:opacity-90">
            Aspek GCG
        </a>

        <div class="bg-[#BDDaf2] text-black font-semibold px-20 py-3 rounded-full shadow-sm opacity-80">
            Aspek {{ $aspek->id }}
        </div>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold uppercase">
            {{ $aspek->name ?? ('ASPEK '.$aspek->id) }}
        </h1>

        <div class="flex items-center gap-4">
            <div class="w-64 h-4 rounded-full overflow-hidden" style="background:#BDDaf2;">
                <div class="h-4 rounded-full" style="width: {{ $percent }}%; background:#338cd9;"></div>
            </div>
            <div class="text-4xl font-extrabold">{{ $percent }}%</div>
        </div>
    </div>

    {{-- ADMIN dropdown divisi --}}
    @if($divisions->count())
        <form method="GET" class="mb-8 flex items-center gap-4">
            <input type="hidden" name="year" value="{{ $year }}">
            <div class="font-bold text-xl">Divisi:</div>

            <select name="division_id"
                    class="w-96 rounded-full px-6 py-3 border-2 border-[#BDDaf2] bg-white outline-none">
                <option value="">-- pilih divisi --</option>
                @foreach($divisions as $d)
                    <option value="{{ $d->id }}" @selected((string)$divisionId === (string)$d->id)>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>

            <button class="rounded-full px-10 py-3 font-bold text-white" style="background:#1d629e;">
                Tampilkan
            </button>
        </form>
    @endif

    <div class="space-y-4">

        @foreach($leafFuks as $f)
            @php
                $info = $statusMap[$f->id] ?? ['status'=>'black','tooltip'=>'-','missing'=>0,'uploaded'=>0,'required'=>1];

                $badge = match($info['status']) {
                    'green' => 'bg-green-500 text-white',
                    'yellow' => 'bg-yellow-400 text-black',
                    'red' => 'bg-red-600 text-white',
                    default => 'bg-black text-white',
                };

                $label = match($info['status']) {
                    'green' => 'Complete',
                    'yellow' => 'Missing 1',
                    'red' => 'Incomplete',
                    default => 'Incomplete',
                };
            @endphp

            <div class="rounded-2xl overflow-hidden border-2" style="border-color:#78b3e6;">
                <div class="px-6 py-3 text-white font-extrabold" style="background:#78b3e6;">
                    FUK
                </div>

                <div class="bg-white px-6 py-4 flex items-center justify-between gap-6">
                    <div class="text-sm">
                        <div class="font-semibold">
                            {{ $f->id }} - {{ $f->name }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Upload: <b>{{ $info['uploaded'] }}</b> / Wajib: <b>{{ $info['required'] }}</b>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-6 py-2 rounded-full text-sm font-bold {{ $badge }}"
                              title="{{ $info['tooltip'] }}">
                            {{ $label }}
                        </span>

                        {{-- USER: upload kalau kurang --}}
                        @if($divisions->count() === 0 && ($info['missing'] ?? 0) > 0)
                            <a href="{{ route('library.uploadPage', ['year'=>$year, 'fuk_id'=>$f->id, 'from'=>'aspek']) }}"
                               class="px-6 py-2 rounded-full text-white font-bold"
                               style="background:#338cd9;">
                                Upload Dokumen
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        @endforeach

    </div>

</div>

{{-- OPTIONAL realtime refresh --}}
@push('scripts')
<script>
  // aktifkan kalau kamu mau progress auto update tanpa refresh manual
  // setInterval(() => window.location.reload(), 10000);
</script>
@endpush

@endsection