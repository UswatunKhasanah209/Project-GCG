<?php

namespace App\Exports;

use App\Models\Aspek;
use App\Models\Fuk;
use App\Models\FukScore;
use App\Models\LibraryDocument;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WorksheetExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithEvents, ShouldAutoSize
{
    protected string $type;
    protected int $year;
    protected $user;
    protected string $scoreState;
    protected int $headerRow = 5;

    public function __construct(string $type, int $year, $user, string $scoreState = 'unscored')
    {
        $this->type = strtolower($type);
        $this->year = $year;
        $this->user = $user;
        $this->scoreState = strtolower($scoreState);
    }

    public function collection()
    {
        $aspeks = Aspek::with([
            'indikators.parameters.fuks' => function ($q) {
                $q->whereNull('parent_id')->with('childrenRecursive');
            }
        ])->orderBy('id')->get();

        $scoreMap = FukScore::where('year', $this->year)->get()->keyBy('fuk_id');

        $leafScores = [];
        if ($this->scoreState === 'scored') {
            $leafScores = FukScore::where('year', $this->year)
                ->pluck('score', 'fuk_id')
                ->toArray();
        }

        $latestDocs = $this->latestDocumentMap();
        $displayData = $this->buildDisplayData($aspeks, $leafScores);

        return match ($this->type) {
            'all'       => $this->buildAllRows($aspeks, $displayData, $scoreMap, $latestDocs),
            'aspek'     => $this->buildAspekRows($aspeks, $displayData),
            'indikator' => $this->buildIndikatorRows($aspeks, $displayData),
            'parameter' => $this->buildParameterRows($aspeks, $displayData),
            'fuk'       => $this->buildFukRows($aspeks, $displayData, $scoreMap, $latestDocs),
            default     => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'all' => [
                'Tahun',
                'Aspek ID',
                'Aspek',
                'Indikator ID',
                'Indikator',
                'Parameter ID',
                'Parameter',
                'FUK ID',
                'FUK',
                'Bobot',
                'Score',
                'Persen',
                'Nama Dokumen',
                'Halaman',
                'Penjelasan',
                'Review Assessor',
                'Rekomendasi',
            ],
            'aspek' => [
                'Tahun',
                'Aspek ID',
                'Aspek',
                'Bobot',
                'Score',
                'Persen',
            ],
            'indikator' => [
                'Tahun',
                'Aspek ID',
                'Aspek',
                'Indikator ID',
                'Indikator',
                'Bobot',
                'Score',
                'Persen',
            ],
            'parameter' => [
                'Tahun',
                'Aspek ID',
                'Aspek',
                'Indikator ID',
                'Indikator',
                'Parameter ID',
                'Parameter',
                'Bobot',
                'Score',
                'Persen',
            ],
            'fuk' => [
                'Tahun',
                'Aspek ID',
                'Aspek',
                'Indikator ID',
                'Indikator',
                'Parameter ID',
                'Parameter',
                'FUK ID',
                'FUK',
                'Bobot',
                'Score',
                'Persen',
                'Nama Dokumen',
                'Halaman',
                'Penjelasan',
                'Review Assessor',
                'Rekomendasi',
            ],
            default => [],
        };
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter'   => ';',
            'enclosure'   => '"',
            'line_ending' => PHP_EOL,
            'use_bom'     => true,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                $delegate->insertNewRowBefore(1, 4);

                $highestColumn = $delegate->getHighestColumn();
                $highestRow = $delegate->getHighestRow();

                $title = match ($this->type) {
                    'all' => 'KERTAS KERJA GCG - KESELURUHAN',
                    'aspek' => 'KERTAS KERJA GCG - ASPEK',
                    'indikator' => 'KERTAS KERJA GCG - INDIKATOR',
                    'parameter' => 'KERTAS KERJA GCG - PARAMETER',
                    'fuk' => 'KERTAS KERJA GCG - FUK',
                    default => 'KERTAS KERJA GCG',
                };

                $scopeText = $this->user->role === 'admin' ? 'ADMIN' : 'USER / DIVISI SENDIRI';
                $scoreStateText = $this->scoreState === 'scored'
                    ? 'VERSI SUDAH ADA PENILAIAN'
                    : 'VERSI KOSONG / BELUM ADA PENILAIAN';

                $delegate->mergeCells("A1:{$highestColumn}1");
                $delegate->setCellValue('A1', $title);

                $delegate->mergeCells("A2:{$highestColumn}2");
                $delegate->setCellValue('A2', "Tahun: {$this->year}");

                $delegate->mergeCells("A3:{$highestColumn}3");
                $delegate->setCellValue('A3', "Akses: {$scopeText}");

                $delegate->mergeCells("A4:{$highestColumn}4");
                $delegate->setCellValue('A4', "Status Data: {$scoreStateText}");

                $delegate->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '17607A'],
                    ],
                ]);

                $delegate->getStyle("A2:{$highestColumn}4")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EAF2FF'],
                    ],
                ]);

                $delegate->getStyle("A{$this->headerRow}:{$highestColumn}{$this->headerRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D7E3FC'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '5B6B82'],
                        ],
                    ],
                ]);

                if ($highestRow >= $this->headerRow + 1) {
                    $delegate->getStyle("A" . ($this->headerRow + 1) . ":{$highestColumn}{$highestRow}")
                        ->applyFromArray([
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_TOP,
                                'wrapText' => true,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'B7C4D6'],
                                ],
                            ],
                        ]);
                }

                $delegate->freezePane('A6');

                $widths = [
                    'A' => 10,
                    'B' => 12,
                    'C' => 28,
                    'D' => 12,
                    'E' => 28,
                    'F' => 14,
                    'G' => 30,
                    'H' => 14,
                    'I' => 36,
                    'J' => 12,
                    'K' => 12,
                    'L' => 12,
                    'M' => 28,
                    'N' => 14,
                    'O' => 40,
                    'P' => 40,
                    'Q' => 40,
                ];

                foreach ($widths as $col => $width) {
                    $delegate->getColumnDimension($col)->setWidth($width);
                }

                $delegate->getStyle("J6:L{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                if ($this->type === 'all' && $highestRow >= 6) {
                    for ($row = 6; $row <= $highestRow; $row++) {
                        $aspekId = (string) $delegate->getCell("B{$row}")->getValue();
                        $indikatorId = (string) $delegate->getCell("D{$row}")->getValue();
                        $parameterId = (string) $delegate->getCell("F{$row}")->getValue();
                        $fukId = (string) $delegate->getCell("H{$row}")->getValue();

                        if ($aspekId !== '' && $indikatorId === '' && $parameterId === '' && $fukId === '') {
                            $delegate->getStyle("A{$row}:Q{$row}")->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'E8F0FE'],
                                ],
                            ]);
                        } elseif ($indikatorId !== '' && $parameterId === '' && $fukId === '') {
                            $delegate->getStyle("A{$row}:Q{$row}")->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F3F7FF'],
                                ],
                            ]);
                        } elseif ($parameterId !== '' && $fukId === '') {
                            $delegate->getStyle("A{$row}:Q{$row}")->applyFromArray([
                                'font' => ['italic' => true],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FAFCFF'],
                                ],
                            ]);
                        }
                    }
                }
            },
        ];
    }

    private function latestDocumentMap(): Collection
    {
        $docs = LibraryDocument::with('division')
            ->where('year', $this->year)
            ->when($this->user->role !== 'admin', function ($q) {
                $q->where('division_id', $this->user->division_id);
            })
            ->orderByDesc('id')
            ->get();

        $map = collect();

        foreach ($docs as $doc) {
            if (!$map->has($doc->fuk_id)) {
                $map->put($doc->fuk_id, $doc);
            }
        }

        return $map;
    }

    private function buildAllRows(Collection $aspeks, array $displayData, Collection $scoreMap, Collection $latestDocs): Collection
    {
        $rows = [];

        foreach ($aspeks as $aspek) {
            $aspekBox = $displayData['aspek'][$aspek->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

            $rows[] = [
                $this->year,
                $aspek->id,
                $aspek->name,
                '',
                '',
                '',
                '',
                '',
                '',
                $this->showValue($aspekBox['bobot']),
                $this->showValue($aspekBox['score']),
                $this->showPercent($aspekBox['percent']),
                '',
                '',
                '',
                '',
                '',
            ];

            foreach ($aspek->indikators as $indikator) {
                $indikatorBox = $displayData['indikators'][$indikator->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

                $rows[] = [
                    $this->year,
                    $aspek->id,
                    $aspek->name,
                    $indikator->id,
                    $indikator->name,
                    '',
                    '',
                    '',
                    '',
                    $this->showValue($indikatorBox['bobot']),
                    $this->showValue($indikatorBox['score']),
                    $this->showPercent($indikatorBox['percent']),
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                foreach ($indikator->parameters as $parameter) {
                    $parameterBox = $displayData['parameters'][$parameter->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

                    $rows[] = [
                        $this->year,
                        $aspek->id,
                        $aspek->name,
                        $indikator->id,
                        $indikator->name,
                        $parameter->id,
                        $parameter->name,
                        '',
                        '',
                        $this->showValue($parameterBox['bobot']),
                        $this->showValue($parameterBox['score']),
                        $this->showPercent($parameterBox['percent']),
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];

                    foreach ($parameter->fuks as $rootFuk) {
                        $this->appendAllFukRows(
                            $rows,
                            $rootFuk,
                            $aspek,
                            $indikator,
                            $parameter,
                            $displayData,
                            $scoreMap,
                            $latestDocs
                        );
                    }
                }
            }
        }

        return collect($rows);
    }

    private function appendAllFukRows(
        array &$rows,
        Fuk $fuk,
        $aspek,
        $indikator,
        $parameter,
        array $displayData,
        Collection $scoreMap,
        Collection $latestDocs
    ): void {
        $fukBox = $displayData['fuks'][$fuk->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];
        $score = $scoreMap->get($fuk->id);
        $doc = $latestDocs->get($fuk->id);

        $rows[] = [
            $this->year,
            $aspek->id,
            $aspek->name,
            $indikator->id,
            $indikator->name,
            $parameter->id,
            $parameter->name,
            $fuk->id,
            $fuk->name,
            $this->showValue($fukBox['bobot']),
            $this->showValue($fukBox['score']),
            $this->showPercent($fukBox['percent']),
            $this->showScoreField($score?->document_name ?: ($doc?->original_name ?? '')),
            $this->showScoreField($score?->page_reference ?? ''),
            $this->showScoreField($score?->explanation ?? ''),
            $this->showScoreField($score?->assessor_review ?? ''),
            $this->showScoreField($score?->recommendation ?? ''),
        ];

        foreach (($fuk->childrenRecursive ?? collect()) as $child) {
            $this->appendAllFukRows(
                $rows,
                $child,
                $aspek,
                $indikator,
                $parameter,
                $displayData,
                $scoreMap,
                $latestDocs
            );
        }
    }

    private function buildAspekRows(Collection $aspeks, array $displayData): Collection
    {
        $rows = [];

        foreach ($aspeks as $aspek) {
            $box = $displayData['aspek'][$aspek->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

            $rows[] = [
                $this->year,
                $aspek->id,
                $aspek->name,
                $this->showValue($box['bobot']),
                $this->showValue($box['score']),
                $this->showPercent($box['percent']),
            ];
        }

        return collect($rows);
    }

    private function buildIndikatorRows(Collection $aspeks, array $displayData): Collection
    {
        $rows = [];

        foreach ($aspeks as $aspek) {
            foreach ($aspek->indikators as $indikator) {
                $box = $displayData['indikators'][$indikator->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

                $rows[] = [
                    $this->year,
                    $aspek->id,
                    $aspek->name,
                    $indikator->id,
                    $indikator->name,
                    $this->showValue($box['bobot']),
                    $this->showValue($box['score']),
                    $this->showPercent($box['percent']),
                ];
            }
        }

        return collect($rows);
    }

    private function buildParameterRows(Collection $aspeks, array $displayData): Collection
    {
        $rows = [];

        foreach ($aspeks as $aspek) {
            foreach ($aspek->indikators as $indikator) {
                foreach ($indikator->parameters as $parameter) {
                    $box = $displayData['parameters'][$parameter->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];

                    $rows[] = [
                        $this->year,
                        $aspek->id,
                        $aspek->name,
                        $indikator->id,
                        $indikator->name,
                        $parameter->id,
                        $parameter->name,
                        $this->showValue($box['bobot']),
                        $this->showValue($box['score']),
                        $this->showPercent($box['percent']),
                    ];
                }
            }
        }

        return collect($rows);
    }

    private function buildFukRows(Collection $aspeks, array $displayData, Collection $scoreMap, Collection $latestDocs): Collection
    {
        $rows = [];

        foreach ($aspeks as $aspek) {
            foreach ($aspek->indikators as $indikator) {
                foreach ($indikator->parameters as $parameter) {
                    foreach ($parameter->fuks as $rootFuk) {
                        $this->appendFukRows(
                            $rows,
                            $rootFuk,
                            $aspek,
                            $indikator,
                            $parameter,
                            $displayData,
                            $scoreMap,
                            $latestDocs
                        );
                    }
                }
            }
        }

        return collect($rows);
    }

    private function appendFukRows(
        array &$rows,
        Fuk $fuk,
        $aspek,
        $indikator,
        $parameter,
        array $displayData,
        Collection $scoreMap,
        Collection $latestDocs
    ): void {
        $box = $displayData['fuks'][$fuk->id] ?? ['bobot' => null, 'score' => null, 'percent' => null];
        $score = $scoreMap->get($fuk->id);
        $doc = $latestDocs->get($fuk->id);

        $rows[] = [
            $this->year,
            $aspek->id,
            $aspek->name,
            $indikator->id,
            $indikator->name,
            $parameter->id,
            $parameter->name,
            $fuk->id,
            $fuk->name,
            $this->showValue($box['bobot']),
            $this->showValue($box['score']),
            $this->showPercent($box['percent']),
            $this->showScoreField($score?->document_name ?: ($doc?->original_name ?? '')),
            $this->showScoreField($score?->page_reference ?? ''),
            $this->showScoreField($score?->explanation ?? ''),
            $this->showScoreField($score?->assessor_review ?? ''),
            $this->showScoreField($score?->recommendation ?? ''),
        ];

        foreach (($fuk->childrenRecursive ?? collect()) as $child) {
            $this->appendFukRows(
                $rows,
                $child,
                $aspek,
                $indikator,
                $parameter,
                $displayData,
                $scoreMap,
                $latestDocs
            );
        }
    }

    private function buildDisplayData(Collection $aspeks, array $leafScores): array
    {
        $data = [
            'aspek' => [],
            'indikators' => [],
            'parameters' => [],
            'fuks' => [],
        ];

        foreach ($aspeks as $aspek) {
            $aspekWeight = $this->resolveEntityWeight(
                $aspek,
                $aspek->indikators->sum(function ($indikator) {
                    return $this->resolveEntityWeight($indikator);
                })
            );

            $aspekScore = 0.0;

            foreach ($aspek->indikators as $indikator) {
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
                        $parameterScore += $this->appendFukDisplayData($rootFuk, $leafScores, $data['fuks']);
                    }

                    $parameterPercent = $parameterWeight > 0 ? (($parameterScore / $parameterWeight) * 100) : 0.0;

                    $data['parameters'][$parameter->id] = [
                        'bobot' => $parameterWeight,
                        'score' => $parameterScore,
                        'percent' => $parameterPercent,
                    ];

                    $indikatorScore += $parameterScore;
                }

                $indikatorPercent = $indikatorWeight > 0 ? (($indikatorScore / $indikatorWeight) * 100) : 0.0;

                $data['indikators'][$indikator->id] = [
                    'bobot' => $indikatorWeight,
                    'score' => $indikatorScore,
                    'percent' => $indikatorPercent,
                ];

                $aspekScore += $indikatorScore;
            }

            $aspekPercent = $aspekWeight > 0 ? (($aspekScore / $aspekWeight) * 100) : 0.0;

            $data['aspek'][$aspek->id] = [
                'bobot' => $aspekWeight,
                'score' => $aspekScore,
                'percent' => $aspekPercent,
            ];
        }

        return $data;
    }

    private function appendFukDisplayData(Fuk $fuk, array $leafScores, array &$target): float
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
                'percent' => $leafScore !== null ? ($leafScore * 100) : null,
            ];

            return $weightedScore;
        }

        $childrenWeightedSum = 0.0;

        foreach ($children as $child) {
            $childrenWeightedSum += $this->appendFukDisplayData($child, $leafScores, $target);
        }

        $score = $weight > 0 ? ($childrenWeightedSum / $weight) : null;
        $percent = $score !== null ? ($score * 100) : null;

        $target[$fuk->id] = [
            'bobot' => $weight,
            'score' => $score,
            'weighted_score' => $childrenWeightedSum,
            'percent' => $percent,
        ];

        return $childrenWeightedSum;
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

    private function showValue($value): string
    {
        if ($this->scoreState !== 'scored') {
            return '';
        }

        if ($value === null) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.');
    }

    private function showPercent($value): string
    {
        if ($this->scoreState !== 'scored') {
            return '';
        }

        if ($value === null) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') . '%';
    }

    private function showScoreField($value): string
    {
        if ($this->scoreState !== 'scored') {
            return '';
        }

        return (string) $value;
    }
}