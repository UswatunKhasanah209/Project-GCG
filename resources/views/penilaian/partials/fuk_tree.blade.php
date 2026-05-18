@php
$current = $existingScores[$fuk->id] ?? null;

// mapping tipe skor
$t = strtolower(trim((string)($fuk->tipe_penilaian ?? '')));

if ($t === '' || str_contains($t,'0,1')) {
    $opts = [0,1];
} elseif (str_contains($t,'-1,0')) {
    $opts = [-1,0];
} elseif (str_contains($t,'0,0.5,1')) {
    $opts = [0,0.5,1];
} elseif (str_contains($t,'0,0.25')) {
    $opts = [0,0.25,0.5,0.75,1];
} else {
    $opts = [0,1];
}

$docs = $docsByFuk[$fuk->id] ?? collect();
@endphp


<div class="border rounded-lg p-4 mb-5 bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-start gap-4">

        <div>
            <div class="font-semibold">
                {{ $fuk->id }} - {{ $fuk->name }}
            </div>

            <div class="text-xs text-gray-500 mt-1">
                Tipe: {{ $fuk->tipe_penilaian ?? '-' }}
            </div>
        </div>

        {{-- INPUT SCORE (LEAF ONLY) --}}
        @if($fuk->children->count() === 0)
            <form method="POST"
                  action="{{ route('admin.penilaian.saveScore') }}"
                  class="flex items-center gap-2">
                @csrf

                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="fuk_id" value="{{ $fuk->id }}">

                <select name="score"
                        class="border rounded-lg p-2 text-sm">
                    @foreach($opts as $v)
                        <option value="{{ $v }}"
                            @selected((string)$current === (string)$v)>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-emerald-600 text-white px-3 py-2 rounded-lg text-sm">
                    Simpan
                </button>
            </form>
        @else
            <span class="text-xs text-gray-400">
                Nilai dihitung dari sub FUK
            </span>
        @endif

    </div>


    {{-- DOKUMEN --}}
    <div class="mt-4">

        <div class="text-sm font-semibold mb-2">
            Dokumen:
        </div>

        @if($docs->count() === 0)
            <div class="text-sm text-gray-400">
                Belum ada dokumen.
            </div>
        @else

            <div class="space-y-2">

                @foreach($docs as $d)

                    @php
                        $statusClass = match($d->review_status) {
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'need_revision' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp

                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded">

                        <div>
                            <a href="{{ asset('storage/'.$d->file_path) }}"
                               target="_blank"
                               class="text-blue-600 underline text-sm">
                                {{ $d->original_name }}
                            </a>

                            <div class="text-xs text-gray-500">
                                Divisi:
                                <b>{{ $d->division->name ?? '-' }}</b>
                            </div>
                        </div>

                        <div class="text-xs px-3 py-1 rounded {{ $statusClass }}">
                            {{ ucfirst(str_replace('_',' ',$d->review_status)) }}
                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>


    {{-- CHILDREN --}}
    @if($fuk->children->count())
        <div class="mt-5 pl-6 border-l-2 border-gray-200 space-y-4">
            @foreach($fuk->children as $child)
                @include('penilaian.partials.fuk_tree', [
                    'fuk' => $child,
                    'year' => $year,
                    'existingScores' => $existingScores,
                    'docsByFuk' => $docsByFuk
                ])
            @endforeach
        </div>
    @endif

</div>