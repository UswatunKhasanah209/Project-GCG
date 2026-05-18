<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fuks', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // F1.. dst (pakai 20 biar aman)
            $table->string('parameter_id', 10);
            $table->string('parent_id', 20)->nullable(); // self reference
            $table->text('name');
            $table->string('tipe_penilaian', 30)->nullable(); // skala_0_1, skala_3, dst
            $table->decimal('bobot', 8, 3)->nullable();
            $table->timestamps();

            $table->foreign('parameter_id')->references('id')->on('parameters')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('fuks')->nullOnDelete();

            $table->index(['parameter_id']);
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuks');
    }
};
