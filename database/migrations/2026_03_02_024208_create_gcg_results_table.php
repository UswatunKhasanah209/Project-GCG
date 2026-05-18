<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gcg_results', function (Blueprint $table) {
            $table->id();
            $table->integer('year');

            $table->enum('level', ['fuk','parameter','indikator','aspek']);
            $table->string('entity_id', 20); // id nya: Fxx / Pxx / Ixx / Axx

            $table->decimal('score', 10, 4)->nullable();
            $table->timestamps();

            $table->unique(['year','level','entity_id']);
            $table->index(['year','level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gcg_results');
    }
};
