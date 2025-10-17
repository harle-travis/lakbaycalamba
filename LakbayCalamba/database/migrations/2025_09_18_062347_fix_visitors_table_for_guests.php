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
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add guest fields if they don't exist
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Revert user_id to not nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            
            // Remove guest fields if they exist
            if (Schema::hasColumn('visitors', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
            if (Schema::hasColumn('visitors', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
            if (Schema::hasColumn('visitors', 'guest_contact')) {
                $table->dropColumn('guest_contact');
            }
        });
    }
};
