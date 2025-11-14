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
        Schema::create('lesson_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->dateTime('lesson_date');
            $table->string('title');
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('visibility', ['student_and_teacher', 'teacher_only'])->default('student_and_teacher');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'lesson_date']);
            $table->index(['teacher_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_notes');
    }
};


