<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('userdata', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
// Keep a running total of stamps for leaderboard
$table->unsignedInteger('stamps_total')->default(0);
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('userdata');
}
};