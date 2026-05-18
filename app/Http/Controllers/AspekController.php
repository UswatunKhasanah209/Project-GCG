<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Aspek;
use App\Models\Division;
use App\Models\Fuk;
use App\Models\LibraryDocument;
use App\Models\Indikator;
use App\Models\Parameter;

class AspekController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EXISTING: /aspects (ADMIN VIEW DIVISIONS)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ==========================
        // ADMIN VIEW: tampil DIVISI
        // ==========================
        if ($user && $user->role === 'admin') {

            $year = (int) ($request->get('year') ?? now()->year);

            $divisions = Division::orderBy('id')->get();

            // Ambil semua fuk + required_docs
            $fuks = Fuk::select('id', 'required_docs')->get();

            // Count dokumen per divisi per fuk (tahun terpilih)
            $docCounts = DB::table('library_documents')
                ->select('division_id', 'fuk_id', DB::raw('COUNT(*) as cnt'))
                ->where('year', $year)
                ->groupBy('division_id', 'fuk_id')
                ->get();

            // Map: counts[division_id][fuk_id] = cnt
            $counts = [];
            foreach ($docCounts as $row) {
                $counts[$row->division_id][$row->fuk_id] = (int) $row->cnt;
            }

            $divisionStatus = [];

            foreach ($divisions as $div) {
                $totalUploadedDocs = 0;
                $missingDocs = 0;

                foreach ($fuks as $fuk) {
                    $uploaded = $counts[$div->id][$fuk->id] ?? 0;
                    $totalUploadedDocs += $uploaded;

                    $req = max((int)($fuk->required_docs ?? 1), 1);
                    $missingDocs += max($req - $uploaded, 0);
                }

                [$status, $tooltip] = $this->statusFromCounts($totalUploadedDocs, $missingDocs);

                $divisionStatus[$div->id] = [
                    'status' => $status,
                    'tooltip' => $tooltip,
                    'missingDocs' => $missingDocs,
                    'uploadedDocs' => $totalUploadedDocs,
                ];
            }

            return view('aspects.admin_divisions', compact(
                'year',
                'divisions',
                'divisionStatus'
            ));
        }

        // ==========================
        // USER VIEW (redirect ke userIndex)
        // ==========================
        return redirect()->route('aspects.user');
    }

    /*
    |--------------------------------------------------------------------------
    | EXISTING: /admin/aspects/divisions/{division}
    |--------------------------------------------------------------------------
    */
    public function divisionDetail(Request $request, Division $division)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $year = (int) ($request->get('year') ?? now()->year);

        // ambil semua FUK + relasi sampai aspek
        $fuks = Fuk::with(['parameter.indikator.aspect'])
            ->orderBy('id')
            ->get(['id', 'name', 'parameter_id', 'required_docs']);

        // count dokumen per fuk untuk divisi & tahun ini
        $docCounts = DB::table('library_documents')
            ->select('fuk_id', DB::raw('COUNT(*) as cnt'))
            ->where('year', $year)
            ->where('division_id', $division->id)
            ->groupBy('fuk_id')
            ->pluck('cnt', 'fuk_id')
            ->toArray();

        // list dokumen per fuk (biar bisa ditampilkan)
        $docsByFuk = LibraryDocument::where('year', $year)
            ->where('division_id', $division->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('fuk_id');

        // build rows detail
        $rows = [];
        $summary = [
            'uploadedDocs' => 0,
            'missingDocs' => 0,
            'totalFuk' => $fuks->count(),
            'missingFuk' => 0,
        ];

        foreach ($fuks as $fuk) {
            $uploaded = (int) ($docCounts[$fuk->id] ?? 0);
            $req = max((int)($fuk->required_docs ?? 1), 1);
            $missing = max($req - $uploaded, 0);

            $summary['uploadedDocs'] += $uploaded;
            $summary['missingDocs'] += $missing;
            if ($missing > 0) $summary['missingFuk']++;

            [$status, $tooltip] = $this->statusFromCounts($uploaded, $missing);

            $aspek = optional(optional(optional($fuk->parameter)->indikator)->aspect);
            $indikator = optional(optional($fuk->parameter)->indikator);
            $parameter = optional($fuk->parameter);

            $rows[] = [
                'fuk' => $fuk,
                'aspek' => $aspek,
                'indikator' => $indikator,
                'parameter' => $parameter,
                'required' => $req,
                'uploaded' => $uploaded,
                'missing' => $missing,
                'status' => $status,
                'tooltip' => $tooltip,
                'docs' => $docsByFuk[$fuk->id] ?? collect(),
            ];
        }

        // default: tampilkan yang kurang dulu
        $show = $request->get('show', 'missing'); // missing | all
        if ($show === 'missing') {
            $rows = array_values(array_filter($rows, fn($r) => $r['missing'] > 0));
        }

        return view('aspects.admin_division_detail', compact(
            'year',
            'division',
            'rows',
            'summary',
            'show'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXISTING: /user/aspects (USER TREE)
    |--------------------------------------------------------------------------
    */
    public function userIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        $divisionId = $user->division_id;

        if (!$divisionId) {
            return view('aspects.user_index', [
                'year' => (int)($request->get('year') ?? now()->year),
                'aspeks' => collect(),
                'tree' => [],
                'statusMap' => [],
                'indikators' => collect(),
                'parameters' => collect(),
                'indikatorAgg' => [],
                'aspekAgg' => [],
                'message' => 'User belum punya division_id. Set dulu division_id user.'
            ]);
        }

        $year = (int)($request->get('year') ?? now()->year);

        // ambil data master hierarchy
        $aspeks = Aspek::orderBy('id')->get();
        $indikators = Indikator::orderBy('id')->get();
        $parameters = Parameter::orderBy('id')->get();

        // Load FUK tree per parameter (5 level)
        $rootFuksByParameter = Fuk::whereNull('parent_id')
            ->with('children.children.children.children.children')
            ->orderBy('id')
            ->get()
            ->groupBy('parameter_id');

        // Count dokumen user divisi per fuk untuk tahun ini
        $docCounts = DB::table('library_documents')
            ->select('fuk_id', DB::raw('COUNT(*) as cnt'))
            ->where('year', $year)
            ->where('division_id', $divisionId)
            ->groupBy('fuk_id')
            ->pluck('cnt', 'fuk_id')
            ->toArray();

        // Semua fuk untuk status (required_docs)
        $allFuks = Fuk::select('id', 'required_docs', 'parameter_id', 'parent_id')->get();

        $statusMap = [];
        foreach ($allFuks as $f) {
            $uploaded = (int) ($docCounts[$f->id] ?? 0);
            $req = max((int)($f->required_docs ?? 1), 1);
            $missing = max($req - $uploaded, 0);

            [$status, $tooltip] = $this->statusFromCounts($uploaded, $missing);

            $statusMap[$f->id] = [
                'uploaded' => $uploaded,
                'required' => $req,
                'missing' => $missing,
                'status' => $status,
                'tooltip' => $tooltip,
            ];
        }

        $aggregateStatus = function (array $fukIds) use ($statusMap) {
            $totalUploaded = 0;
            $totalMissing = 0;

            foreach ($fukIds as $id) {
                $totalUploaded += $statusMap[$id]['uploaded'] ?? 0;
                $totalMissing += $statusMap[$id]['missing'] ?? 0;
            }

            return array_merge(
                ['uploaded' => $totalUploaded, 'missing' => $totalMissing],
                ['status' => $this->statusFromCounts($totalUploaded, $totalMissing)[0]],
                ['tooltip' => $this->statusFromCounts($totalUploaded, $totalMissing)[1]]
            );
        };

        // build tree
        $tree = [];
        $indikatorById = $indikators->keyBy('id');

        $childrenMap = [];
        foreach ($allFuks as $f) {
            $key = $f->parent_id ?? 'root';
            $childrenMap[$key][] = $f->id;
        }

        $collectDescendants = function ($parentId) use (&$collectDescendants, $childrenMap) {
            $ids = $childrenMap[$parentId] ?? [];
            $all = [];
            foreach ($ids as $cid) {
                $all[] = $cid;
                $all = array_merge($all, $collectDescendants($cid));
            }
            return $all;
        };

        foreach ($parameters as $p) {
            $indikator = $indikatorById[$p->indikator_id] ?? null;
            if (!$indikator) continue;

            $aspekId = $indikator->aspect_id;
            $indikatorId = $indikator->id;
            $parameterId = $p->id;

            $rootForParam = ($rootFuksByParameter[$parameterId] ?? collect());

            $fukIds = [];
            foreach ($rootForParam as $rf) {
                $fukIds[] = $rf->id;
                $fukIds = array_merge($fukIds, $collectDescendants($rf->id));
            }

            $tree[$aspekId][$indikatorId][$parameterId] = [
                'parameter' => $p,
                'rootFuks' => $rootForParam,
                'fukIds' => $fukIds,
                'statusAgg' => $aggregateStatus($fukIds),
            ];
        }

        $indikatorAgg = [];
        $aspekAgg = [];

        foreach ($tree as $aspekId => $indikatorGroup) {
            $aspekFukIds = [];

            foreach ($indikatorGroup as $indikatorId => $paramGroup) {
                $indikatorFukIds = [];
                foreach ($paramGroup as $obj) {
                    $indikatorFukIds = array_merge($indikatorFukIds, $obj['fukIds']);
                }
                $indikatorAgg[$indikatorId] = $aggregateStatus(array_unique($indikatorFukIds));
                $aspekFukIds = array_merge($aspekFukIds, $indikatorFukIds);
            }

            $aspekAgg[$aspekId] = $aggregateStatus(array_unique($aspekFukIds));
        }

        return view('aspects.user_index', compact(
            'year',
            'aspeks',
            'indikators',
            'parameters',
            'tree',
            'statusMap',
            'indikatorAgg',
            'aspekAgg'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | NEW: HALAMAN ASPEK GCG GRID (gambar 23)
    | URL: /aspek-gcg
    |--------------------------------------------------------------------------
    */
    public function gcgIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        $year = (int) ($request->get('year') ?? now()->year);

        // admin bisa pilih divisi untuk lihat progress divisi tertentu
        $divisionId = null;
        if ($user->role === 'admin' && $request->filled('division_id')) {
            $divisionId = (int) $request->division_id;
        }

        $aspeks = Aspek::orderBy('id')->get();
        $divisions = $user->role === 'admin'
            ? Division::orderBy('id')->get()
            : collect();

        $progressMap = [];
        foreach ($aspeks as $a) {
            [$complete, $total] = $this->calcAspectProgress($a->id, $year, $user, $divisionId);
            $progressMap[$a->id] = [
                'complete' => $complete,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(($complete / $total) * 100) : 0,
            ];
        }

        return view('aspek_gcg.index', compact(
            'year',
            'aspeks',
            'divisions',
            'divisionId',
            'progressMap'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | NEW: DETAIL ASPEK (gambar 24 user / 25 admin)
    | URL: /aspek-gcg/{aspek}
    |--------------------------------------------------------------------------
    */
    public function gcgShow(Request $request, Aspek $aspek)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        $year = (int) ($request->get('year') ?? now()->year);

        $divisionId = null;
        if ($user->role === 'admin') {
            // admin memilih divisi di dropdown
            if ($request->filled('division_id')) {
                $divisionId = (int) $request->division_id;
            }
        } else {
            // user pakai divisinya sendiri
            $divisionId = (int) $user->division_id;
        }

        // Ambil LEAF FUK untuk aspek ini
        $leafFuks = Fuk::with(['parameter.indikator'])
            ->withCount('children')
            ->whereHas('parameter.indikator', function ($q) use ($aspek) {
                $q->where('aspect_id', $aspek->id);
            })
            ->orderBy('id')
            ->get()
            ->filter(fn($f) => (int)$f->children_count === 0)
            ->values();

        // ambil dokumen per fuk (tahun + division)
        $docCounts = DB::table('library_documents')
            ->select('fuk_id', DB::raw('COUNT(*) as cnt'))
            ->where('year', $year)
            ->where('division_id', $divisionId)
            ->whereIn('fuk_id', $leafFuks->pluck('id'))
            ->groupBy('fuk_id')
            ->pluck('cnt', 'fuk_id')
            ->toArray();

        $completeCount = 0;
        $statusMap = [];

        foreach ($leafFuks as $f) {
            $uploaded = (int) ($docCounts[$f->id] ?? 0);
            $required = max((int)($f->required_docs ?? 1), 1);
            $missing = max($required - $uploaded, 0);

            // status sesuai definisi kamu:
            // hitam: 0 dokumen
            // merah: missing > 1
            // kuning: missing = 1
            // hijau: missing = 0
            $status = 'black';
            $tooltip = 'Dokumen belum ada / kosong';

            if ($uploaded > 0) {
                if ($missing === 0) {
                    $status = 'green';
                    $tooltip = 'Complete';
                    $completeCount++;
                } elseif ($missing === 1) {
                    $status = 'yellow';
                    $tooltip = 'Missing 1';
                } else {
                    $status = 'red';
                    $tooltip = 'Incomplete';
                }
            }

            $statusMap[$f->id] = [
                'uploaded' => $uploaded,
                'required' => $required,
                'missing' => $missing,
                'status' => $status,
                'tooltip' => $tooltip,
            ];
        }

        $total = $leafFuks->count();
        $percent = $total > 0 ? (int) round(($completeCount / $total) * 100) : 0;

        $divisions = $user->role === 'admin'
            ? Division::orderBy('id')->get()
            : collect();

        return view('aspek_gcg.show', compact(
            'aspek',
            'year',
            'percent',
            'leafFuks',
            'statusMap',
            'divisions',
            'divisionId'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: hitung progress aspek (complete leaf / total leaf)
    |--------------------------------------------------------------------------
    */
    private function calcAspectProgress($aspekId, int $year, $user, $divisionId = null): array
    {
        // leaf fuk ids per aspek
        $leafFuks = Fuk::withCount('children')
            ->whereHas('parameter.indikator', function ($q) use ($aspekId) {
                $q->where('aspect_id', (string) $aspekId);
            })
            ->get()
            ->filter(fn($f) => (int)$f->children_count === 0);

        $total = $leafFuks->count();
        if ($total === 0) return [0, 0];

        // division target:
        // admin: pakai divisionId (kalau null → 0 progress karena admin belum pilih divisi)
        // user: pakai division user
        if ($user->role === 'admin') {
            if (!$divisionId) {
                // kalau admin belum pilih divisi, kita tampilkan 0% biar tidak misleading
                return [0, $total];
            }
            $targetDivisionId = (int)$divisionId;
        } else {
            $targetDivisionId = (int)$user->division_id;
        }

        $docCounts = DB::table('library_documents')
            ->select('fuk_id', DB::raw('COUNT(*) as cnt'))
            ->where('year', $year)
            ->where('division_id', $targetDivisionId)
            ->whereIn('fuk_id', $leafFuks->pluck('id'))
            ->groupBy('fuk_id')
            ->pluck('cnt', 'fuk_id')
            ->toArray();

        $complete = 0;

        foreach ($leafFuks as $f) {
            $uploaded = (int) ($docCounts[$f->id] ?? 0);
            $required = max((int)($f->required_docs ?? 1), 1);
            if ($uploaded >= $required) $complete++;
        }

        return [$complete, $total];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: status warna + tooltip dari uploaded/missing
    |--------------------------------------------------------------------------
    */
    private function statusFromCounts(int $uploaded, int $missing): array
    {
        $status = 'black';
        $tooltip = 'Dokumen belum ada / kosong';

        if ($uploaded > 0) {
            if ($missing === 0) {
                $status = 'green';
                $tooltip = 'Dokumen lengkap';
            } elseif ($missing === 1) {
                $status = 'yellow';
                $tooltip = 'Dokumen kurang 1';
            } else {
                $status = 'red';
                $tooltip = 'Dokumen kurang ' . $missing;
            }
        }

        return [$status, $tooltip];
    }
}
