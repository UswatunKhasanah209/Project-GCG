@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-120px)] overflow-hidden">
    <x-animated-wave-bg />

    <div class="relative z-10 flex flex-col items-center px-6 py-8">

        <div class="flex gap-6 flex-wrap justify-center">
            <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm border border-white/40">
                Library
            </div>
            <div class="bg-[#B9C8F0]/90 backdrop-blur-sm text-black font-semibold px-20 py-3 rounded-full shadow-sm border border-white/40">
                Upload
            </div>
        </div>

        <div class="w-full max-w-5xl mt-12">

            @if (!empty($prefillFukId))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-6">
                    <div class="font-semibold">Mode cepat upload</div>
                    <div class="text-sm mt-1">
                        Kamu diarahkan untuk upload dokumen pada
                        <b>FUK ID: {{ $prefillFukId }}</b> (Tahun: <b>{{ $prefillYear }}</b>)
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('library.upload') }}" enctype="multipart/form-data"
                class="bg-white/85 backdrop-blur-sm rounded-2xl shadow-lg p-10 border border-white/60">
                @csrf

                @if (request('from') === 'aspek')
                    <input type="hidden" name="from" value="aspek">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 items-center">

                    <div class="flex items-center justify-between gap-6">
                        <label class="text-3xl font-extrabold">Tahun</label>

                        <input type="number" name="year" value="{{ $prefillYear ?? date('Y') }}"
                            class="w-[280px] border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg focus:outline-none bg-white">
                    </div>

                    <div class="flex items-center justify-between gap-6">
                        <label class="text-3xl font-extrabold">Indikator</label>

                        <select id="indikatorSelect" name="indikator_id"
                            class="w-[280px] border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg bg-white" disabled>
                            <option value="">-- pilih indikator --</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between gap-6">
                        <label class="text-3xl font-extrabold">Aspek</label>

                        <select id="aspekSelect" name="aspek_id"
                            class="w-[280px] border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg bg-white">
                            <option value="">-- pilih aspek --</option>
                            @foreach ($aspeks as $a)
                                <option value="{{ $a->id }}">{{ $a->id }} - {{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-between gap-6">
                        <label class="text-3xl font-extrabold">Parameter</label>

                        <select id="parameterSelect" name="parameter_id"
                            class="w-[280px] border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg bg-white" disabled>
                            <option value="">-- pilih parameter --</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between gap-6 col-span-1 md:col-span-2">
                        <label class="text-3xl font-extrabold">FUK</label>

                        <select id="fukSelect"
                            class="w-[640px] max-w-full border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg bg-white" disabled>
                            <option value="">-- pilih FUK --</option>
                        </select>
                    </div>

                    <input type="hidden" name="fuk_id" id="finalFukId">

                    <div class="flex items-center justify-between gap-6 col-span-1 md:col-span-2">
                        <label class="text-3xl font-extrabold">Upload Dokumen</label>

                        <input type="file" name="document"
                            class="w-[640px] max-w-full border-2 border-[#B9C8F0] rounded-full px-6 py-3 text-lg bg-white">
                    </div>

                </div>

                <div class="flex justify-end mt-12">
                    <button
                        class="bg-[#B9C8F0] hover:brightness-95 transition text-black font-extrabold text-3xl px-24 py-6 rounded-2xl shadow">
                        Upload
                    </button>
                </div>

            </form>

            <div class="mt-12">
                <div class="flex items-center justify-between gap-4 mb-4 flex-wrap">
                    <h2 class="text-xl font-bold">Dokumen</h2>

                    @if (auth()->user()->role === 'admin')
                        <form method="GET" action="{{ route('library.uploadPage') }}" class="flex items-center gap-2 flex-wrap">
                            <input type="hidden" name="year" value="{{ request('year') }}">
                            <label class="text-sm font-semibold">Divisi:</label>
                            <select name="division_id" onchange="this.form.submit()"
                                class="border-2 border-[#B9C8F0] rounded-full px-4 py-2 bg-white text-sm">
                                <option value="">Semua</option>
                                @foreach ($divisions ?? collect() as $div)
                                    <option value="{{ $div->id }}" @selected((string) request('division_id') === (string) $div->id)>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>

                <div class="bg-white/85 backdrop-blur-sm rounded-xl shadow overflow-hidden border border-white/60">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3 text-left">Tahun</th>
                                    <th class="p-3 text-left">Divisi</th>
                                    <th class="p-3 text-left">Edit By</th>
                                    <th class="p-3 text-left">FUK</th>
                                    <th class="p-3 text-left">File</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($documents as $d)
                                    @php
                                        $status = $d->review_status ?? 'pending';
                                        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
                                            $status = 'pending';
                                        }

                                        $badge = match ($status) {
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-yellow-100 text-yellow-800',
                                        };

                                        $isAdmin = auth()->user()->role === 'admin';
                                        $isOwner = !$isAdmin && (int) $d->uploader_user_id === (int) auth()->id();
                                        $canDeleteUser = $isOwner && $status === 'rejected';
                                    @endphp

                                    <tr class="border-t">
                                        <td class="p-3">{{ $d->year }}</td>
                                        <td class="p-3">{{ $d->division?->name }}</td>
                                        <td class="p-3">{{ $d->uploader?->name ?? '-' }}</td>
                                        <td class="p-3">{{ $d->fuk?->id }} - {{ $d->fuk?->name }}</td>

                                        <td class="p-3">
                                            <span class="text-sm">{{ $d->original_name }}</span>
                                        </td>

                                        <td class="p-3">
                                            @if ($isAdmin)
                                                <form method="POST"
                                                    action="{{ route('library.documents.updateStatus', $d->id) }}">
                                                    @csrf
                                                    <select name="review_status" onchange="this.form.submit()"
                                                        class="border-2 border-[#B9C8F0] rounded-full px-4 py-1 text-xs font-semibold bg-white">
                                                        <option value="pending" @selected($status === 'pending')>PENDING</option>
                                                        <option value="approved" @selected($status === 'approved')>APPROVED</option>
                                                        <option value="rejected" @selected($status === 'rejected')>REJECTED</option>
                                                    </select>
                                                </form>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                                    {{ strtoupper($status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="p-3">
                                            <div class="flex items-center gap-3 text-lg">

                                                <a href="{{ asset('storage/' . $d->file_path) }}" target="_blank"
                                                    title="Lihat Dokumen" class="hover:opacity-70">
                                                    👁️
                                                </a>

                                                <a href="{{ route('library.documents.download', $d->id) }}" title="Download"
                                                    class="hover:opacity-70">
                                                    ⬇️
                                                </a>

                                                @if ($isAdmin)
                                                    <form method="POST"
                                                        action="{{ route('library.documents.destroy', $d->id) }}"
                                                        onsubmit="return confirm('Yakin hapus dokumen ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="hover:opacity-70" title="Hapus Dokumen">🗑️</button>
                                                    </form>
                                                @else
                                                    @if ($canDeleteUser)
                                                        <form method="POST"
                                                            action="{{ route('library.documents.destroy', $d->id) }}"
                                                            onsubmit="return confirm('Yakin hapus dokumen ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="hover:opacity-70" title="Hapus Dokumen">🗑️</button>
                                                        </form>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td class="p-3 text-gray-500" colspan="7">Belum ada dokumen</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    const aspekSelect = document.getElementById('aspekSelect');
    const indikatorSelect = document.getElementById('indikatorSelect');
    const parameterSelect = document.getElementById('parameterSelect');

    const fukSelect = document.getElementById('fukSelect');
    const finalFukId = document.getElementById('finalFukId');

    const PREFILL_FUK_ID = @json($prefillFukId ?? null);

    function reset(select, text) {
        select.innerHTML = `<option value="">${text}</option>`;
        select.disabled = true;
    }

    function resetFuk() {
        reset(fukSelect, '-- pilih FUK --');
        finalFukId.value = '';
    }

    function applyPrefillIfAny() {
        if (!PREFILL_FUK_ID) return;

        finalFukId.value = PREFILL_FUK_ID;

        aspekSelect.disabled = true;
        indikatorSelect.disabled = true;
        parameterSelect.disabled = true;
        fukSelect.disabled = true;

        fukSelect.innerHTML = `<option value="${PREFILL_FUK_ID}" selected>${PREFILL_FUK_ID}</option>`;
    }
    applyPrefillIfAny();

    if (!PREFILL_FUK_ID) {

        aspekSelect.addEventListener('change', async () => {
            reset(indikatorSelect, '-- pilih indikator --');
            reset(parameterSelect, '-- pilih parameter --');
            resetFuk();

            if (!aspekSelect.value) return;

            const res = await fetch(`/library/indikators/${aspekSelect.value}`);
            const data = await res.json();

            indikatorSelect.disabled = false;
            data.forEach(i => {
                indikatorSelect.innerHTML += `<option value="${i.id}">${i.id} - ${i.name}</option>`;
            });
        });

        indikatorSelect.addEventListener('change', async () => {
            reset(parameterSelect, '-- pilih parameter --');
            resetFuk();

            if (!indikatorSelect.value) return;

            const res = await fetch(`/library/parameters/${indikatorSelect.value}`);
            const data = await res.json();

            parameterSelect.disabled = false;
            data.forEach(p => {
                parameterSelect.innerHTML += `<option value="${p.id}">${p.id} - ${p.name}</option>`;
            });
        });

        parameterSelect.addEventListener('change', async () => {
            resetFuk();

            if (!parameterSelect.value) return;

            const res = await fetch(`/library/fuks/${parameterSelect.value}`);
            const data = await res.json();

            fukSelect.disabled = false;
            data.forEach(f => {
                fukSelect.innerHTML += `<option value="${f.id}">${f.id} - ${f.name}</option>`;
            });
        });

        fukSelect.addEventListener('change', () => {
            const id = fukSelect.value;
            finalFukId.value = id || '';
        });
    }
</script>
@endpush