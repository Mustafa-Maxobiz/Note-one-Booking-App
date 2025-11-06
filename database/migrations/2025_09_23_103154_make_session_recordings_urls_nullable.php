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
        Schema::table('session_recordings', function (Blueprint $table) {
            $table->string('play_url')->nullable()->change();
            $table->string('download_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_recordings', function (Blueprint $table) {
            $table->string('play_url')->nullable(false)->change();
            $table->string('download_url')->nullable(false)->change();
        });
    }
};