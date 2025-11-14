<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class TestThemeFormSubmission extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:theme-form-submission';

    /**
     * The console command description.
     */
    protected $description = 'Test actual theme form submission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing theme form submission...');

        // Create a mock request with theme data
        $request = new Request();
        $request->merge([
            'app_name' => 'Online Booking System',
            'contact_email' => 'admin@example.com',
            'contact_phone' => '+1234567890',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'lesson_duration_default' => 60,
            'cancellation_policy_hours' => 24,
            'primary_bg_color' => '#ff0000',
            'secondary_bg_color' => '#ffffff',
            'primary_text_color' => '#000000',
            'secondary_text_color' => '#666666',
            'accent_color' => '#00ff00',
            'border_color' => '#cccccc',
            'navbar_bg_color' => '#333333',
            'navbar_text_color' => '#ffffff',
            'sidebar_bg_color' => '#ff6600',
            'sidebar_text_color' => '#ffffff',
            'sidebar_hover_color' => '#ff3300',
            'use_gradients' => true,
            'brand_gradient' => 'linear-gradient(135deg, #ff0000 0%, #ff6600 100%)',
            'sidebar_gradient' => 'linear-gradient(135deg, #ff6600 0%, #ff3300 100%)',
            'navbar_gradient' => 'linear-gradient(135deg, #333333 0%, #444444 100%)',
            'accent_gradient' => 'linear-gradient(135deg, #00ff00 0%, #00cc00 100%)',
        ]);

        $this->line("\n📝 Form data prepared:");
        $this->line("   Primary BG: #ff0000");
        $this->line("   Accent Color: #00ff00");
        $this->line("   Sidebar BG: #ff6600");

        // Test the actual controller method
        $this->line("\n🔧 Testing SettingsController::update():");
        try {
            $controller = new SettingsController();
            $response = $controller->update($request);
            
            $this->line("   Controller response: " . get_class($response));
            $this->line("   Status code: " . $response->getStatusCode());
            
        } catch (\Exception $e) {
            $this->line("   ❌ Controller error: " . $e->getMessage());
        }

        // Check if values were updated
        $this->line("\n🎨 Checking updated values:");
        $testKeys = [
            'primary_bg_color' => '#ff0000',
            'accent_color' => '#00ff00',
            'sidebar_bg_color' => '#ff6600',
        ];

        foreach ($testKeys as $key => $expectedValue) {
            $actualValue = SystemSetting::getValue($key);
            $match = ($actualValue === $expectedValue);
            $this->line("   {$key}: {$actualValue} " . ($match ? '✅' : '❌'));
        }

        // Test CSS generation with new values
        $this->line("\n📝 Testing CSS generation:");
        $themeService = new \App\Services\ThemeService();
        $css = $themeService->generateThemeCSS();
        $cssContainsRed = strpos($css, '#ff0000') !== false;
        $cssContainsGreen = strpos($css, '#00ff00') !== false;
        $cssContainsOrange = strpos($css, '#ff6600') !== false;
        
        $this->line("   CSS contains red (#ff0000): " . ($cssContainsRed ? '✅' : '❌'));
        $this->line("   CSS contains green (#00ff00): " . ($cssContainsGreen ? '✅' : '❌'));
        $this->line("   CSS contains orange (#ff6600): " . ($cssContainsOrange ? '✅' : '❌'));

        $this->info("\n✅ Theme form submission test completed!");
        return 0;
    }
}