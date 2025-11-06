<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('session_recordings', function (Blueprint $table) {
            $table->string('passcode')->nullable()->after('play_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_recordings', function (Blueprint $table) {
            $table->dropColumn('passcode');
        });
    }
};
