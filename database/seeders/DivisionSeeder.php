<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            "Divisi Sekretaris Perusahaan",#1
            "Divisi Satuan Pengawas Intern",#2
            "Divisi Keuangan, Akuntansi dan TJSL",#3
            "Divisi Human Capital dan General Affairs",#4
            "Divisi Manajemen Risiko dan Kepatuhan",#5
            "Divisi Hukum",#6
            "Divisi Teknologi",#7
            "Divisi Riset dan Pengembangan",#8
            "Divisi Subsidiary and Business Strategy",#9
            "Divisi Pemasaran",#10
            "Divisi Pabrik Banyuwangi",#11
            "Divisi Perencanaan dan Pengendalian Operasi",#12
            "Divisi Produksi",#13
            "Divisi Pengelolaan Kualitas dan Dukungan Produk",#14
            "Divisi Pengelolaan Kualitas Proses Bisnis",#15
            "Divisi Logistik",#16
        ];

        foreach ($divisions as $name) {
            Division::create(['name' => $name]);
        }
    }
}
