<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fuk_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id');
            $table->string('fuk_id');
            $table->integer('year');
            $table->string('status', 20)->default('black');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['division_id', 'fuk_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuk_statuses');
    }
};