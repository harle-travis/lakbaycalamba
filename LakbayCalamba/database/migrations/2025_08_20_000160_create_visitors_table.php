<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('visitors', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('establishment_id');
    $table->unsignedBigInteger('user_id');
    $table->timestamp('visit_date')->nullable();
    $table->timestamps();

    $table->foreign('establishment_id')->references('id')->on('establishments')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    
    $table->unique(['user_id', 'establishment_id']);
});
}


public function down(): void
{
Schema::dropIfExists('visitors');
}
};