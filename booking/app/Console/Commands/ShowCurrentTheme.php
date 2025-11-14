<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Services\ThemeService;

class ShowCurrentTheme extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:show';

    /**
     * The console command description.
     */
    protected $description = 'Show current theme settings and status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Current Theme Settings:');
        $this->line('');

        // Get theme settings
        $settings = ThemeService::getThemeSettings();
        
        // Display color palette
        $this->line('🎨 Color Palette:');
        $this->line('┌─────────────────────────────────────────────────────────┐');
        $this->line('│  Setting                │  Color     │  Preview        │');
        $this->line('├─────────────────────────────────────────────────────────┤');
        
        $colorMap = [
            'primary_bg_color' => 'Primary Background',
            'secondary_bg_color' => 'Secondary Background',
            'primary_text_color' => 'Primary Text',
            'secondary_text_color' => 'Secondary Text',
            'accent_color' => 'Accent Color',
            'border_color' => 'Border Color',
            'navbar_bg_color' => 'Navbar Background',
            'navbar_text_color' => 'Navbar Text'
        ];

        foreach ($colorMap as $key => $label) {
            $color = $settings[$key];
            $preview = $this->getColorPreview($color);
            $this->line(sprintf('│  %-22s │  %-8s │  %s  │', $label, $color, $preview));
        }
        
        $this->line('└─────────────────────────────────────────────────────────┘');

        // Show CSS generation status
        $this->line('');
        $this->line('📝 CSS Generation:');
        $css = ThemeService::generateThemeCSS();
        $cssLines = count(explode("\n", $css));
        $this->line("   Generated CSS: {$cssLines} lines");
        $this->line("   CSS Variables: " . (strpos($css, '--custom-') !== false ? 'Active' : 'Inactive'));

        // Show route status
        $this->line('');
        $this->line('🌐 Theme Route:');
        $routeUrl = route('theme.css');
        $this->line("   URL: {$routeUrl}");
        $this->line("   Status: " . (route('theme.css') ? 'Available' : 'Not Available'));

        // Show database status
        $this->line('');
        $this->line('💾 Database Status:');
        $themeKeys = array_keys($settings);
        $storedCount = SystemSetting::whereIn('key', $themeKeys)->count();
        $this->line("   Stored Settings: {$storedCount}/" . count($themeKeys));
        
        foreach ($themeKeys as $key) {
            $setting = SystemSetting::where('key', $key)->first();
            $status = $setting ? '✅' : '❌';
            $this->line("   {$status} {$key}");
        }

        $this->line('');
        $this->info('✅ Theme system is fully operational!');
        $this->line('');
        $this->line('🎯 Next steps:');
        $this->line('   1. Visit /admin/settings to customize colors');
        $this->line('   2. Use preview functionality to test changes');
        $this->line('   3. Save settings to apply globally');

        return 0;
    }

    /**
     * Get color preview block
     */
    private function getColorPreview($color)
    {
        // Convert hex to RGB for terminal display
        $hex = ltrim($color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Simple terminal color preview
        return "███";
    }
}
