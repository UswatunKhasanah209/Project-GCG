<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aspeks', function (Blueprint $table) {
            $table->string('id', 10)->primary();   // A1..A6
            $table->text('name');
            $table->decimal('bobot', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspeks');
    }
};
