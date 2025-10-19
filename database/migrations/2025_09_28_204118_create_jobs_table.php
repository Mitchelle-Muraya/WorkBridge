<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('jobs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('client_id');
        $table->string('title');
        $table->text('description');
        $table->string('skills_required');
        $table->string('location');
        $table->decimal('budget', 10, 2)->nullable();
        $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
        $table->timestamps();
        // foreign key to users table
        $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

    });
}





    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }

};

