<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('establishment_pics', function (Blueprint $table) {
$table->id();
$table->foreignId('establishment_id')->constrained('establishments')->onDelete('cascade');
$table->string('image_path');
$table->string('caption')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('establishment_pics');
}
};