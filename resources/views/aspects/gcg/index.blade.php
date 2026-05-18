@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-center mb-10">
        <div class="bg-[#BDDaf2] text-black font-semibold px-24 py-4 rounded-full shadow-sm text-2xl">
            Aspek GCG
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        @foreach($aspekCards as $c)
            <a href="{{ route('aspek.gcg.show', ['aspek' => $c['aspek']->id]) }}"
               class="group bg-white rounded-[2rem] overflow-hidden shadow hover:shadow-lg transition relative">

                <img src="{{ asset($c['img']) }}"
                     class="w-full h-[240px] object-cover opacity-60 group-hover:opacity-80 transition">

                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-5xl font-extrabold text-black">
                        ASPEK {{ $c['roman'] }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

</div>
@endsection