<?php

namespace App\Http\Controllers;

use App\Models\LibraryDocument;
use App\Models\Division;
use App\Models\Aspek;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // hanya admin yang boleh akses halaman penilaian
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        return redirect()->route('admin.penilaian.index');
    }

    public function reviewIndex(Request $request)
    {
        $user = $request->user();

        // hanya admin yang boleh akses
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $query = LibraryDocument::with(['division', 'uploader', 'fuk']);

        // filter tahun
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // filter divisi
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // filter aspek
        if ($request->filled('aspek_id')) {
            $query->where('aspek_id', $request->aspek_id);
        }

        $documents = $query->latest()->paginate(20);

        return view('penilaian.review', [
            'documents' => $documents,
            'divisions' => Division::all(),
            'aspeks' => Aspek::all(),
        ]);
    }

    public function updateStatus(Request $request, LibraryDocument $document)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'review_status' => 'required|in:pending,approved,rejected',
            'review_note' => 'nullable|string',
        ]);

        $document->update([
            'review_status' => $request->review_status,
            'review_note' => $request->review_note,
            'reviewed_by' => $user->id,
        ]);

        return back()->with('success', 'Status berhasil diperbarui');
    }
}