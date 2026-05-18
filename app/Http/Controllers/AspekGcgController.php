<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Models\Indikator;
use App\Models\Parameter;
use App\Models\Fuk;
use App\Models\FukStatus;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AspekGcgController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $year = (int) ($request->get('year') ?? now()->year);

        $aspeks = Aspek::orderBy('id')->get();

        $aspekCards = [];
        foreach ($aspeks as $idx => $aspek) {
            $roman = $this->romanByIndex($idx + 1);
            $img = "images/Aspek {$roman}.png";

            $aspekCards[] = [
                'aspek' => $aspek,
                'roman' => $roman,
                'img' => $img,
            ];
        }

        return view('aspects.gcg.index', compact(
            'year',
            'aspekCards'
        ));
    }

    public function show(Request $request, Aspek $aspek)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $year = (int) ($request->get('year') ?? now()->year);
        $roman = $this->romanForAspekId($aspek->id);

        // =========================
        // DIVISI
        // =========================
        $divisions = collect();
        $divisionId = null;

        if ($user->role === 'admin') {
            $divisions = Division::orderBy('id')->get();
            $divisionId = $request->filled('division_id')
                ? (int) $request->division_id
                : (int) optional($divisions->first())->id;
        } else {
            $divisionId = (int) $user->division_id;
        }

        // =========================
        // INDIKATOR
        // =========================
        $indikators = Indikator::where('aspect_id', (string) $aspek->id)
            ->orderBy('id')
            ->get();

        $indikatorId = $request->get('indikator_id');
        if ($indikatorId && !$indikators->firstWhere('id', $indikatorId)) {
            $indikatorId = null;
        }

        // =========================
        // PARAMETER
        // =========================
        $parameters = collect();
        $parameterId = $request->get('parameter_id');

        if ($indikatorId) {
            $parameters = Parameter::where('indikator_id', $indikatorId)
                ->orderBy('id')
                ->get();

            if ($parameterId && !$parameters->firstWhere('id', $parameterId)) {
                $parameterId = null;
            }
        }

        // =========================
        // FUK
        // =========================
        $fuks = collect();
        $fukId = $request->get('fuk_id');

        if ($parameterId) {
            $fuks = Fuk::where('parameter_id', $parameterId)
                ->orderBy('id')
                ->get();

            if ($fukId && !$fuks->firstWhere('id', $fukId)) {
                $fukId = null;
            }
        }

        // =========================
        // HASIL: tampil hanya kalau FUK sudah dipilih
        // =========================
        $hasSearch = !empty($fukId);

        $selectedIndikator = $indikatorId ? $indikators->firstWhere('id', $indikatorId) : null;
        $selectedParameter = $parameterId ? $parameters->firstWhere('id', $parameterId) : null;
        $selectedFuk = $fukId ? $fuks->firstWhere('id', $fukId) : null;

        $result = null;

        if ($hasSearch && $selectedFuk) {
            [$fukStatus, $fukTooltip, $docCount, $required, $missing] = $this->calcFukStatus(
                $selectedFuk->id,
                $year,
                (int) $divisionId
            );

            $result = [
                'indikator' => $selectedIndikator,
                'parameter' => $selectedParameter,
                'fuk' => $selectedFuk,
                'status' => $fukStatus,
                'tooltip' => $fukTooltip,
                'uploaded' => $docCount,
                'required' => $required,
                'missing' => $missing,
            ];
        }

        return view('aspects.gcg.show', compact(
            'aspek',
            'roman',
            'year',
            'divisions',
            'divisionId',
            'indikators',
            'indikatorId',
            'parameters',
            'parameterId',
            'fuks',
            'fukId',
            'hasSearch',
            'result'
        ));
    }

    public function updateFukStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $data = $request->validate([
            'division_id' => ['required', 'integer'],
            'fuk_id' => ['required'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'status' => ['required', 'in:black,red,yellow,green'],
            'note' => ['nullable', 'string'],
            'redirect' => ['nullable', 'string'],
        ]);

        FukStatus::updateOrCreate(
            [
                'division_id' => (int) $data['division_id'],
                'fuk_id' => $data['fuk_id'],
                'year' => (int) $data['year'],
            ],
            [
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'updated_by' => $user->id,
            ]
        );

        return redirect($data['redirect'] ?: url()->previous())
            ->with('success', 'Status FUK berhasil diperbarui.');
    }

    private function calcFukStatus($fukId, int $year, int $divisionId): array
    {
        $override = FukStatus::where('division_id', $divisionId)
            ->where('fuk_id', $fukId)
            ->where('year', $year)
            ->first();

        $docCount = (int) DB::table('library_documents')
            ->where('year', $year)
            ->where('division_id', $divisionId)
            ->where('fuk_id', $fukId)
            ->count();

        $fuk = Fuk::find($fukId);
        $required = max((int) ($fuk->required_docs ?? 1), 1);
        $missing = max($required - $docCount, 0);

        if ($override) {
            return [
                $override->status,
                $override->note ?: $this->tooltipByStatus($override->status),
                $docCount,
                $required,
                $missing,
            ];
        }

        if ($docCount === 0) {
            return ['black', 'Incomplete (Dokumen belum ada / kosong)', $docCount, $required, $missing];
        }
        if ($missing === 0) {
            return ['green', 'Complete', $docCount, $required, $missing];
        }
        if ($missing === 1) {
            return ['yellow', 'Missing 1', $docCount, $required, $missing];
        }

        return ['red', 'In Complete', $docCount, $required, $missing];
    }

    private function romanByIndex(int $n): string
    {
        return match ($n) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            default => (string) $n
        };
    }

    private function romanForAspekId($aspekId): string
    {
        $ids = Aspek::orderBy('id')->pluck('id')->values();
        $index = $ids->search($aspekId);

        if ($index === false) {
            return (string) $aspekId;
        }

        return $this->romanByIndex($index + 1);
    }

    private function tooltipByStatus(string $st): string
    {
        return match ($st) {
            'green' => 'Complete',
            'yellow' => 'Missing 1',
            'red' => 'In Complete',
            default => 'Incomplete (Dokumen belum ada / kosong)',
        };
    }
}