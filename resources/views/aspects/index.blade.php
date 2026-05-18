@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Aspek</h2>

    <a href="{{ route('aspects.create') }}" class="btn btn-primary mb-3">+ Tambah Aspek</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nama Aspek</th>
            <th>Tahun</th>
            <th>Aksi</th>
        </tr>

        @foreach ($aspects as $index => $aspect)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $aspect->name }}</td>
            <td>{{ $aspect->year }}</td>
            <td>
                <a href="{{ route('aspects.edit', $aspect->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('aspects.destroy', $aspect->id) }}" method="POST" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
