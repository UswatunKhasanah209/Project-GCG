<?php

namespace App\Console\Commands;

use App\Exports\WorksheetExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ExportSeedCsv extends Command
{
    protected $signature = 'export:seed-csv';
    protected $description = 'Export master seed data to storage/app/seed as CSV files';

    public function handle(): int
    {
        $seedPath = storage_path('app/seed');

        if (!File::exists($seedPath)) {
            File::makeDirectory($seedPath, 0755, true);
        }

        $files = [
            'aspek'     => 'Aspek.csv',
            'indikator' => 'Indikator.csv',
            'parameter' => 'Parameter.csv',
            'fuk'       => 'fuk.csv',
        ];

        foreach ($files as $type => $filename) {
            Excel::store(
                new WorksheetExport($type),
                'seed/' . $filename,
                'local',
                ExcelFormat::CSV
            );

            $this->info('Berhasil export: ' . $seedPath . DIRECTORY_SEPARATOR . $filename);
        }

        $this->newLine();
        $this->info('Semua file CSV berhasil dibuat di storage/app/seed');

        return self::SUCCESS;
    }
}