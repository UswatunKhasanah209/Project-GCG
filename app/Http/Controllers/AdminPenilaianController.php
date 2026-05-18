<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Aspek;
use App\Models\Indikator;
use App\Models\Parameter;
use App\Models\Fuk;
use App\Models\FukScore;
use App\Models\GcgResult;
use App\Models\LibraryDocument;

class AdminPenilaianController extends Controller
{
    private function authorizeAdmin(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $year = (int) ($request->get('year') ?? now()->year);

        $aspeks = Aspek::orderBy('id')->get();

        $scores = FukScore::where('year', $year)->pluck('score', 'fuk_id');

        foreach ($aspeks as $aspek) {
            $leafFuks = Fuk::whereHas('parameter.indikator', function ($q) use ($aspek) {
                $q->where('aspect_id', $aspek->id);
            })
                ->whereDoesntHave('children')
                ->get();

            $total = $leafFuks->count();
            $filled = $leafFuks->filter(fn($f) => $scores->has($f->id))->count();
            $progress = $total > 0 ? round(($filled / $total) * 100) : 0;

            $leafScores = FukScore::where('year', $year)
                ->whereIn('fuk_id', $leafFuks->pluck('id'))
                ->pluck('score', 'fuk_id')
                ->toArray();

            $aspek->load([
                'indikators.parameters.fuks' => function ($q) {
                    $q->whereNull('parent_id')->with('childrenRecursive');
                }
            ]);

            $displayData = $this->buildAspekDisplayDataRealtime($aspek, $aspek->indikators, $year, $leafScores);
            $aspekRealtime = $displayData['aspek'][$aspek->id] ?? [
                'score' => 0,
                'percent' => 0,
            ];

            $aspek->progress = $progress;
            $aspek->total_fuk = $total;
            $aspek->filled_fuk = $filled;
            $aspek->result_score = (float) $aspekRealtime['score'];
            $aspek->result_percent = (float) $aspekRealtime['percent'];
        }

        return view('penilaian.admin.index', compact('year', 'aspeks'));
    }

    public function showAspek(Request $request, Aspek $aspek)
    {
        $this->authorizeAdmin();

        $year = (int) ($request->get('year') ?? now()->year);

        $aspek->load([
            'indikators.parameters.fuks' => function ($q) {
                $q->whereNull('parent_id')->with('childrenRecursive');
            }
        ]);

        $indikators = $aspek->indikators;

        $existingScores = $this->buildCalculatedScores($indikators, $year);
        $displayData = $this->buildAspekDisplayDataRealtime($aspek, $indikators, $year);

        $allLeafFuks = collect();

        foreach ($indikators as $indikator) {
            foreach ($indikator->parameters as $parameter) {
                foreach ($parameter->fuks as $fuk) {
                    $this->collectLeafFuks($fuk, $allLeafFuks);
                }
            }
        }

        $totalLeaf = $allLeafFuks->count();
        $filledLeaf = $allLeafFuks->filter(function ($fuk) use ($existingScores) {
            return array_key_exists($fuk->id, $existingScores) && $existingScores[$fuk->id] !== null;
        })->count();

        $progress = $totalLeaf > 0 ? round(($filledLeaf / $totalLeaf) * 100) : 0;

        return view('penilaian.admin.aspek_show', compact(
            'year',
            'aspek',
            'indikators',
            'existingScores',
            'displayData',
            'progress'
        ));
    }

