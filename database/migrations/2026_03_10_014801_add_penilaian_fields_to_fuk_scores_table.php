<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuk_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('fuk_scores', 'document_name')) {
                $table->string('document_name')->nullable()->after('score');
            }

            if (!Schema::hasColumn('fuk_scores', 'page_reference')) {
                $table->string('page_reference')->nullable()->after('document_name');
            }

            if (!Schema::hasColumn('fuk_scores', 'explanation')) {
                $table->text('explanation')->nullable()->after('page_reference');
            }

            if (!Schema::hasColumn('fuk_scores', 'assessor_review')) {
                $table->text('assessor_review')->nullable()->after('explanation');
            }

            if (!Schema::hasColumn('fuk_scores', 'recommendation')) {
                $table->text('recommendation')->nullable()->after('assessor_review');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fuk_scores', function (Blueprint $table) {
            $columns = [];

            foreach (
                [
                    'document_name',
                    'page_reference',
                    'explanation',
                    'assessor_review',
                    'recommendation',
                ] as $column
            ) {
                if (Schema::hasColumn('fuk_scores', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
