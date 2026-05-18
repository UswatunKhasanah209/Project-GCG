<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fuk_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('fuk_id', 20);

            $table->decimal('score', 8, 2);
            $table->foreignId('scored_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('fuk_id')->references('id')->on('fuks')->cascadeOnDelete();
            $table->unique(['year','fuk_id']); // global per tahun
            $table->index(['year']);
        }) ;
    }

    public function down(): void
    {
        Schema::dropIfExists('fuk_scores');
    }
};
