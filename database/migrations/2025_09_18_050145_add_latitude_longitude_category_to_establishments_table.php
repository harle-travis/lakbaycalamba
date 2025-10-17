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
        Schema::table('establishments', function (Blueprint $table) {
            if (!Schema::hasColumn('establishments', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('establishments', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('establishments', 'category')) {
                $table->string('category')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'category']);
        });
    }
};
