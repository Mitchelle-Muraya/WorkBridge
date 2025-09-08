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
    Schema::create('jobs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
        $table->string('title');
        $table->text('description');
        $table->decimal('budget', 10, 2)->nullable();
        $table->enum('status', ['pending','approved','rejected','open','in_progress','completed'])->default('pending');
        $table->date('deadline')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('jobs');
}
};
