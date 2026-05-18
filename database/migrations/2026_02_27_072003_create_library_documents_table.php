<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('library_documents', function (Blueprint $table) {
            $table->id();

            // siapa yang upload
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('uploader_user_id')->constrained('users')->cascadeOnDelete();

            // filter tahun penilaian
            $table->integer('year');

            // posisi di struktur GCG (biar bisa filter cepat)
            $table->string('aspek_id', 10);
            $table->string('indikator_id', 10);
            $table->string('parameter_id', 10);
            $table->string('fuk_id', 20); // node paling spesifik yang dipilih user

            // file
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // review admin
            $table->enum('review_status', ['pending', 'approved', 'rejected', 'need_revision'])
                  ->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable();

            $table->timestamps();

            // FK ke master data
            $table->foreign('aspek_id')->references('id')->on('aspeks')->cascadeOnDelete();
            $table->foreign('indikator_id')->references('id')->on('indikators')->cascadeOnDelete();
            $table->foreign('parameter_id')->references('id')->on('parameters')->cascadeOnDelete();
            $table->foreign('fuk_id')->references('id')->on('fuks')->cascadeOnDelete();

            // index biar query dashboard/review kenceng
            $table->index(['year', 'division_id']);
            $table->index(['year', 'fuk_id']);
            $table->index(['year', 'aspek_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_documents');
    }
};