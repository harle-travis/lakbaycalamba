<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // Try dropping the unique index safely
        try {
            DB::statement('ALTER TABLE visitors DROP INDEX visitors_user_id_establishment_id_unique');
        } catch (\Exception $e) {
            // Skip silently if it's still referenced by a foreign key or doesn't exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            if (Schema::hasColumn('visitors', 'is_guest')) {
                $table->dropColumn(['is_guest', 'guest_name', 'guest_contact']);
            }

            // Recreate the unique constraint if needed
            try {
                $table->unique(['user_id', 'establishment_id']);
            } catch (\Exception $e) {
                // Skip if already exists
            }
        });
    }
};
