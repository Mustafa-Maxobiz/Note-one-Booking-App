<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Http\Request;

class DebugThemeForm extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'debug:theme-form';

    /**
     * The console command description.
     */
    protected $description = 'Debug theme form submission issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging theme form submission...');

        // Test 1: Check current database values
        $this->line("\n🔍 Current Database Values:");
        $themeKeys = [
            'primary_bg_color', 'secondary_bg_color', 'primary_text_color',
            'secondary_text_color', 'accent_color', 'border_color',
            'navbar_bg_color', 'navbar_text_color', 'sidebar_bg_color',
            'sidebar_text_color', 'sidebar_hover_color', 'use_gradients',
            'brand_gradient', 'sidebar_gradient', 'navbar_gradient', 'accent_gradient'
        ];

        foreach ($themeKeys as $key) {
            $value = SystemSetting::getValue($key);
            $this->line("   {$key}: {$value}");
        }

        // Test 2: Simulate form submission
        $this->line("\n📝 Testing form submission simulation:");
        $formData = [
            'app_name' => 'Online Booking System',
            'contact_email' => 'admin@example.com',
            'contact_phone' => '+1234567890',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'lesson_duration_default' => 60,
            'cancellation_policy_hours' => 24,
            'primary_bg_color' => '#f0f0f0',
            'secondary_bg_color' => '#ffffff',
            'primary_text_color' => '#333333',
            'secondary_text_color' => '#666666',
            'accent_color' => '#ff6600',
            'border_color' => '#cccccc',
            'navbar_bg_color' => '#2c3e50',
            'navbar_text_color' => '#ffffff',
            'sidebar_bg_color' => '#e74c3c',
            'sidebar_text_color' => '#ffffff',
            'sidebar_hover_color' => '#c0392b',
            'use_gradients' => true,
            'brand_gradient' => 'linear-gradient(135deg, #ff6600 0%, #ff3300 100%)',
            'sidebar_gradient' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
            'navbar_gradient' => 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
            'accent_gradient' => 'linear-gradient(135deg, #ff6600 0%, #ff3300 100%)',
        ];

        $this->line("   Form data prepared with " . count($formData) . " fields");

        // Test 3: Test validation
        $this->line("\n🔍 Testing validation:");
        try {
            $request = new Request();
            $request->merge($formData);
            
            $validated = $request->validate([
                'app_name' => 'required|string|max:255',
                'contact_email' => 'required|email',
                'contact_phone' => 'nullable|string',
                'timezone' => 'required|string',
                'currency' => 'required|string|max:3',
                'lesson_duration_default' => 'required|integer|min:15|max:480',
                'cancellation_policy_hours' => 'required|integer|min:0',
                'primary_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'primary_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'accent_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'border_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'navbar_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'navbar_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'sidebar_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'sidebar_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'sidebar_hover_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'use_gradients' => 'nullable|boolean',
                'brand_gradient' => 'nullable|string|max:500',
                'sidebar_gradient' => 'nullable|string|max:500',
                'navbar_gradient' => 'nullable|string|max:500',
                'accent_gradient' => 'nullable|string|max:500',
            ]);
            
            $this->line("   ✅ Validation passed");
            
            // Test 4: Save settings
            $this->line("\n💾 Testing settings save:");
            $savedCount = 0;
            foreach ($validated as $key => $value) {
                $type = 'string';
                if (in_array($key, ['lesson_duration_default', 'cancellation_policy_hours'])) {
                    $type = 'integer';
                } elseif (in_array($key, ['use_gradients'])) {
                    $type = 'boolean';
                }
                
                $result = SystemSetting::setValue($key, $value, $type);
                $retrieved = SystemSetting::getValue($key);
                $success = ($retrieved == $value);
                $this->line("   {$key}: " . ($success ? '✅' : '❌') . " (saved: {$retrieved})");
                if ($success) $savedCount++;
            }
            
            $this->line("   Saved: {$savedCount}/" . count($validated) . " settings");
            
        } catch (\Exception $e) {
            $this->line("   ❌ Validation failed: " . $e->getMessage());
        }

        // Test 5: Check if changes are reflected
        $this->line("\n🎨 Checking updated values:");
        $updatedKeys = ['accent_color', 'primary_bg_color', 'sidebar_bg_color'];
        foreach ($updatedKeys as $key) {
            $value = SystemSetting::getValue($key);
            $this->line("   {$key}: {$value}");
        }

        $this->info("\n✅ Theme form debug completed!");
        return 0;
    }
}
