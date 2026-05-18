@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Aspek</h2>

    <form action="{{ route('aspects.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Aspek</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tahun</label>
            <input type="number" name="year" class="form-control" required>
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
