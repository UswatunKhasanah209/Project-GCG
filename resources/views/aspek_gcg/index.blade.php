@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center">

    <div class="bg-[#BDDaf2] text-black font-semibold px-20 py-3 rounded-full shadow-sm mb-8">
        Aspek GCG
    </div>

    <form method="GET" class="w-full max-w-6xl flex items-center gap-6 mb-10">

        <div class="flex items-center gap-4">
            <div class="text-4xl font-extrabold">Tahun</div>
            <input type="number" name="year" value="{{ $year }}"
                   class="w-44 rounded-full px-6 py-3 border-2 border-[#BDDaf2] bg-white outline-none">
        </div>

        {{-- admin: pilih divisi --}}
        @if($divisions->count())
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold">DIVISI</div>
                <select name="division_id"
                        class="w-96 rounded-full px-6 py-3 border-2 border-[#BDDaf2] bg-white outline-none">
                    <option value="">-- pilih divisi --</option>
                    @foreach($divisions as $d)
                        <option value="{{ $d->id }}" @selected((string)$divisionId === (string)$d->id)>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <button class="ml-auto rounded-full px-12 py-3 font-bold text-white"
                style="background:#338cd9;">
            CARI
        </button>
    </form>

    @php
        $imgMap = [
            1 => 'images/Aspek I.png',
            2 => 'images/Aspek II.png',
            3 => 'images/Aspek III.png',
            4 => 'images/Aspek IV.png',
            5 => 'images/Aspek V.png',
            6 => 'images/Aspek VI.png',
        ];
    @endphp

    <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-3 gap-10">

        @foreach($aspeks as $a)
            @php
                $img = $imgMap[$a->id] ?? 'images/Aspek I.png';
                $p = $progressMap[$a->id]['percent'] ?? 0;
            @endphp

            <a href="{{ route('aspek.gcg.show', ['aspek'=>$a->id, 'year'=>$year, 'division_id'=>$divisionId]) }}"
               class="group bg-white rounded-[2rem] overflow-hidden shadow hover:shadow-lg transition relative">

                <img src="{{ asset($img) }}"
                     class="w-full h-[200px] object-cover opacity-60 group-hover:opacity-80 transition">

                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="text-4xl font-extrabold text-black">
                        ASPEK {{ $a->id }}
                    </div>
                    <div class="mt-3 px-5 py-2 rounded-full text-sm font-bold"
                         style="background:#BDDaf2;">
                        {{ $p }}%
                    </div>
                </div>
            </a>
        @endforeach

    </div>

</div>
@endsection