<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class ZoomSettingsSeeder extends Seeder
{
    public function run()
    {
        $zoomSettings = [
            [
                'key' => 'zoom_api_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Zoom API Key from Zoom App Marketplace'
            ],
            [
                'key' => 'zoom_api_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'Zoom API Secret from Zoom App Marketplace'
            ],
            [
                'key' => 'zoom_account_id',
                'value' => '',
                'type' => 'string',
                'description' => 'Zoom Account ID for JWT App'
            ],
            [
                'key' => 'zoom_webhook_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'Zoom Webhook Secret for webhook verification'
            ],
            [
                'key' => 'zoom_auto_recording',
                'value' => 'cloud',
                'type' => 'string',
                'description' => 'Default recording setting for Zoom meetings (none, local, cloud)'
            ],
            [
                'key' => 'zoom_waiting_room',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable waiting room for Zoom meetings'
            ],
            [
                'key' => 'zoom_join_before_host',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow participants to join before host'
            ],
            [
                'key' => 'zoom_mute_upon_entry',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Mute participants when they join the meeting'
            ]
        ];

        foreach ($zoomSettings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Zoom settings seeded successfully!');
    }
}
