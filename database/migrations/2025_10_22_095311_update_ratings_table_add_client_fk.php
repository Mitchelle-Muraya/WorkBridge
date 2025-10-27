<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Add client_id if not already there
            if (!Schema::hasColumn('ratings', 'client_id')) {
                $table->unsignedBigInteger('client_id')->after('worker_id');
            }

            // 🔑 Use unique foreign key names to avoid duplicate-key error
            $table->foreign('client_id', 'fk_ratings_client')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('worker_id', 'fk_ratings_worker')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign('fk_ratings_client');
            $table->dropForeign('fk_ratings_worker');
            $table->dropColumn('client_id');
        });
    }
};
