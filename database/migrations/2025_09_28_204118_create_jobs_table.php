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
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 👈 This line
        $table->string('title');
        $table->text('description');
        $table->string('location');
        $table->decimal('budget', 10, 2)->nullable();
        $table->enum('status', ['open', 'in_progress', 'completed'])->default('open');
        $table->timestamps();
    });
}





    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }

};

