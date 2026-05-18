<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parameters', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // P1..P153
            $table->string('indikator_id', 10);
            $table->text('name');
            $table->decimal('bobot', 8, 3)->nullable();
            $table->timestamps();

            $table->foreign('indikator_id')->references('id')->on('indikators')->cascadeOnDelete();
            $table->index(['indikator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
