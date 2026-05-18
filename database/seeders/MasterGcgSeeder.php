<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Aspek;
use App\Models\Indikator;
use App\Models\Parameter;
use App\Models\Fuk;

class MasterGcgSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Throwable $e) {
            //
        }

        Fuk::truncate();
        Parameter::truncate();
        Indikator::truncate();
        Aspek::truncate();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {
            //
        }

        $aspekPath     = storage_path('app/seed/Aspek.csv');
        $indikatorPath = storage_path('app/seed/Indikator.csv');
        $parameterPath = storage_path('app/seed/Parameter.csv');
        $fukPath       = storage_path('app/seed/fuk.csv');

        $delimAspek     = $this->detectDelimiter($aspekPath);
        $delimIndikator = $this->detectDelimiter($indikatorPath);
        $delimParameter = $this->detectDelimiter($parameterPath);
        $delimFuk       = $this->detectDelimiter($fukPath);

        $this->importAspek($aspekPath, $delimAspek);
        $this->importIndikator($indikatorPath, $delimIndikator);
        $this->importParameter($parameterPath, $delimParameter);
        $this->importFuk($fukPath, $delimFuk);
    }

    private function detectDelimiter(string $path): string
    {
        if (!file_exists($path)) {
            throw new \Exception('CSV not found: ' . $path);
        }

        $sample = file_get_contents($path, false, null, 0, 4096) ?: '';

        $delimiters = [
            ';'  => substr_count($sample, ';'),
            ','  => substr_count($sample, ','),
            "\t" => substr_count($sample, "\t"),
        ];

        arsort($delimiters);

        return array_key_first($delimiters) ?: ';';
    }

    private function readCsv(string $path, string $delimiter = ';'): array
    {
        if (!file_exists($path)) {
            throw new \Exception('CSV not found: ' . $path);
        }

        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \Exception('Cannot open CSV: ' . $path);
        }

        $header = null;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($data) === 1 && trim((string) ($data[0] ?? '')) === '') {
                continue;
            }

            if ($header === null) {
                $header = array_map(function ($h) {
                    $h = (string) $h;
                    $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
                    $h = trim($h);
                    $h = strtolower($h);
                    return $h;
                }, $data);
                continue;
            }

            $row = [];
            foreach ($header as $i => $col) {
                $val = $data[$i] ?? null;

                if (is_string($val)) {
                    $val = trim($val);
                }

                $row[$col] = ($val === '' ? null : $val);
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function normalizeDecimal($value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return round((float) $value, 3);
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(' ', '', $value);

        $hasComma = str_contains($value, ',');
        $hasDot   = str_contains($value, '.');

        if ($hasComma && $hasDot) {
            if (strrpos($value, ',') > strrpos($value, '.')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($hasComma) {
            $value = str_replace(',', '.', $value);
        }

        if (!is_numeric($value)) {
            throw new \Exception("Nilai numerik tidak valid: {$value}");
        }

        return round((float) $value, 3);
    }

    private function normalizeInt($value, int $default = 0): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_int($value)) {
            return $value;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return $default;
        }

        $value = preg_replace('/[^\d\-]/', '', $value);

        if ($value === '' || $value === '-') {
            return $default;
        }

        return (int) $value;
    }

    private function importAspek(string $path, string $delimiter): void
    {
        $rows = $this->readCsv($path, $delimiter);

        foreach ($rows as $r) {
            if (!isset($r['id'])) {
                throw new \Exception("Header kolom 'id' tidak ditemukan di Aspek.csv.");
            }

            Aspek::updateOrCreate(
                ['id' => $r['id']],
                [
                    'name'  => $r['name'] ?? '',
                    'bobot' => $this->normalizeDecimal($r['bobot'] ?? null),
                ]
            );
        }
    }

    private function importIndikator(string $path, string $delimiter): void
    {
        $rows = $this->readCsv($path, $delimiter);

        foreach ($rows as $r) {
            if (!isset($r['id']) || !isset($r['aspect_id'])) {
                throw new \Exception("Header kolom 'id' / 'aspect_id' tidak ditemukan di Indikator.csv.");
            }

            Indikator::updateOrCreate(
                ['id' => $r['id']],
                [
                    'aspect_id' => $r['aspect_id'],
                    'name'      => $r['name'] ?? '',
                    'bobot'     => $this->normalizeDecimal($r['bobot'] ?? null),
                ]
            );
        }
    }

    private function importParameter(string $path, string $delimiter): void
    {
        $rows = $this->readCsv($path, $delimiter);

        foreach ($rows as $r) {
            if (!isset($r['id']) || !isset($r['indikator_id'])) {
                throw new \Exception("Header kolom 'id' / 'indikator_id' tidak ditemukan di Parameter.csv.");
            }

            Parameter::updateOrCreate(
                ['id' => $r['id']],
                [
                    'indikator_id' => $r['indikator_id'],
                    'name'         => $r['name'] ?? '',
                    'bobot'        => $this->normalizeDecimal($r['bobot'] ?? null),
                ]
            );
        }
    }

    private function importFuk(string $path, string $delimiter): void
    {
        $rows = $this->readCsv($path, $delimiter);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Throwable $e) {
            //
        }

        foreach ($rows as $r) {
            if (!isset($r['id']) || !isset($r['parameter_id'])) {
                throw new \Exception("Header kolom 'id' / 'parameter_id' tidak ditemukan di fuk.csv.");
            }

            Fuk::updateOrCreate(
                ['id' => $r['id']],
                [
                    'parameter_id'   => $r['parameter_id'],
                    'parent_id'      => null,
                    'name'           => $r['name'] ?? '',
                    'tipe_penilaian' => $r['tipe_penilaian'] ?? null,
                    'required_docs'  => $this->normalizeInt($r['required_docs'] ?? null, 1),
                    'bobot'          => $this->normalizeDecimal($r['bobot'] ?? null),
                ]
            );
        }

        $rootsByParameter = [];

        foreach ($rows as $r) {
            $parameterId = $r['parameter_id'] ?? null;
            $parentIdRaw = $r['parent_id'] ?? null;

            if (!$parameterId) {
                continue;
            }

            if ($parentIdRaw === null || $parentIdRaw === '') {
                $rootsByParameter[$parameterId][] = $r['id'];
            }
        }

        foreach ($rows as $r) {
            $currentId   = $r['id'] ?? null;
            $parameterId = $r['parameter_id'] ?? null;
            $rawParent   = $r['parent_id'] ?? null;

            if (!$currentId || !$parameterId || $rawParent === null || $rawParent === '') {
                continue;
            }

            $resolvedParentId = null;
            $rawParentString = trim((string) $rawParent);

            if (str_starts_with(strtoupper($rawParentString), 'F')) {
                $resolvedParentId = $rawParentString;
            } else {
                $index = (int) $rawParentString;

                if ($index > 0 && isset($rootsByParameter[$parameterId][$index - 1])) {
                    $resolvedParentId = $rootsByParameter[$parameterId][$index - 1];
                }
            }

            if ($resolvedParentId && $resolvedParentId !== $currentId) {
                Fuk::where('id', $currentId)->update([
                    'parent_id' => $resolvedParentId,
                ]);
            }
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {
            //
        }
    }
}