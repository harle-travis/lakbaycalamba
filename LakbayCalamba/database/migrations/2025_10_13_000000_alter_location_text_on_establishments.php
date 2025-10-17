<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('establishments') && Schema::hasColumn('establishments', 'location')) {
            Schema::table('establishments', function (Blueprint $table) {
                // Change location to text to allow longer addresses
                $table->text('location')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('establishments') && Schema::hasColumn('establishments', 'location')) {
            Schema::table('establishments', function (Blueprint $table) {
                $table->string('location')->nullable()->change();
            });
        }
    }
};


