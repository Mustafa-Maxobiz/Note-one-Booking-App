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
        Schema::dropIfExists('subjects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., Math, Science, Language, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
