@php
    $info = $statusMap[$fuk->id] ?? [
        'status' => 'black',
        'tooltip' => 'Dokumen belum ada / kosong',
        'missing' => 0,
        'uploaded' => 0,
        'required' => 1
    ];

    $badgeClass = match($info['status']) {
        'green' => 'bg-green-100 text-green-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'red' => 'bg-red-100 text-red-700',
        default => 'bg-gray-900 text-white',
    };
@endphp

<div class="border rounded-lg p-3 bg-white">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <div class="font-semibold text-sm">
                {{ $fuk->id }} - {{ $fuk->name }}
            </div>

            <div class="text-xs text-gray-500 mt-1">
                Upload: <b>{{ $info['uploaded'] }}</b> /
                Wajib: <b>{{ $info['required'] }}</b> /
                Kurang: <b>{{ $info['missing'] }}</b>
            </div>

            {{-- ✅ Step 11 + 12 --}}
            @if(($info['missing'] ?? 0) > 0)
                <div class="mt-2">
                    <a href="{{ route('library.index', ['year' => $year, 'fuk_id' => $fuk->id, 'from' => 'aspek']) }}"
                       class="inline-flex items-center gap-2 text-xs bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition">
                        Upload Dokumen
                    </a>
                </div>
            @endif
        </div>

        <span class="text-xs px-3 py-1 rounded-full {{ $badgeClass }}"
              title="{{ $info['tooltip'] }}">
            {{ strtoupper($info['status']) }}
        </span>
    </div>

    {{-- CHILDREN --}}
    @if($fuk->children->count())
        <div class="mt-3 pl-4 border-l-2 border-gray-200 space-y-3">
            @foreach($fuk->children as $child)
                @include('penilaian.partials.user_fuk_tree', [
                    'fuk' => $child,
                    'statusMap' => $statusMap,
                    'year' => $year
                ])
            @endforeach
        </div>
    @endif
</div>