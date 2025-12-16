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
        // Drop the old table if it exists (with email column)
        Schema::dropIfExists('password_reset_tokens');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->integer('id_client')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->foreign('id_client')
                ->references('id_client')
                ->on('client')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
