<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Models\Indikator;
use App\Models\Parameter;
use App\Models\Fuk;
use App\Models\LibraryDocument;
use App\Models\Division;
use App\Models\FukScore;
use App\Models\DownloadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\WorksheetExport;

class LibraryController extends Controller
{
    private function saveDownloadHistory($user, string $fileName, ?string $filePath = null, ?int $documentId = null): void
    {
        DownloadHistory::create([
            'user_id' => $user->id,
            'library_document_id' => $documentId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'downloaded_at' => now(),
        ]);
    }

    public function index()
    {
        return view('library.index');
    }

    public function uploadPage(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $aspeks = Aspek::orderBy('id')->get();

        $prefillYear = (int) ($request->get('year') ?? now()->year);
        $prefillFukId = $request->get('fuk_id');

        $isAdmin = $user->role === 'admin';
        $divisions = collect();

        $query = LibraryDocument::with(['division', 'fuk', 'uploader'])->latest();

        if ($isAdmin) {
            $divisions = Division::orderBy('id')->get();

            if ($request->filled('division_id')) {
                $query->where('division_id', (int) $request->division_id);
            }

            if ($request->filled('year')) {
                $query->where('year', (int) $request->year);
            }

            $documents = $query->limit(200)->get();
        } else {
            $documents = $query
                ->where('uploader_user_id', $user->id)
                ->when($request->filled('year'), function ($q) use ($request) {
                    $q->where('year', (int) $request->year);
                })
                ->limit(50)
                ->get();
        }

        return view('library.upload', compact(
            'aspeks',
            'documents',
            'prefillYear',
            'prefillFukId',
            'isAdmin',
            'divisions'
        ));
    }

    public function downloadIndex(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);

