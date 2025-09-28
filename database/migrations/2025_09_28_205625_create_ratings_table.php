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
      Schema::create('ratings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
    $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
    $table->integer('rating');
    $table->text('review')->nullable();
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