    public function showFukForm(Request $request, Fuk $fuk)
    {
        $this->authorizeAdmin();

        $year = (int) ($request->get('year') ?? now()->year);

        if ($fuk->children()->count() > 0) {
            return back()->with('error', 'Hanya FUK leaf yang bisa dinilai.');
        }

        $fuk->load('parameter.indikator.aspect');

        $documents = LibraryDocument::with('division')
            ->where('year', $year)
            ->where('fuk_id', $fuk->id)
            ->latest()
            ->get();

        $score = FukScore::where('year', $year)
            ->where('fuk_id', $fuk->id)
            ->first();

        $allowedScores = $this->allowedScores($fuk->tipe_penilaian);

        $scoreOptions = collect($allowedScores)->map(function ($value) use ($fuk) {
            return [
                'value' => $value,
                'label' => $this->scoreLabel((float) $value, $fuk->tipe_penilaian),
                'percent' => $this->scoreToPercent((float) $value, $fuk->tipe_penilaian),
            ];
        })->values()->all();

        $summary = $this->buildFukSummaryPayloadRealtime($fuk, $year);

        return view('penilaian.admin.fuk_form', compact(
            'year',
            'fuk',
            'documents',
            'score',
            'allowedScores',
            'scoreOptions',
            'summary'
        ));
    }

