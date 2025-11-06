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
        // Add indexes for bookings table
        Schema::table('bookings', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['teacher_id', 'status', 'created_at'], 'bookings_teacher_status_created_idx');
            $table->index(['student_id', 'status', 'created_at'], 'bookings_student_status_created_idx');
            $table->index(['status', 'created_at'], 'bookings_status_created_idx');
            $table->index(['start_time', 'status'], 'bookings_start_time_status_idx');
            $table->index(['created_at', 'status'], 'bookings_created_status_idx');
            $table->index(['teacher_id', 'start_time'], 'bookings_teacher_start_time_idx');
        });

        // Add indexes for teacher_availabilities table
        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->index(['teacher_id', 'day_of_week'], 'teacher_availabilities_teacher_day_idx');
            $table->index(['teacher_id', 'is_available'], 'teacher_availabilities_teacher_available_idx');
        });

        // Add indexes for feedback table
        Schema::table('feedback', function (Blueprint $table) {
            $table->index(['teacher_id', 'created_at'], 'feedback_teacher_created_idx');
            $table->index(['student_id', 'created_at'], 'feedback_student_created_idx');
            $table->index(['booking_id'], 'feedback_booking_idx');
            $table->index(['rating', 'created_at'], 'feedback_rating_created_idx');
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'created_at'], 'users_role_created_idx');
            $table->index(['is_active', 'role'], 'users_active_role_idx');
        });

        // Add indexes for teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->index(['is_available', 'is_verified'], 'teachers_available_verified_idx');
            $table->index(['user_id'], 'teachers_user_idx');
        });

        // Add indexes for students table
        Schema::table('students', function (Blueprint $table) {
            $table->index(['user_id'], 'students_user_idx');
        });

        // Add indexes for payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'payments_status_created_idx');
            $table->index(['teacher_id', 'status'], 'payments_teacher_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_teacher_status_created_idx');
            $table->dropIndex('bookings_student_status_created_idx');
            $table->dropIndex('bookings_status_created_idx');
            $table->dropIndex('bookings_start_time_status_idx');
            $table->dropIndex('bookings_created_status_idx');
            $table->dropIndex('bookings_teacher_start_time_idx');
        });

        // Drop indexes for teacher_availabilities table
        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->dropIndex('teacher_availabilities_teacher_day_idx');
            $table->dropIndex('teacher_availabilities_teacher_available_idx');
        });

        // Drop indexes for feedback table
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex('feedback_teacher_created_idx');
            $table->dropIndex('feedback_student_created_idx');
            $table->dropIndex('feedback_booking_idx');
            $table->dropIndex('feedback_rating_created_idx');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_created_idx');
            $table->dropIndex('users_active_role_idx');
        });

        // Drop indexes for teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropIndex('teachers_available_verified_idx');
            $table->dropIndex('teachers_user_idx');
        });

        // Drop indexes for students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_user_idx');
        });

        // Drop indexes for payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_idx');
            $table->dropIndex('payments_teacher_status_idx');
        });
    }
};
