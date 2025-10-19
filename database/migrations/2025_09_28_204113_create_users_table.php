<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role')->default('worker'); // Default is worker
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_status')->default('incomplete');
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('profile_status');
    });
    }
};
