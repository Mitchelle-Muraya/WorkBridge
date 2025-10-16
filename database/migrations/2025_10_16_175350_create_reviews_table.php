<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable(); // link to a job
            $table->unsignedBigInteger('client_id')->nullable(); // who gave the review
            $table->unsignedBigInteger('worker_id')->nullable(); // who received the review
            $table->tinyInteger('rating')->default(0); // 1–5 stars
            $table->text('comment')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('worker_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
