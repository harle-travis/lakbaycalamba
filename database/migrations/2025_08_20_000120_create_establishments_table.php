<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::create('establishments', function (Blueprint $table) {
$table->id();
$table->string('establishment_name');
$table->string('location')->nullable(); // address or barangay
$table->text('maps_data')->nullable(); // embed/link/geojson
$table->text('description')->nullable();
$table->json('schedule')->nullable(); // optional JSON hours
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('establishments');
}
};