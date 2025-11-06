<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class PusherSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Pusher settings for real-time notifications
        SystemSetting::setValue(
            'pusher_app_key',
            '36cc216640c31b85e68d',
            'string',
            'Pusher App Key for real-time notifications'
        );

        SystemSetting::setValue(
            'pusher_app_secret',
            'your-pusher-secret-key',
            'string',
            'Pusher App Secret for real-time notifications'
        );

        SystemSetting::setValue(
            'pusher_app_id',
            'your-pusher-app-id',
            'string',
            'Pusher App ID for real-time notifications'
        );

        SystemSetting::setValue(
            'pusher_app_cluster',
            'ap4',
            'string',
            'Pusher App Cluster for real-time notifications'
        );

        SystemSetting::setValue(
            'pusher_enabled',
            'true',
            'boolean',
            'Enable Pusher for real-time notifications'
        );
    }
}
