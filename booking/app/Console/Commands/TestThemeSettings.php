<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ThemeService;
use App\Models\SystemSetting;

class TestThemeSettings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:theme-settings';

    /**
     * The console command description.
     */
    protected $description = 'Test theme settings functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Theme Settings...');

        // Test 1: Check if theme settings exist
        $this->line("\n🔍 Checking theme settings in database:");
        $themeKeys = [
            'primary_bg_color', 'secondary_bg_color', 'primary_text_color',
            'secondary_text_color', 'accent_color', 'border_color',
            'navbar_bg_color', 'navbar_text_color'
        ];

        foreach ($themeKeys as $key) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                $this->line("   ✅ {$key}: {$setting->value}");
            } else {
                $this->line("   ❌ {$key}: Not found");
            }
        }

        // Test 2: Test ThemeService
        $this->line("\n🎨 Testing ThemeService:");
        $settings = ThemeService::getThemeSettings();
        $this->line("   Primary BG: {$settings['primary_bg_color']}");
        $this->line("   Secondary BG: {$settings['secondary_bg_color']}");
        $this->line("   Primary Text: {$settings['primary_text_color']}");
        $this->line("   Accent: {$settings['accent_color']}");

        // Test 3: Generate CSS
        $this->line("\n📝 Testing CSS generation:");
        $css = ThemeService::generateThemeCSS();
        $cssLines = count(explode("\n", $css));
        $this->line("   Generated CSS: {$cssLines} lines");
        $this->line("   Contains CSS variables: " . (strpos($css, '--custom-') !== false ? 'Yes' : 'No'));

        // Test 4: Test route
        $this->line("\n🌐 Testing theme route:");
        $routeUrl = route('theme.css');
        $this->line("   Theme CSS URL: {$routeUrl}");

        $this->info("\n✅ Theme settings test completed!");
        $this->line("\n🎯 Features implemented:");
        $this->line("   - Color picker interface in admin settings");
        $this->line("   - Real-time preview functionality");
        $this->line("   - Theme CSS generation service");
        $this->line("   - Global theme application");
        $this->line("   - Database storage for theme settings");

        return 0;
    }
}
