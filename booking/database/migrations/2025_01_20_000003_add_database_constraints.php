<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add constraints to users table (skip if already exists)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->change();
                $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->change();
                $table->boolean('is_active')->default(true)->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to teachers table
        try {
            Schema::table('teachers', function (Blueprint $table) {
                $table->decimal('hourly_rate', 8, 2)->default(0)->change();
                $table->boolean('is_verified')->default(false)->change();
                $table->boolean('is_available')->default(true)->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to students table
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->change();
                $table->string('timezone')->default('UTC')->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to bookings table
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending')->change();
                $table->integer('duration_minutes')->unsigned()->change();
                $table->decimal('price', 8, 2)->unsigned()->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to payments table
        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->change();
                $table->decimal('amount', 8, 2)->unsigned()->change();
                $table->string('payment_method')->nullable()->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to feedback table
        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->integer('rating')->nullable()->unsigned()->change();
                $table->enum('type', ['teacher_to_student', 'student_to_teacher'])->default('student_to_teacher')->change();
                $table->boolean('is_public')->default(false)->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to packages table
        try {
            Schema::table('packages', function (Blueprint $table) {
                $table->integer('duration_months')->unsigned()->change();
                $table->decimal('price', 10, 2)->unsigned()->change();
                $table->boolean('is_active')->default(true)->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to subscriptions table
        try {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add constraints to teacher_availabilities table
        try {
            Schema::table('teacher_availabilities', function (Blueprint $table) {
                $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->change();
                $table->boolean('is_available')->default(true)->change();
            });
        } catch (\Exception $e) {
            // Skip if constraints already exist
        }

        // Add check constraints for data validation
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT chk_bookings_duration CHECK (duration_minutes > 0)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT chk_bookings_price CHECK (price >= 0)');
        DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payments_amount CHECK (amount >= 0)');
        DB::statement('ALTER TABLE feedback ADD CONSTRAINT chk_feedback_rating CHECK (rating IS NULL OR (rating >= 1 AND rating <= 5))');
        DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_packages_duration CHECK (duration_months > 0)');
        DB::statement('ALTER TABLE packages ADD CONSTRAINT chk_packages_price CHECK (price >= 0)');
        DB::statement('ALTER TABLE teachers ADD CONSTRAINT chk_teachers_hourly_rate CHECK (hourly_rate >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove check constraints
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT chk_bookings_duration');
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT chk_bookings_price');
        DB::statement('ALTER TABLE payments DROP CONSTRAINT chk_payments_amount');
        DB::statement('ALTER TABLE feedback DROP CONSTRAINT chk_feedback_rating');
        DB::statement('ALTER TABLE packages DROP CONSTRAINT chk_packages_duration');
        DB::statement('ALTER TABLE packages DROP CONSTRAINT chk_packages_price');
        DB::statement('ALTER TABLE teachers DROP CONSTRAINT chk_teachers_hourly_rate');
    }
};
