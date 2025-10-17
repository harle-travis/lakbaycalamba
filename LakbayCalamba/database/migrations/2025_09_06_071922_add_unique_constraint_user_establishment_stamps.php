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
        Schema::table('stamps', function (Blueprint $table) {
            // Add unique constraint for user_id and establishment_id (one stamp per user per establishment ever)
            $table->unique(['user_id', 'establishment_id'], 'stamps_user_establishment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stamps', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('stamps_user_establishment_unique');
        });
    }
};
