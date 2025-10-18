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
        Schema::create('email_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'email_change' or 'password_change'
            $table->string('token', 64)->unique();
            $table->string('new_email')->nullable(); // For email changes
            $table->string('new_password_hash')->nullable(); // For password changes
            $table->boolean('confirmed')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_confirmations');
    }
};
