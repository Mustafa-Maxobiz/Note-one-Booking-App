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
        // Increase size of encrypted fields in teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->text('zoom_api_key')->nullable()->change();
            $table->text('zoom_api_secret')->nullable()->change();
        });

        // Increase size of encrypted fields in payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->text('transaction_id')->nullable()->change();
            $table->text('payment_details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert encrypted fields to original sizes
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('zoom_api_key')->nullable()->change();
            $table->string('zoom_api_secret')->nullable()->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->change();
            $table->json('payment_details')->nullable()->change();
        });
    }
};
