@extends('layouts.app')

@section('content')
<div class="p-6">

    <h2 class="text-xl font-bold mb-4">Detail Penilaian Parameter</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Informasi Parameter --}}
        <div class="p-4 bg-red-700 text-white rounded-xl">
            <h3 class="font-bold mb-2">Informasi Parameter</h3>

            <p><strong>Indikator:</strong><br>{{ $parameter->indicator->name ?? '-' }}</p>
            <p class="mt-3"><strong>Parameter:</strong><br>{{ $parameter->name }}</p>
        </div>

        {{-- Form Penilaian --}}
        <div class="col-span-2 p-4 border rounded-xl">
            <h3 class="font-bold mb-4">Form Penilaian Internal</h3>

            <form action="{{ route('penilaian.parameter.store', $parameter->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Kekuatan --}}
                <label class="font-semibold">Kekuatan</label>
                <textarea name="kekuatan" class="w-full border p-2 rounded">{{ $penilaian->kekuatan ?? '' }}</textarea>

                {{-- Kelemahan --}}
                <label class="font-semibold mt-3 block">Kelemahan</label>
                <textarea name="kelemahan" class="w-full border p-2 rounded">{{ $penilaian->kelemahan ?? '' }}</textarea>

                {{-- Masalah --}}
                <label class="font-semibold mt-3 block">Identifikasi Masalah</label>
                <textarea name="masalah" class="w-full border p-2 rounded">{{ $penilaian->masalah ?? '' }}</textarea>

                {{-- Rekomendasi --}}
                <label class="font-semibold mt-3 block">Rekomendasi</label>
                <textarea name="rekomendasi" class="w-full border p-2 rounded">{{ $penilaian->rekomendasi ?? '' }}</textarea>

                {{-- Upload Evidence --}}
                <h3 class="font-semibold mt-6">Upload Evidence</h3>

                <input type="file" name="evidence[]" multiple class="mt-2">

                {{-- Existing evidence --}}
                @foreach ($evidences as $e)
                    <div class="mt-2 p-2 bg-gray-100 rounded flex justify-between">
                        <a href="{{ $e->url() }}" target="_blank">{{ $e->file_name }}</a>

                        <form action="{{ route('evidence.delete', $e->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                @endforeach

                {{-- Bobot --}}
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div>
                        <label>I/P (%)</label>
                        <input type="number" name="ip" class="w-full border p-2 rounded" value="{{ $penilaian->nilai_ip ?? '' }}">
                    </div>
                    <div>
                        <label>SP (%)</label>
                        <input type="number" name="sp" class="w-full border p-2 rounded" value="{{ $penilaian->nilai_sp ?? '' }}">
                    </div>
                    <div>
                        <label>SSP (%)</label>
                        <input type="number" name="ssp" class="w-full border p-2 rounded" value="{{ $penilaian->nilai_ssp ?? '' }}">
                    </div>
                    <div>1
                        <label>SSSP (%)</label>
                        <input type="number" name="sssp" class="w-full border p-2 rounded" value="{{ $penilaian->nilai_sssp ?? '' }}">
                    </div>
                </div>

                <button class="mt-6 w-full bg-blue-600 text-white p-2 rounded-xl hover:bg-blue-700">
                    Simpan Penilaian
                </button>

            </form>
        </div>

    </div>

</div>
@endsection
