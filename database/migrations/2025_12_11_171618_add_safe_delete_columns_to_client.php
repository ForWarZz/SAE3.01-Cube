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
        Schema::table('client', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('date_der_connexion');
            $table->timestamp('anonymized_at')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
            $table->dropColumn('anonymized_at');
        });
    }
};
