<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aspek;
use App\Models\Fuk;
use App\Models\FukScore;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);

        $aspeks = Aspek::with([
            'indikators.parameters.fuks' => function ($q) {
                $q->whereNull('parent_id')->with('childrenRecursive');
            }
        ])->orderBy('id')->get();

        $leafScoresSelectedYear = FukScore::where('year', $year)
            ->pluck('score', 'fuk_id')
            ->toArray();

        $bigChartData = [];
        $bigChartLabels = [];
        $smallCharts = [];

        $years = FukScore::select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (empty($years)) {
            $years = [$year];
        }

        if (!in_array($year, $years, true)) {
            $years[] = $year;
            sort($years);
        }

        foreach ($aspeks as $aspek) {
            $displayDataSelectedYear = $this->buildAspekDisplayDataRealtime(
                $aspek,
                $aspek->indikators,
                $year,
                $leafScoresSelectedYear
            );

            $aspekRealtime = $displayDataSelectedYear['aspek'][$aspek->id] ?? [
                'score' => 0,
                'percent' => 0,
            ];

            $bigChartData[] = round((float) $aspekRealtime['score'], 4);
            $bigChartLabels[] = $this->aspectLabel($aspek);

            $allLeafFuks = collect();
            foreach ($aspek->indikators as $indikator) {
                foreach ($indikator->parameters as $parameter) {
                    foreach ($parameter->fuks as $fuk) {
                        $this->collectLeafFuks($fuk, $allLeafFuks);
                    }
                }
            }

            $totalLeaf = $allLeafFuks->count();
            $filledLeaf = $allLeafFuks->filter(function ($fuk) use ($leafScoresSelectedYear) {
                return array_key_exists($fuk->id, $leafScoresSelectedYear)
                    && $leafScoresSelectedYear[$fuk->id] !== null;
            })->count();

            $progress = $totalLeaf > 0 ? round(($filledLeaf / $totalLeaf) * 100) : 0;

            $trendScores = [];
            foreach ($years as $y) {
                $leafScoresByYear = FukScore::where('year', $y)
                    ->pluck('score', 'fuk_id')
                    ->toArray();

                $displayDataByYear = $this->buildAspekDisplayDataRealtime(
                    $aspek,
                    $aspek->indikators,
                    (int) $y,
                    $leafScoresByYear
                );

                $trendAspek = $displayDataByYear['aspek'][$aspek->id] ?? [
                    'score' => 0,
                    'percent' => 0,
                ];

                $trendScores[] = round((float) $trendAspek['score'], 4);
            }

            $smallCharts[$aspek->id] = [
                'years' => $years,
                'scores' => $trendScores,
                'label' => $this->aspectLabel($aspek),
                'name' => $aspek->name ?? '-',
                'progress' => $progress,
            ];
        }

        $leftAspeks = $aspeks->take(3)->values();
        $rightAspeks = $aspeks->slice(3, 3)->values();

        return view('dashboard.index', compact(
            'year',
            'aspeks',
            'leftAspeks',
            'rightAspeks',
            'bigChartData',
            'bigChartLabels',
            'smallCharts'
        ));
    }

    private function aspectLabel($aspek): string
    {
        if (!empty($aspek->display_name)) {
            return strtoupper($aspek->display_name);
        }

        return 'ASPEK ' . $aspek->id;
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
                    ? (($parameterScore / $parameterWeight) * 100)
                    : 0.0;

                $data['parameters'][$parameter->id] = [
                    'bobot' => $parameterWeight,
                    'score' => $parameterScore,
                    'percent' => $parameterPercent,
                ];

                $indikatorScore += $parameterScore;
            }

            $indikatorPercent = $indikatorWeight > 0
                ? (($indikatorScore / $indikatorWeight) * 100)
                : 0.0;

            $data['indikators'][$indikator->id] = [
                'bobot' => $indikatorWeight,
                'score' => $indikatorScore,
                'percent' => $indikatorPercent,
            ];

            $aspekScore += $indikatorScore;
        }

        $aspekPercent = $aspekWeight > 0
            ? (($aspekScore / $aspekWeight) * 100)
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
            $leafScore = array_key_exists($fuk->id, $leafScores)
                ? (float) $leafScores[$fuk->id]
                : null;

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
            'percent' => $weight > 0 ? (($childrenWeightedSum / $weight) * 100) : 0.0,
        ];

        return $childrenWeightedSum;
    }

    private function collectLeafFuks(Fuk $fuk, \Illuminate\Support\Collection &$leafs): void
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
}
