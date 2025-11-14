<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add default theme settings
        $defaultThemeSettings = [
            'primary_bg_color' => '#f8f9fa',
            'secondary_bg_color' => '#ffffff',
            'primary_text_color' => '#212529',
            'secondary_text_color' => '#6c757d',
            'accent_color' => '#007bff',
            'border_color' => '#dee2e6',
            'navbar_bg_color' => '#343a40',
            'navbar_text_color' => '#ffffff',
        ];

        foreach ($defaultThemeSettings as $key => $value) {
            SystemSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'description' => 'Theme customization setting']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove theme settings
        $themeKeys = [
            'primary_bg_color',
            'secondary_bg_color',
            'primary_text_color',
            'secondary_text_color',
            'accent_color',
            'border_color',
            'navbar_bg_color',
            'navbar_text_color',
        ];

        SystemSetting::whereIn('key', $themeKeys)->delete();
    }
};