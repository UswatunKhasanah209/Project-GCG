@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Aspek</h2>

    <form action="{{ route('aspects.update', $aspect->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Aspek</label>
            <input type="text" name="name" value="{{ $aspect->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tahun</label>
            <input type="number" name="year" value="{{ $aspect->year }}" class="form-control" required>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
