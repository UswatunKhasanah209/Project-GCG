<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fuk_scores', function (Blueprint $table) {
            $table->string('document_name')->nullable()->after('score');
            $table->string('page_reference')->nullable()->after('document_name');
            $table->text('explanation')->nullable()->after('page_reference');
            $table->text('assessor_review')->nullable()->after('explanation');
            $table->text('recommendation')->nullable()->after('assessor_review');
        });
    }

    public function down(): void
    {
        Schema::table('fuk_scores', function (Blueprint $table) {
            $table->dropColumn([
                'document_name',
                'page_reference',
                'explanation',
                'assessor_review',
                'recommendation',
            ]);
        });
    }
};