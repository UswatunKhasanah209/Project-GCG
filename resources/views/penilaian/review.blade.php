@extends('layouts.app')

@section('content')
    <div class="p-6">

        <h1 class="text-2xl font-bold mb-6">Review Dokumen (Admin)</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Tahun</th>
                        <th class="p-3 text-left">Divisi</th>
                        <th class="p-3 text-left">FUK</th>
                        <th class="p-3 text-left">File</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($documents as $doc)
                        <tr class="border-t">
                            <td class="p-3">{{ $doc->year }}</td>
                            <td class="p-3">{{ $doc->division->name }}</td>
                            <td class="p-3">
                                {{ $doc->fuk->id }} - {{ $doc->fuk->name }}
                            </td>
                            <td class="p-3">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                    class="text-blue-600 underline">
                                    {{ $doc->original_name }}
                                </a>
                            </td>
                            <td class="p-3">
                                {{ $doc->review_status }}
                            </td>
                            <td class="p-3">

                                <form method="POST" action="{{ route('penilaian.review.update', $doc->id) }}"
                                    class="space-y-2">
                                    @csrf

                                    <select name="review_status" class="border rounded p-1 text-sm">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    </select>

                                    <input type="text" name="review_note" placeholder="Catatan"
                                        class="border rounded p-1 text-sm w-full">

                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                        Update
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>

    </div>
@endsection
