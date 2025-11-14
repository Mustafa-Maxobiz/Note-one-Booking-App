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
        // Add OAuth settings to system_settings table
        DB::table('system_settings')->insert([
            [
                'key' => 'wp_oauth_client_id',
                'value' => 'WLSCsiG3IS0coaI3Wb1jQP3ZQVg7M33k8PoppuOg',
                'description' => 'WordPress OAuth Client ID',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wp_oauth_client_secret',
                'value' => 'B78yd8Hdh0q3sSqRBrMfNgOpORj1H4NmAptDBva1',
                'description' => 'WordPress OAuth Client Secret',
                'type' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wp_oauth_redirect_uri',
                'value' => 'http://localhost/OnlineLessonBookingSystem/callback',
                'description' => 'WordPress OAuth Redirect URI',
                'type' => 'url',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wp_oauth_server',
                'value' => 'https://maxobiz.works/staging.site',
                'description' => 'WordPress OAuth Server URL',
                'type' => 'url',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'wp_oauth_enabled',
                'value' => '1',
                'description' => 'Enable WordPress OAuth Login',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove OAuth settings
        DB::table('system_settings')->whereIn('key', [
            'wp_oauth_client_id',
            'wp_oauth_client_secret',
            'wp_oauth_redirect_uri',
            'wp_oauth_server',
            'wp_oauth_enabled'
        ])->delete();
    }
};