        return view('library.download.index', compact('year'));
    }

    public function downloadWorksheet(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);

        $user = Auth::user();
        $scoreState = 'unscored';

        if ($user && $user->role === 'admin') {
            $scoreState = $request->get('score_state', 'unscored');

            if (!in_array($scoreState, ['unscored', 'scored'], true)) {
                $scoreState = 'unscored';
            }
        }

        return view('library.download.worksheet', compact('year', 'scoreState'));
    }

    public function downloadReport(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);
        $user = Auth::user();

        $divisions = collect();

        if ($user && $user->role === 'admin') {
            $divisions = Division::orderBy('name')->get();
        }

        return view('library.download.report', compact('year', 'divisions'));
    }

    public function indikators($aspekId)
    {
        return response()->json(
            Indikator::where('aspect_id', $aspekId)
                ->orderBy('id')
                ->get()
        );
    }

    public function parameters($indikatorId)
    {
        return response()->json(
            Parameter::where('indikator_id', $indikatorId)
                ->orderBy('id')
                ->get()
        );
    }

    public function fuksByParameter($parameterId)
    {
        return response()->json(
            Fuk::where('parameter_id', $parameterId)
                ->whereNull('parent_id')
                ->orderBy('id')
                ->get()
        );
    }

    public function fukChildren($fukId)
    {
        return response()->json(
            Fuk::where('parent_id', $fukId)
                ->orderBy('id')
                ->get()
        );
    }

    public function upload(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if (!$user->division_id) {
            return back()->with('error', 'Akun kamu belum punya divisi. Hubungi admin.');
        }

        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:1900', 'max:999999'],
            'fuk_id' => ['required', 'string', 'exists:fuks,id'],
            'document' => ['required', 'file', 'max:10240'],
            'from' => ['nullable', 'string'],
        ]);

        $year = (int) $validated['year'];
        $fukId = $validated['fuk_id'];
        $from = $request->input('from');

        $aspekId = null;
        $indikatorId = null;
        $parameterId = null;

        if ($from === 'aspek') {
            $fuk = Fuk::with(['parameter.indikator.aspect'])->findOrFail($fukId);

            $parameterId = $fuk->parameter_id ?? null;
            $indikatorId = $fuk->parameter?->indikator_id ?? null;
            $aspekId = $fuk->parameter?->indikator?->aspect_id ?? null;

            if (!$parameterId || !$indikatorId || !$aspekId) {
                return back()->with('error', 'Data relasi FUK belum lengkap. Hubungi admin.');
            }
        } else {
            $validated2 = $request->validate([
                'aspek_id' => ['required', 'string', 'exists:aspeks,id'],
                'indikator_id' => ['required', 'string', 'exists:indikators,id'],
                'parameter_id' => ['required', 'string', 'exists:parameters,id'],
            ]);

            $aspekId = $validated2['aspek_id'];
            $indikatorId = $validated2['indikator_id'];
            $parameterId = $validated2['parameter_id'];
        }

        if (!$aspekId || !$indikatorId || !$parameterId || !$fukId) {
            return back()->with('error', 'Aspek/Indikator/Parameter/FUK tidak valid.');
        }

        $file = $request->file('document');
        $path = $file->store('library_documents/' . $year, 'public');

        LibraryDocument::create([
            'division_id' => $user->division_id,
            'uploader_user_id' => $user->id,
            'year' => $year,
            'aspek_id' => $aspekId,
            'indikator_id' => $indikatorId,
            'parameter_id' => $parameterId,
            'fuk_id' => $fukId,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'review_status' => 'pending',
        ]);

        if ($from === 'aspek') {
            return redirect()
                ->route('aspects.user', ['year' => $year])
                ->with('success', 'Dokumen berhasil diupload.');
        }

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function replaceDocument(Request $request, LibraryDocument $document)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ((int) $document->uploader_user_id !== (int) $user->id) {
            abort(403);
        }

        $request->validate([
            'document' => ['required', 'file', 'max:10240'],
        ]);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $file = $request->file('document');
        $path = $file->store('library_documents/' . $document->year, 'public');

        $document->update([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'review_status' => 'pending',
        ]);

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function updateStatus(Request $request, LibraryDocument $document)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'review_status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $document->update([
            'review_status' => $request->review_status,
        ]);

        return back()->with('success', 'Status dokumen diperbarui.');
    }

    public function downloadDocument(LibraryDocument $document)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $isAdmin = $user->role === 'admin';
        $isOwner = (int) $document->uploader_user_id === (int) $user->id;

        if (!$isAdmin && !$isOwner) {
            abort(403);
        }

        $fullPath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'File tidak ditemukan di storage.');
        }

        $this->saveDownloadHistory(
            $user,
            $document->original_name,
            $document->file_path,
            $document->id
        );

        return response()->download($fullPath, $document->original_name);
    }

    public function destroyDocument(LibraryDocument $document)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $isAdmin = $user->role === 'admin';
        $isOwner = (int) $document->uploader_user_id === (int) $user->id;

        if (!$isAdmin) {
            if (!$isOwner) {
                abort(403);
            }

            if (($document->review_status ?? 'pending') !== 'rejected') {
                return back()->with('error', 'Dokumen hanya bisa dihapus user jika status REJECTED.');
            }
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function renameDocument(Request $request, LibraryDocument $document)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $isAdmin = $user->role === 'admin';
        $isOwner = (int) $document->uploader_user_id === (int) $user->id;

        if ($isAdmin || !$isOwner) {
            abort(403);
        }

        $request->validate([
            'original_name' => ['required', 'string', 'max:255'],
        ]);

        $document->update([
            'original_name' => $request->original_name,
        ]);

        return response()->json(['ok' => true]);
    }

    public function downloadExcel(Request $request, $type)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $year = (int) ($request->get('year') ?? now()->year);

        $allowed = ['all', 'aspek', 'indikator', 'parameter', 'fuk'];

        if (!in_array($type, $allowed, true)) {
            abort(404);
        }

        $scoreState = $user->role === 'admin'
            ? $request->get('score_state', 'unscored')
            : 'unscored';

        if (!in_array($scoreState, ['scored', 'unscored'], true)) {
            $scoreState = 'unscored';
        }

        $filename = "worksheet_{$type}_{$year}_" . ($scoreState === 'scored' ? 'bernilai' : 'kosong') . ".xlsx";

        $this->saveDownloadHistory($user, $filename, null, null);

        return Excel::download(
            new WorksheetExport($type, $year, $user, $scoreState),
            $filename
        );
    }

    public function downloadPDF(Request $request, $type)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $year = (int) ($request->get('year') ?? now()->year);

        if (!in_array($type, ['all', 'division'], true)) {
            abort(404);
        }

        $selectedDivisionId = null;

        if ($user->role !== 'admin') {
            $type = 'division';
            $selectedDivisionId = $user->division_id;
        } else {
            if ($type === 'division' && $request->filled('division_id')) {
                $selectedDivisionId = (int) $request->division_id;
            }
        }

        $documents = LibraryDocument::with([
                'division',
                'fuk.parameter.indikator.aspect',
            ])
            ->where('year', $year)
            ->when($selectedDivisionId, function ($q) use ($selectedDivisionId) {
                $q->where('division_id', $selectedDivisionId);
            })
            ->orderBy('division_id')
            ->orderBy('fuk_id')
            ->get();

        $scores = FukScore::where('year', $year)->get()->keyBy('fuk_id');

        $rows = [];

        foreach ($documents as $doc) {
            $score = $scores->get($doc->fuk_id);

            if (!$score || $score->score === null || $score->score === '') {
                continue;
            }

            $scorePercent = is_numeric($score->score) ? ((float) $score->score * 100) : null;

            $rows[] = [
                'division' => $doc->division?->name ?? '-',
                'aspek' => $doc->fuk?->parameter?->indikator?->aspect?->name ?? '-',
                'indikator' => $doc->fuk?->parameter?->indikator?->name ?? '-',
                'parameter' => $doc->fuk?->parameter?->name ?? '-',
                'fuk' => $doc->fuk?->id . ' - ' . ($doc->fuk?->name ?? '-'),
                'dokumen' => $score->document_name ?: ($doc->original_name ?? '-'),
                'score' => $score->score,
                'score_percent' => $scorePercent !== null
                    ? rtrim(rtrim(number_format($scorePercent, 2, '.', ''), '0'), '.') . '%'
                    : '-',
                'halaman' => $score->page_reference ?? '',
                'penjelasan' => $score->explanation ?? '',
                'review' => $score->assessor_review ?? '',
                'rekomendasi' => $score->recommendation ?? '',
            ];
        }

        $selectedDivisionName = null;

        if ($selectedDivisionId) {
            $selectedDivisionName = Division::where('id', $selectedDivisionId)->value('name');
        }

        $pdf = Pdf::loadView('library.pdf.report', [
            'year' => $year,
            'type' => $type,
            'rows' => $rows,
            'user' => $user,
            'selectedDivisionName' => $selectedDivisionName,
        ])->setPaper('a4', 'portrait');

        $filename = "laporan_{$type}_{$year}.pdf";

        $this->saveDownloadHistory($user, $filename, null, null);

        return $pdf->download($filename);
    }
}