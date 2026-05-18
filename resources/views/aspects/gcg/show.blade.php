@extends('layouts.app')

@php
    $badge = fn($st) => match ($st) {
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-400',
        'red' => 'bg-red-500',
        default => 'bg-gray-900',
    };

    $label = fn($st) => match ($st) {
        'green' => 'Complete',
        'yellow' => 'Missing 1',
        'red' => 'In Complete',
        default => 'Incomplete',
    };

    $gridCols = $divisions->count() ? 'md:grid-cols-5' : 'md:grid-cols-4';
@endphp

@section('content')
    <div class="max-w-6xl mx-auto">

        <div class="flex justify-center gap-6 mb-10">
            <div class="bg-[#BDDaf2] text-black font-semibold px-20 py-4 rounded-full shadow-sm text-2xl">
                Aspek GCG
            </div>

            <div class="bg-[#BDDaf2] text-black font-semibold px-20 py-4 rounded-full shadow-sm text-2xl">
                Aspek {{ $roman }}
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-green-100 text-green-700 px-5 py-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('aspek.gcg.show', $aspek->id) }}" class="bg-white rounded-3xl shadow p-8 mb-8">
            <div class="grid grid-cols-1 {{ $gridCols }} gap-6">

                {{-- TAHUN --}}
                <div>
                    <label class="block font-bold text-xl mb-3">Tahun</label>

                    <input type="number" name="year" value="{{ $year }}"
                        class="w-full border-2 border-[#BDDaf2] rounded-2xl px-5 py-4 text-xl bg-white"
                        onkeydown="if(event.key==='Enter'){ this.form.submit(); }" onchange="handleYearChange(this.form)">

                </div>

                {{-- ADMIN: tampilkan DIVISI --}}
                @if ($divisions->count())
                    <div>
                        <label class="block font-bold text-xl mb-3">Divisi</label>
                        <select id="division_id" name="division_id"
                            class="w-full border-2 border-[#BDDaf2] rounded-2xl px-5 py-4 text-xl bg-white"
                            onchange="this.form.submit()">
                            @foreach ($divisions as $d)
                                <option value="{{ $d->id }}" @selected((int) $divisionId === (int) $d->id)>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- INDIKATOR --}}
                <div>
                    <label class="block font-bold text-xl mb-3">Indikator</label>
                    <select id="indikator_id" name="indikator_id"
                        class="w-full border-2 border-[#BDDaf2] rounded-2xl px-5 py-4 text-xl bg-white"
                        onchange="handleIndikatorChange(this.form)">
                        <option value="">-- pilih indikator --</option>

                        @foreach ($indikators as $i)
                            <option value="{{ $i->id }}" @selected((string) $indikatorId === (string) $i->id)>
                                {{ $i->id }} - {{ $i->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PARAMETER --}}
                <div>
                    <label class="block font-bold text-xl mb-3">Parameter</label>
                    <select id="parameter_id" name="parameter_id"
                        class="w-full border-2 border-[#BDDaf2] rounded-2xl px-5 py-4 text-xl bg-white"
                        onchange="handleParameterChange(this.form)">
                        <option value="">-- pilih parameter --</option>

                        @foreach ($parameters as $p)
                            <option value="{{ $p->id }}" @selected((string) $parameterId === (string) $p->id)>
                                {{ $p->id }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FUK --}}
                <div>
                    <label class="block font-bold text-xl mb-3">FUK</label>
                    <select id="fuk_id" name="fuk_id"
                        class="w-full border-2 border-[#BDDaf2] rounded-2xl px-5 py-4 text-xl bg-white"
                        onchange="this.form.submit()">
                        <option value="">-- pilih fuk --</option>

                        @foreach ($fuks as $f)
                            <option value="{{ $f->id }}" @selected((string) $fukId === (string) $f->id)>
                                {{ $f->id }} - {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>

        @if (!$hasSearch)
            <div class="bg-white rounded-3xl shadow p-10 text-center text-gray-500 text-lg">
                Pilih <b>Indikator</b>, <b>Parameter</b>, lalu <b>FUK</b> terlebih dahulu untuk menampilkan hasil.
            </div>
        @elseif($result)
            @php $st = $result['status']; @endphp

            <div class="bg-white rounded-3xl shadow border-2 border-[#BDDaf2] overflow-hidden">
                <div class="px-6 py-5 bg-[#f8fbff] border-b border-[#BDDaf2]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <div class="text-sm text-gray-500 mb-2">Indikator</div>
                            <div class="font-bold text-xl">
                                {{ $result['indikator']->id ?? '-' }} - {{ $result['indikator']->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500 mb-2">Parameter</div>
                            <div class="font-bold text-xl">
                                {{ $result['parameter']->id ?? '-' }} - {{ $result['parameter']->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500 mb-2">FUK</div>
                            <div class="font-bold text-xl">
                                {{ $result['fuk']->id ?? '-' }} - {{ $result['fuk']->name ?? '-' }}
                            </div>
                        </div>

                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-start justify-between gap-6 flex-wrap">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ $badge($st) }}"></div>
                                <div class="text-2xl font-extrabold">
                                    Status FUK
                                </div>
                                <span class="text-sm px-4 py-2 rounded-full text-white {{ $badge($st) }}">
                                    {{ $label($st) }}
                                </span>
                            </div>

                            <div class="text-gray-600">
                                {{ $result['tooltip'] }}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div class="bg-[#edf2fb] rounded-2xl p-4">
                                    <div class="text-sm text-gray-500">Upload</div>
                                    <div class="text-2xl font-bold">{{ $result['uploaded'] }}</div>
                                </div>

                                <div class="bg-[#edf2fb] rounded-2xl p-4">
                                    <div class="text-sm text-gray-500">Wajib</div>
                                    <div class="text-2xl font-bold">{{ $result['required'] }}</div>
                                </div>

                                <div class="bg-[#edf2fb] rounded-2xl p-4">
                                    <div class="text-sm text-gray-500">Kurang</div>
                                    <div class="text-2xl font-bold">{{ $result['missing'] }}</div>
                                </div>
                            </div>
                        </div>

                        @if (auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('aspek.gcg.fukStatus.update') }}"
                                class="flex items-center gap-3">
                                @csrf
                                <input type="hidden" name="division_id" value="{{ $divisionId }}">
                                <input type="hidden" name="fuk_id" value="{{ $result['fuk']->id }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="redirect" value="{{ url()->full() }}">

                                <select name="status" class="border rounded-2xl px-5 py-3 text-base"
                                    onchange="this.form.submit()">
                                    <option value="black" @selected($st === 'black')>Not Available (Hitam)</option>
                                    <option value="red" @selected($st === 'red')>In Complete (Merah)</option>
                                    <option value="yellow" @selected($st === 'yellow')>Missing 1 (Kuning)</option>
                                    <option value="green" @selected($st === 'green')>Complete (Hijau)</option>
                                </select>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl shadow p-10 text-center text-gray-500 text-lg">
                Tidak ada data yang sesuai filter.
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        function handleYearChange(form) {
            const indikator = document.getElementById('indikator_id');
            const parameter = document.getElementById('parameter_id');
            const fuk = document.getElementById('fuk_id');

            if (indikator) indikator.value = '';
            if (parameter) parameter.value = '';
            if (fuk) fuk.value = '';

            form.submit();
        }

        function handleIndikatorChange(form) {
            const parameter = document.getElementById('parameter_id');
            const fuk = document.getElementById('fuk_id');

            if (parameter) parameter.value = '';
            if (fuk) fuk.value = '';

            form.submit();
        }

        function handleParameterChange(form) {
            const fuk = document.getElementById('fuk_id');
            if (fuk) fuk.value = '';

            form.submit();
        }
    </script>
@endpush