    public function saveFukReview(Request $request, Fuk $fuk)
    {
        $this->authorizeAdmin();

        if ($fuk->children()->count() > 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya FUK leaf yang bisa dinilai.'
                ], 422);
            }

            return back()->with('error', 'Hanya FUK leaf yang bisa dinilai.');
        }

        $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'score' => ['required', 'numeric'],
            'document_name' => ['nullable', 'string', 'max:255'],
            'page_reference' => ['nullable', 'string', 'max:255'],
            'explanation' => ['nullable', 'string'],
            'assessor_review' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
        ]);

        $scoreValue = (float) $request->score;
        $allowed = $this->allowedScores($fuk->tipe_penilaian);

        if (!in_array($scoreValue, $allowed, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Skor tidak sesuai tipe penilaian.'
                ], 422);
            }

            return back()->with('error', 'Skor tidak sesuai tipe penilaian.');
        }

        FukScore::updateOrCreate(
            [
                'year' => (int) $request->year,
                'fuk_id' => $fuk->id,
            ],
            [
                'score' => $scoreValue,
                'document_name' => $request->document_name,
                'page_reference' => $request->page_reference,
                'explanation' => $request->explanation,
                'assessor_review' => $request->assessor_review,
                'recommendation' => $request->recommendation,
                'scored_by' => Auth::id(),
            ]
        );

        $this->recalculateYear((int) $request->year);

        $payload = $this->buildFukSummaryPayloadRealtime(
            $fuk->load('parameter.indikator.aspect'),
            (int) $request->year
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penilaian berhasil disimpan.',
                'data' => $payload,
            ]);
        }

        return redirect()
            ->route('admin.penilaian.aspek', [
                'aspek' => $fuk->parameter->indikator->aspect_id,
                'year' => $request->year,
            ])
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    private function buildFukSummaryPayloadRealtime(Fuk $fuk, int $year): array
    {
        $parameter = $fuk->parameter;
        $indikator = $parameter->indikator;
        $aspek = $indikator->aspect;

        $aspek->load([
            'indikators.parameters.fuks' => function ($q) {
                $q->whereNull('parent_id')->with('childrenRecursive');
            }
        ]);

        $leafScores = FukScore::where('year', $year)->pluck('score', 'fuk_id')->toArray();
        $displayData = $this->buildAspekDisplayDataRealtime($aspek, $aspek->indikators, $year, $leafScores);

        $fukData = $displayData['fuks'][$fuk->id] ?? [
            'bobot' => 0,
            'score' => null,
            'weighted_score' => 0,
            'percent' => 0,
        ];

        $parameterData = $displayData['parameters'][$parameter->id] ?? [
            'bobot' => 0,
            'score' => 0,
            'percent' => 0,
        ];

        $indikatorData = $displayData['indikators'][$indikator->id] ?? [
            'bobot' => 0,
            'score' => 0,
            'percent' => 0,
        ];

        $aspekData = $displayData['aspek'][$aspek->id] ?? [
            'bobot' => 0,
            'score' => 0,
            'percent' => 0,
        ];

        return [
            'fuk' => [
                'id' => $fuk->id,
                'bobot' => $this->formatNumber($fukData['bobot'], 3),
                'score' => $this->formatNumber($fukData['score'], 3),
                'weighted_score' => $this->formatNumber($fukData['weighted_score'], 3),
                'percent' => $fukData['score'] !== null ? '100' : '0',
            ],
            'parameter' => [
                'id' => $parameter->id,
                'bobot' => $this->formatNumber($parameterData['bobot'], 3),
                'score' => $this->formatNumber($parameterData['score'], 3),
                'percent' => $this->formatNumber($parameterData['percent'], 3),
            ],
            'indikator' => [
                'id' => $indikator->id,
                'bobot' => $this->formatNumber($indikatorData['bobot'], 3),
                'score' => $this->formatNumber($indikatorData['score'], 3),
                'percent' => $this->formatNumber($indikatorData['percent'], 3),
            ],
            'aspek' => [
                'id' => $aspek->id,
                'bobot' => $this->formatNumber($aspekData['bobot'], 3),
                'score' => $this->formatNumber($aspekData['score'], 3),
                'percent' => $this->formatNumber($aspekData['percent'], 3),
            ],
        ];
    }

    private function buildAspekDisplayDataRealtime(Aspek $aspek, $indikators, int $year, ?array $leafScores = null): array
    {
        $leafScores ??= FukScore::where('year', $year)->pluck('score', 'fuk_id')->toArray();

        $data = [
            'aspek' => [],
            'indikators' => [],
            'parameters' => [],
            'fuks' => [],
        ];

        $aspekWeight = $this->resolveEntityWeight(
            $aspek,
            $indikators->sum(function ($indikator) {
                return $this->resolveEntityWeight($indikator);
            })
        );

        $aspekScore = 0.0;

        foreach ($indikators as $indikator) {
            $indikatorWeight = $this->resolveEntityWeight(
                $indikator,
                $indikator->parameters->sum(function ($parameter) {
                    return $this->resolveEntityWeight($parameter);
                })
            );

            $indikatorScore = 0.0;

            foreach ($indikator->parameters as $parameter) {
                $parameterWeight = $this->resolveEntityWeight(
                    $parameter,
                    $parameter->fuks->sum(fn($rootFuk) => $this->normalizeWeight($rootFuk->bobot))
                );

                $parameterScore = 0.0;

                foreach ($parameter->fuks as $rootFuk) {
                    $parameterScore += $this->appendFukDisplayDataRealtime($rootFuk, $leafScores, $data['fuks']);
                }

                $parameterPercent = $parameterWeight > 0
                    ? $this->capPercent(($parameterScore / $parameterWeight) * 100)
                    : 0.0;

                $data['parameters'][$parameter->id] = [
                    'bobot' => $parameterWeight,
                    'score' => $parameterScore,
                    'percent' => $parameterPercent,
                ];

                $indikatorScore += $parameterScore;
            }

            $indikatorPercent = $indikatorWeight > 0
                ? $this->capPercent(($indikatorScore / $indikatorWeight) * 100)
                : 0.0;

            $data['indikators'][$indikator->id] = [
                'bobot' => $indikatorWeight,
                'score' => $indikatorScore,
                'percent' => $indikatorPercent,
            ];

            $aspekScore += $indikatorScore;
        }

        $aspekPercent = $aspekWeight > 0
            ? $this->capPercent(($aspekScore / $aspekWeight) * 100)
            : 0.0;

        $data['aspek'][$aspek->id] = [
            'bobot' => $aspekWeight,
            'score' => $aspekScore,
            'percent' => $aspekPercent,
        ];

        return $data;
    }

    private function appendFukDisplayDataRealtime(Fuk $fuk, array $leafScores, array &$target): float
    {
        $children = $fuk->childrenRecursive ?? collect();
        $isLeaf = $children->count() === 0;
        $weight = $this->normalizeWeight($fuk->bobot);

        if ($isLeaf) {
            $leafScore = array_key_exists($fuk->id, $leafScores) ? (float) $leafScores[$fuk->id] : null;
            $weightedScore = $leafScore !== null ? ($weight * $leafScore) : 0.0;

            $target[$fuk->id] = [
                'bobot' => $weight,
                'score' => $leafScore,
                'weighted_score' => $weightedScore,
                'percent' => $leafScore !== null ? 100.0 : 0.0,
            ];

            return $weightedScore;
        }

        $childrenWeightedSum = 0.0;

        foreach ($children as $child) {
            $childrenWeightedSum += $this->appendFukDisplayDataRealtime($child, $leafScores, $target);
        }

        $target[$fuk->id] = [
            'bobot' => $weight,
            'score' => $weight > 0 ? ($childrenWeightedSum / $weight) : 0.0,
            'weighted_score' => $childrenWeightedSum,
            'percent' => $weight > 0
                ? $this->capPercent(($childrenWeightedSum / $weight) * 100)
                : 0.0,
        ];

        return $childrenWeightedSum;
    }

    private function allowedScores(?string $type): array
    {
        $t = strtolower(trim((string) $type));

        return match ($t) {
            'skala_0_1', '0,1', '0_1', '' => [0.0, 1.0],
            'skala_-1_0', '-1,0', '-1_0' => [-1.0, 0.0],
            'skala_3', 'skala3', '0,0.5,1', '0_05_1' => [0.0, 0.5, 1.0],
            'skala_5', '0,0.25,0.5,0.75,1', '0_025_05_075_1' => [0.0, 0.25, 0.5, 0.75, 1.0],
            'skala_0_2' => [0.0, 1.0, 2.0],
            'skala_0_3' => [0.0, 1.0, 2.0, 3.0],
            default => [0.0, 1.0],
        };
    }

    private function scoreLabel(float $value, ?string $type): string
    {
        $percent = $this->scoreToPercent($value, $type);

        $t = strtolower(trim((string) $type));

        return match ($t) {
            'skala_0_2', 'skala_0_3' =>
                $this->formatNumber($value, 3) . ' (' . $this->formatNumber($percent, 3) . '%)',

            default =>
                $this->formatNumber($percent, 3) . '%',
        };
    }

    private function scoreToPercent(float $value, ?string $type): float
    {
        $t = strtolower(trim((string) $type));

        return match ($t) {
            'skala_0_1', '0,1', '0_1', '' => $value * 100,
            'skala_-1_0', '-1,0', '-1_0' => $value < 0 ? -100 : 0,
            'skala_3', 'skala3', '0,0.5,1', '0_05_1' => $value * 100,
            'skala_5', '0,0.25,0.5,0.75,1', '0_025_05_075_1' => $value * 100,
            'skala_0_2' => ($value / 2) * 100,
            'skala_0_3' => ($value / 3) * 100,
            default => $value * 100,
        };
    }

    private function buildDisplayPercent($weightedScore, float $weight): ?float
    {
        if ($weightedScore === null || $weight <= 0) {
            return null;
        }

        return $this->capPercent((((float) $weightedScore / $weight) * 100));
    }

    private function capPercent(float $percent): float
    {
        return max(0, min(100, $percent));
    }

    private function recalculateYear(int $year): void
    {
        DB::transaction(function () use ($year) {
            $allFuks = Fuk::with('children')->get()->keyBy('id');
            $allParameters = Parameter::all()->keyBy('id');
            $allIndikators = Indikator::all()->keyBy('id');
            $allAspeks = Aspek::all()->keyBy('id');

            $leafInputScores = FukScore::where('year', $year)
                ->pluck('score', 'fuk_id')
                ->toArray();

            GcgResult::where('year', $year)->delete();

            $memoWeightedScore = [];
            $memoFulfillment = [];

            $computeFuk = function (string $fukId) use (
                &$computeFuk,
                &$memoWeightedScore,
                &$memoFulfillment,
                $allFuks,
                $leafInputScores
            ) {
                if (array_key_exists($fukId, $memoWeightedScore)) {
                    return $memoWeightedScore[$fukId];
                }

                $fuk = $allFuks->get($fukId);

                if (!$fuk) {
                    $memoWeightedScore[$fukId] = null;
                    $memoFulfillment[$fukId] = null;
                    return null;
                }

                $children = $allFuks->where('parent_id', $fukId)->values();
                $bobot = $this->normalizeWeight($fuk->bobot);

                if ($children->count() === 0) {
                    $inputScore = $leafInputScores[$fukId] ?? null;

                    if ($inputScore === null) {
                        $memoWeightedScore[$fukId] = null;
                        $memoFulfillment[$fukId] = null;
                        return null;
                    }

                    $weightedScore = $bobot * (float) $inputScore;

                    $memoWeightedScore[$fukId] = $weightedScore;
                    $memoFulfillment[$fukId] = (float) $inputScore;

                    return $weightedScore;
                }

                $childrenWeightedSum = 0.0;

                foreach ($children as $child) {
                    $childWeighted = $computeFuk($child->id);

                    if ($childWeighted === null) {
                        $memoWeightedScore[$fukId] = null;
                        $memoFulfillment[$fukId] = null;
                        return null;
                    }

                    $childrenWeightedSum += $childWeighted;
                }

                $memoWeightedScore[$fukId] = $childrenWeightedSum;
                $memoFulfillment[$fukId] = $bobot > 0 ? ($childrenWeightedSum / $bobot) : null;

                return $childrenWeightedSum;
            };

            foreach ($allFuks as $fuk) {
                $computeFuk($fuk->id);
            }

            foreach ($allFuks as $fuk) {
                GcgResult::create([
                    'year' => $year,
                    'level' => GcgResult::LEVEL_FUK,
                    'entity_id' => $fuk->id,
                    'score' => $memoWeightedScore[$fuk->id] ?? null,
                ]);
            }

            $parameterScores = [];

            foreach ($allParameters as $parameter) {
                $rootFuks = $allFuks
                    ->where('parameter_id', $parameter->id)
                    ->whereNull('parent_id')
                    ->values();

                if ($rootFuks->count() === 0) {
                    $parameterScores[$parameter->id] = null;
                    continue;
                }

                $sumWeighted = 0.0;

                foreach ($rootFuks as $rootFuk) {
                    $fukWeighted = $memoWeightedScore[$rootFuk->id] ?? null;

                    if ($fukWeighted === null) {
                        $parameterScores[$parameter->id] = null;
                        continue 2;
                    }

                    $sumWeighted += $fukWeighted;
                }

                $parameterScores[$parameter->id] = $sumWeighted;

                GcgResult::create([
                    'year' => $year,
                    'level' => GcgResult::LEVEL_PARAMETER,
                    'entity_id' => $parameter->id,
                    'score' => $sumWeighted,
                ]);
            }

            $indikatorScores = [];

            foreach ($allIndikators as $indikator) {
                $parameters = $allParameters
                    ->where('indikator_id', $indikator->id)
                    ->values();

                if ($parameters->count() === 0) {
                    $indikatorScores[$indikator->id] = null;
                    continue;
                }

                $sumWeighted = 0.0;

                foreach ($parameters as $parameter) {
                    $parameterWeighted = $parameterScores[$parameter->id] ?? null;

                    if ($parameterWeighted === null) {
                        $indikatorScores[$indikator->id] = null;
                        continue 2;
                    }

                    $sumWeighted += $parameterWeighted;
                }

                $indikatorScores[$indikator->id] = $sumWeighted;

                GcgResult::create([
                    'year' => $year,
                    'level' => GcgResult::LEVEL_INDIKATOR,
                    'entity_id' => $indikator->id,
                    'score' => $sumWeighted,
                ]);
            }

            foreach ($allAspeks as $aspek) {
                $indikators = $allIndikators
                    ->where('aspect_id', $aspek->id)
                    ->values();

                if ($indikators->count() === 0) {
                    continue;
                }

                $sumWeighted = 0.0;

                foreach ($indikators as $indikator) {
                    $indikatorWeighted = $indikatorScores[$indikator->id] ?? null;

                    if ($indikatorWeighted === null) {
                        continue 2;
                    }

                    $sumWeighted += $indikatorWeighted;
                }

                GcgResult::create([
                    'year' => $year,
                    'level' => GcgResult::LEVEL_ASPEK,
                    'entity_id' => $aspek->id,
                    'score' => $sumWeighted,
                ]);
            }
        });
    }

    private function resolveFukScore(Fuk $fuk, array $leafScores, array &$calculatedScores): ?float
    {
        if (array_key_exists($fuk->id, $calculatedScores)) {
            return $calculatedScores[$fuk->id];
        }

        $children = $fuk->childrenRecursive ?? collect();

        if ($children->count() === 0) {
            $score = isset($leafScores[$fuk->id]) ? (float) $leafScores[$fuk->id] : null;
            $calculatedScores[$fuk->id] = $score;
            return $score;
        }

        $parentWeight = $this->normalizeWeight($fuk->bobot);
        $childrenWeightedSum = 0.0;

        foreach ($children as $child) {
            $childFulfillment = $this->resolveFukScore($child, $leafScores, $calculatedScores);

            if ($childFulfillment === null) {
                $calculatedScores[$fuk->id] = null;
                return null;
            }

            $childWeight = $this->normalizeWeight($child->bobot);
            $childrenWeightedSum += ($childFulfillment * $childWeight);
        }

        $score = $parentWeight > 0 ? ($childrenWeightedSum / $parentWeight) : null;

        $calculatedScores[$fuk->id] = $score;

        return $score;
    }

    private function buildCalculatedScores($indikators, int $year): array
    {
        $allFukIds = [];

        foreach ($indikators as $indikator) {
            foreach ($indikator->parameters as $parameter) {
                foreach ($parameter->fuks as $fuk) {
                    $allFukIds[] = $fuk->id;
                    $this->collectChildFukIds($fuk, $allFukIds);
                }
            }
        }

        $allFukIds = array_values(array_unique($allFukIds));

        $leafScores = FukScore::where('year', $year)
            ->whereIn('fuk_id', $allFukIds)
            ->pluck('score', 'fuk_id')
            ->toArray();

        $calculatedScores = [];

        foreach ($indikators as $indikator) {
            foreach ($indikator->parameters as $parameter) {
                foreach ($parameter->fuks as $fuk) {
                    $this->resolveFukScore($fuk, $leafScores, $calculatedScores);
                }
            }
        }

        return $calculatedScores;
    }

    private function collectChildFukIds(Fuk $fuk, array &$ids): void
    {
        $children = $fuk->childrenRecursive ?? collect();

        foreach ($children as $child) {
            $ids[] = $child->id;
            $this->collectChildFukIds($child, $ids);
        }
    }

    private function collectLeafFuks(Fuk $fuk, Collection &$leafs): void
    {
        $children = $fuk->childrenRecursive ?? collect();

        if ($children->count() === 0) {
            $leafs->push($fuk);
            return;
        }

        foreach ($children as $child) {
            $this->collectLeafFuks($child, $leafs);
        }
    }

    private function normalizeWeight($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', (string) $value);
    }

    private function resolveEntityWeight(object $entity, float $fallback = 0.0): float
    {
        if (isset($entity->bobot) && $entity->bobot !== null && $entity->bobot !== '') {
            return $this->normalizeWeight($entity->bobot);
        }

        return $fallback;
    }

    private function formatNumber($value, int $decimals = 3): ?string
    {
        if ($value === null) {
            return null;
        }

        return rtrim(rtrim(number_format((float) $value, $decimals, '.', ''), '0'), '.');
    }
}