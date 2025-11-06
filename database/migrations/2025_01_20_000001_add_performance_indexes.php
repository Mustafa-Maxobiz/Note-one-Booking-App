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
            $table->index(['start_time', 'end_time'], 'idx_bookings_time_range');
            $table->index('status', 'idx_bookings_status');
            $table->index(['teacher_id', 'start_time'], 'idx_bookings_teacher_time');
            $table->index(['student_id', 'start_time'], 'idx_bookings_student_time');
            $table->index('scheduled_at', 'idx_bookings_scheduled_at');
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('role', 'idx_users_role');
            $table->index('is_active', 'idx_users_active');
        });

        // Add indexes for notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read'], 'idx_notifications_user_read');
            $table->index('type', 'idx_notifications_type');
            $table->index('created_at', 'idx_notifications_created');
        });

        // Add indexes for payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'idx_payments_status');
            $table->index(['student_id', 'status'], 'idx_payments_student_status');
            $table->index(['teacher_id', 'status'], 'idx_payments_teacher_status');
            $table->index('paid_at', 'idx_payments_paid_at');
        });

        // Add indexes for teacher_availabilities table
        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->index(['teacher_id', 'day_of_week'], 'idx_availability_teacher_day');
            $table->index(['day_of_week', 'is_available'], 'idx_availability_day_active');
        });

        // Add indexes for feedback table
        Schema::table('feedback', function (Blueprint $table) {
            $table->index(['teacher_id', 'type'], 'idx_feedback_teacher_type');
            $table->index(['student_id', 'type'], 'idx_feedback_student_type');
            $table->index('is_public', 'idx_feedback_public');
        });

        // Add indexes for subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_subscriptions_user_status');
            $table->index('expires_at', 'idx_subscriptions_expires');
            $table->index('status', 'idx_subscriptions_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_time_range');
            $table->dropIndex('idx_bookings_status');
            $table->dropIndex('idx_bookings_teacher_time');
            $table->dropIndex('idx_bookings_student_time');
            $table->dropIndex('idx_bookings_scheduled_at');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_active');
        });

        // Drop indexes for notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_read');
            $table->dropIndex('idx_notifications_type');
            $table->dropIndex('idx_notifications_created');
        });

        // Drop indexes for payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status');
            $table->dropIndex('idx_payments_student_status');
            $table->dropIndex('idx_payments_teacher_status');
            $table->dropIndex('idx_payments_paid_at');
        });

        // Drop indexes for teacher_availabilities table
        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->dropIndex('idx_availability_teacher_day');
            $table->dropIndex('idx_availability_day_active');
        });

        // Drop indexes for feedback table
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex('idx_feedback_teacher_type');
            $table->dropIndex('idx_feedback_student_type');
            $table->dropIndex('idx_feedback_public');
        });

        // Drop indexes for subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('idx_subscriptions_user_status');
            $table->dropIndex('idx_subscriptions_expires');
            $table->dropIndex('idx_subscriptions_status');
        });
    }
};
