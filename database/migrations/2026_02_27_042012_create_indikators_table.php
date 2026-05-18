<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indikators', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // I1..I43
            $table->string('aspect_id', 10);
            $table->text('name');
            $table->decimal('bobot', 8, 3)->nullable();
            $table->timestamps();

            $table->foreign('aspect_id')->references('id')->on('aspeks')->cascadeOnDelete();
            $table->index(['aspect_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};
