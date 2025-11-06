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
        Schema::table('bookings', function (Blueprint $table) {
            // Add a check constraint to ensure zoom_meeting_id is positive when not null
            // This prevents negative meeting IDs from being stored
            DB::statement('ALTER TABLE bookings ADD CONSTRAINT check_zoom_meeting_id_positive 
                CHECK (zoom_meeting_id IS NULL OR zoom_meeting_id > 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove the check constraint
            DB::statement('ALTER TABLE bookings DROP CONSTRAINT check_zoom_meeting_id_positive');
        });
    }
};