@php
    $fukData = $displayData['fuks'][$fuk->id] ?? [
        'bobot' => 0,
        'score' => null,
        'weighted_score' => 0,
        'percent' => 0,
    ];

    $children = $fuk->childrenRecursive ?? collect();
    $isLeaf = $children->count() === 0;
    $indent = $level * 22;
@endphp

<div class="grid grid-cols-12 gap-4 items-start px-4 py-4 border-t"
     style="border-color:#d7e5ff; background:white;">

    <div class="col-span-7" style="padding-left: {{ $indent }}px;">
        <div class="text-sm md:text-base leading-relaxed pt-8">
            {{ $fuk->id }}. {{ $fuk->name }}
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-xs text-center font-bold mb-2">Bobot</div>
        <div class="h-10 rounded-2xl flex items-center justify-center text-sm font-semibold"
             style="border:2px solid #214f7a;">
            {{ rtrim(rtrim(number_format($fukData['bobot'], 3, '.', ''), '0'), '.') }}
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-xs text-center font-bold mb-2">Score</div>
        <div class="h-10 rounded-2xl flex items-center justify-center text-sm font-semibold"
             style="border:2px solid #214f7a;">
            {{ rtrim(rtrim(number_format($fukData['weighted_score'], 3, '.', ''), '0'), '.') }}
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-xs text-center font-bold mb-2">%</div>
        <div class="h-10 rounded-2xl flex items-center justify-center text-sm font-semibold"
             style="border:2px solid #214f7a;">
            @if ($isLeaf)
                {{ $fukData['score'] !== null ? '100' : '0' }}
            @else
                {{ round($fukData['percent']) }}
            @endif
        </div>
    </div>

    <div class="col-span-1">
        <div class="text-xs text-center font-bold mb-2">Action</div>
        <div class="flex justify-center">
            @if ($isLeaf)
                <a href="{{ route('admin.penilaian.fuk.form', ['fuk' => $fuk->id, 'year' => $year]) }}"
                   class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-lg font-bold"
                   style="background:#164b7a;">
                    ▼
                </a>
            @else
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-lg font-bold opacity-60"
                     style="background:#7ba9d8;">
                    ▼
                </div>
            @endif
        </div>
    </div>
</div>

@if ($children->count())
    @foreach ($children as $child)
        @include('penilaian.admin.partials.fuk_rows', [
            'fuk' => $child,
            'displayData' => $displayData,
            'year' => $year,
            'level' => $level + 1,
        ])
    @endforeach
@endif