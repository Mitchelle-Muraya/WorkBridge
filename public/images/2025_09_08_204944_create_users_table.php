<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();                 // full name
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();             // may be null for migrated users
            $table->string('role')->default('worker');         // worker|client|admin
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();                // JSON array of skills
            $table->string('resume_path')->nullable();         // stored file path
            $table->string('avatar_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
