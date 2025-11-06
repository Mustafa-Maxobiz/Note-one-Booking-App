<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('booking_time_limit')->nullable()->after('status');
            $table->boolean('notification_sent_24h')->default(false)->after('booking_time_limit');
            $table->boolean('notification_sent_1h')->default(false)->after('notification_sent_24h');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_time_limit',
                'notification_sent_24h',
                'notification_sent_1h'
            ]);
        });
    }
};
