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
        Schema::table('visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('visitors', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('visitors', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('is_guest');
            }
            if (!Schema::hasColumn('visitors', 'guest_contact')) {
                $table->string('guest_contact')->nullable()->after('guest_name');
            }
        });
        
        // Check if the unique constraint exists before dropping it
        $indexExists = Schema::hasIndex('visitors', 'visitors_user_id_establishment_id_unique');
        if ($indexExists) {
            Schema::table('visitors', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'establishment_id']); // Remove unique constraint
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['is_guest', 'guest_name', 'guest_contact']);
            $table->unique(['user_id', 'establishment_id']); // Restore unique constraint
        });
    }
};
