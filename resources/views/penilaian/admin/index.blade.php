@extends('layouts.app')

@section('content')
    <div class="rounded-3xl p-6" style="background:#edf2fb;border:2px solid #ccdbfd;">

        <div class="flex items-center justify-between mb-10">
            <div class="px-16 py-3 rounded-full text-black font-extrabold text-2xl" style="background:#abc4ff;">
                Penilaian
            </div>

            <form method="GET" action="{{ route('admin.penilaian.index') }}" class="flex items-center gap-4">
                <label class="text-3xl font-extrabold text-black">Tahun</label>

                <div class="flex items-center rounded-full px-6 py-2 bg-white"
                    style="border:3px solid #b6c8fa; min-width: 210px;">
                    <input type="number" name="year" value="{{ $year }}"
                        class="w-32 bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none text-xl font-semibold shadow-none"
                        placeholder="2025">
                    <button type="submit" class="ml-3 text-2xl leading-none">
                        🔍
                    </button>
                </div>
            </form>
        </div>

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

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
            @foreach ($aspeks as $aspek)
                @php
                    $imageMap = [
                        'A1' => 'images/aspek-a1.png',
                        'A2' => 'images/aspek-a2.png',
                        'A3' => 'images/aspek-a3.png',
                        'A4' => 'images/aspek-a4.png',
                        'A5' => 'images/aspek-a5.png',
                        'A6' => 'images/aspek-a6.png',
                        '1' => 'images/aspek-a1.png',
                        '2' => 'images/aspek-a2.png',
                        '3' => 'images/aspek-a3.png',
                        '4' => 'images/aspek-a4.png',
                        '5' => 'images/aspek-a5.png',
                        '6' => 'images/aspek-a6.png',
                        'I' => 'images/aspek-a1.png',
                        'II' => 'images/aspek-a2.png',
                        'III' => 'images/aspek-a3.png',
                        'IV' => 'images/aspek-a4.png',
                        'V' => 'images/aspek-a5.png',
                        'VI' => 'images/aspek-a6.png',
                    ];

                    $img = $imageMap[(string) $aspek->id] ?? 'images/aspek-default.png';
                @endphp

                <a href="{{ route('admin.penilaian.aspek', ['aspek' => $aspek->id, 'year' => $year]) }}"
                    class="group block rounded-[28px] overflow-hidden shadow-sm hover:shadow-lg transition"
                    style="background:#dfe9ff;">

                    <div class="relative h-[300px] overflow-hidden rounded-[28px]">
                        <img src="{{ asset($img) }}" alt="{{ $aspek->display_name }}"
                            class="w-full h-full object-cover opacity-45 group-hover:opacity-55 transition"
                            onerror="this.style.display='none'">

                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-5xl font-extrabold text-black tracking-wide">
                                {{ $aspek->display_name }}
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4">
                        <div class="w-full h-3 bg-white rounded-full overflow-hidden mb-3">
                            <div class="h-full rounded-full" style="width:{{ $aspek->progress }}%; background:#4bc6df;">
                            </div>
                        </div>

                        <div class="text-sm font-semibold text-gray-700">
                            {{ $aspek->filled_fuk }}/{{ $aspek->total_fuk }} FUK dinilai • Tahun {{ $year }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
@endsection
