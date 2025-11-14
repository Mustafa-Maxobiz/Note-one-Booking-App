<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshTheme extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:refresh';

    /**
     * The console command description.
     */
    protected $description = 'Refresh theme by clearing caches and regenerating CSS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Refreshing theme system...');

        // Clear all caches
        $this->line("\n🧹 Clearing caches...");
        Artisan::call('config:clear');
        $this->line("   ✅ Config cache cleared");
        
        Artisan::call('route:clear');
        $this->line("   ✅ Route cache cleared");
        
        Artisan::call('view:clear');
        $this->line("   ✅ View cache cleared");
        
        Artisan::call('cache:clear');
        $this->line("   ✅ Application cache cleared");

        // Regenerate theme CSS
        $this->line("\n🎨 Regenerating theme CSS...");
        $themeService = new \App\Services\ThemeService();
        $css = $themeService->generateThemeCSS();
        $this->line("   ✅ CSS regenerated (" . count(explode("\n", $css)) . " lines)");

        // Test theme route
        $this->line("\n🌐 Testing theme route...");
        $routeUrl = route('theme.css');
        $this->line("   Theme URL: {$routeUrl}");

        // Show current settings
        $this->line("\n📊 Current theme settings:");
        $settings = [
            'primary_bg_color' => \App\Models\SystemSetting::getValue('primary_bg_color'),
            'secondary_bg_color' => \App\Models\SystemSetting::getValue('secondary_bg_color'),
            'accent_color' => \App\Models\SystemSetting::getValue('accent_color'),
            'sidebar_bg_color' => \App\Models\SystemSetting::getValue('sidebar_bg_color'),
            'use_gradients' => \App\Models\SystemSetting::getValue('use_gradients'),
        ];

        foreach ($settings as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        $this->info("\n✅ Theme refresh completed!");
        $this->line("\n🎯 Next steps:");
        $this->line("   1. Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)");
        $this->line("   2. Check if theme changes are visible");
        $this->line("   3. If not, try clearing browser cache");

        return 0;
    }
}
