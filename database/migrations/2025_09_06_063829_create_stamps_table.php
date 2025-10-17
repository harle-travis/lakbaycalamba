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
        Schema::create('stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->timestamp('visit_date')->useCurrent();
            $table->timestamps();
            
            // Ensure a user can only get one stamp per establishment per day
            $table->unique(['user_id', 'establishment_id', 'visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stamps');
    }
};
