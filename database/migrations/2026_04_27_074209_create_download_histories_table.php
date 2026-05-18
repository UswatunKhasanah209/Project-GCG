<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('library_document_id')->nullable()->constrained('library_documents')->nullOnDelete();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'downloaded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_histories');
    }
};