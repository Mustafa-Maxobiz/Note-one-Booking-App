<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('zoom_meeting_id');
            $table->string('recording_id');
            $table->string('recording_type'); // audio, video, chat, etc.
            $table->string('file_name');
            $table->string('download_url');
            $table->string('play_url');
            $table->integer('file_size')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->timestamp('recording_start')->nullable();
            $table->timestamp('recording_end')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_recordings');
    }
};
