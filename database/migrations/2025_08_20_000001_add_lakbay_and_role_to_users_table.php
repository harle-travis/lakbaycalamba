<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void
{
Schema::table('users', function (Blueprint $table) {
$table->string('lakbay_id')->unique()->after('id');
$table->enum('role', ['tourist','admin','superadmin'])->default('tourist')->after('password');
});
}


public function down(): void
{
Schema::table('users', function (Blueprint $table) {
$table->dropColumn(['lakbay_id','role']);
});
}
};