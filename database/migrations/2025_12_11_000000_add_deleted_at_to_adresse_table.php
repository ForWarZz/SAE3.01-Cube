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
        if (! Schema::hasColumn('adresse', 'deleted_at')) {
            Schema::table('adresse', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('tva_adresse');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('adresse', 'deleted_at')) {
            Schema::table('adresse', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
};
