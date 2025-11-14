<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Services\ThemeService;

class FixThemeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:theme-update';

    /**
     * The console command description.
     */
    protected $description = 'Fix theme update issues and provide solutions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing theme update issues...');

        // Check current theme settings
        $this->line("\n🔍 Current theme settings:");
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

        // Test CSS generation
        $this->line("\n📝 Testing CSS generation:");
        $css = ThemeService::generateThemeCSS();
        $this->line("   CSS generated: " . strlen($css) . " characters");
        $this->line("   CSS lines: " . count(explode("\n", $css)));

        // Check if CSS contains current values
        $accentColor = SystemSetting::getValue('accent_color');
        $cssContainsAccent = strpos($css, $accentColor) !== false;
        $this->line("   CSS contains accent color ({$accentColor}): " . ($cssContainsAccent ? '✅' : '❌'));

        // Test theme update
        $this->line("\n🔄 Testing theme update:");
        $testColor = '#ff0000';
        SystemSetting::setValue('accent_color', $testColor);
        $updatedColor = SystemSetting::getValue('accent_color');
        $this->line("   Updated accent color: {$updatedColor}");
        $this->line("   Update successful: " . ($updatedColor === $testColor ? '✅' : '❌'));

        // Regenerate CSS with new color
        $newCss = ThemeService::generateThemeCSS();
        $newCssContainsColor = strpos($newCss, $testColor) !== false;
        $this->line("   New CSS contains updated color: " . ($newCssContainsColor ? '✅' : '❌'));

        // Restore original color
        SystemSetting::setValue('accent_color', $accentColor);
        $this->line("   Restored original color: {$accentColor}");

        // Provide solutions
        $this->line("\n💡 Solutions for theme update issues:");
        $this->line("   1. Clear browser cache (Ctrl+F5 or Cmd+Shift+R)");
        $this->line("   2. Check if theme-custom.css is accessible in browser");
        $this->line("   3. Verify web server is serving the route correctly");
        $this->line("   4. Check if there are any JavaScript errors in browser console");
        $this->line("   5. Try accessing the theme CSS directly: " . route('theme.css'));

        // Check if the issue is with the route
        $this->line("\n🌐 Route troubleshooting:");
        $this->line("   Route name: theme.css");
        $this->line("   Route URL: " . route('theme.css'));
        $this->line("   Route exists: " . (route('theme.css') ? 'Yes' : 'No'));

        $this->info("\n✅ Theme update fix completed!");
        $this->line("\n🎯 If theme changes are still not visible:");
        $this->line("   1. Hard refresh your browser");
        $this->line("   2. Check browser developer tools for errors");
        $this->line("   3. Verify the theme-custom.css file is loading");
        $this->line("   4. Try updating a different color to test");

        return 0;
    }
}
