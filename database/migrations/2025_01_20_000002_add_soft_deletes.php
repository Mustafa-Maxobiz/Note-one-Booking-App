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
        // Add soft deletes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to students table
        Schema::table('students', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to feedback table
        Schema::table('feedback', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to packages table
        Schema::table('packages', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to teacher_availabilities table
        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes from all tables
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('teacher_availabilities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
