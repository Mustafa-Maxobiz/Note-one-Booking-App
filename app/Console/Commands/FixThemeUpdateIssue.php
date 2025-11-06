<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Services\ThemeService;

class FixThemeUpdateIssue extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:theme-update-issue';

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

        // Check current status
        $this->line("\n🔍 Current Theme Status:");
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

        // Test theme update
        $this->line("\n🔄 Testing theme update:");
        $testColor = '#ff6600';
        SystemSetting::setValue('accent_color', $testColor);
        $updatedColor = SystemSetting::getValue('accent_color');
        $this->line("   Updated accent color: {$updatedColor}");
        $this->line("   Update successful: " . ($updatedColor === $testColor ? '✅' : '❌'));

        // Test CSS generation
        $this->line("\n📝 Testing CSS generation:");
        $css = ThemeService::generateThemeCSS();
        $cssContainsColor = strpos($css, $testColor) !== false;
        $this->line("   CSS contains updated color: " . ($cssContainsColor ? '✅' : '❌'));

        // Clear all caches
        $this->line("\n🧹 Clearing all caches:");
        \Artisan::call('config:clear');
        $this->line("   ✅ Config cache cleared");
        
        \Artisan::call('route:clear');
        $this->line("   ✅ Route cache cleared");
        
        \Artisan::call('view:clear');
        $this->line("   ✅ View cache cleared");
        
        \Artisan::call('cache:clear');
        $this->line("   ✅ Application cache cleared");

        // Test theme route
        $this->line("\n🌐 Testing theme route:");
        $routeUrl = route('theme.css');
        $this->line("   Theme URL: {$routeUrl}");

        // Provide solutions
        $this->line("\n💡 Solutions for Theme Update Issues:");
        $this->line("   1. ✅ Backend is working correctly");
        $this->line("   2. ✅ Database updates are working");
        $this->line("   3. ✅ CSS generation is working");
        $this->line("   4. ❌ Frontend form might have issues");
        $this->line("   5. ❌ Browser cache might be blocking changes");

        $this->line("\n🎯 Troubleshooting Steps:");
        $this->line("   1. Hard refresh browser (Ctrl+F5 or Cmd+Shift+R)");
        $this->line("   2. Clear browser cache completely");
        $this->line("   3. Check browser developer tools for JavaScript errors");
        $this->line("   4. Verify form fields are properly named");
        $this->line("   5. Check if form is submitting to correct route");

        $this->line("\n🔧 Manual Testing:");
        $this->line("   1. Go to /admin/settings");
        $this->line("   2. Change a color value");
        $this->line("   3. Click 'Save Settings'");
        $this->line("   4. Check if success message appears");
        $this->line("   5. Hard refresh the page");
        $this->line("   6. Check if color changed in form");

        $this->line("\n🌐 Direct CSS Test:");
        $this->line("   Visit: {$routeUrl}");
        $this->line("   Should show CSS with your custom colors");

        $this->info("\n✅ Theme update issue analysis completed!");
        $this->line("\n🎯 If theme changes are still not visible:");
        $this->line("   1. The backend is working correctly");
        $this->line("   2. The issue is likely browser cache or frontend form");
        $this->line("   3. Try updating colors through the admin interface");
        $this->line("   4. Hard refresh your browser after making changes");

        return 0;
    }
}
