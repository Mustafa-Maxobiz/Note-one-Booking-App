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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('qualifications')->nullable();
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->string('timezone')->default('UTC');
            $table->json('subjects')->nullable(); // Array of subject IDs
            $table->json('teaching_style')->nullable();
            $table->string('zoom_api_key')->nullable();
            $table->string('zoom_api_secret')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
