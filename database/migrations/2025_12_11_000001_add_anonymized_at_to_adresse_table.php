<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('adresse', 'anonymized_at')) {
            Schema::table('adresse', function (Blueprint $table) {
                $table->timestamp('anonymized_at')->nullable()->after('deleted_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('adresse', 'anonymized_at')) {
            Schema::table('adresse', function (Blueprint $table) {
                $table->dropColumn('anonymized_at');
            });
        }
    }
};
