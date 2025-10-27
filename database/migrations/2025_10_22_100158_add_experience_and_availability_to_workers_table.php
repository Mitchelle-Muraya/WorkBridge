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
    Schema::table('workers', function (Blueprint $table) {
        $table->string('experience')->nullable()->after('skills');
        $table->string('availability')->default('available')->after('experience');
    });
}

public function down(): void
{
    Schema::table('workers', function (Blueprint $table) {
        $table->dropColumn(['experience', 'availability']);
    });
}

};
