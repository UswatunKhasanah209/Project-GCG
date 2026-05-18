<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fuks', function (Blueprint $table) {
            $table->unsignedInteger('required_docs')
                ->default(1)
                ->after('tipe_penilaian');
        });
    }

    public function down(): void
    {
        Schema::table('fuks', function (Blueprint $table) {
            $table->dropColumn('required_docs');
        });
    }
};