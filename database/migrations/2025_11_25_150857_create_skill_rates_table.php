<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('skill_rates', function (Blueprint $table) {
        $table->id();
        $table->string('skill_name');
        $table->integer('min_rate');
        $table->integer('max_rate');
        $table->integer('average_rate');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_rates');
    }
};
