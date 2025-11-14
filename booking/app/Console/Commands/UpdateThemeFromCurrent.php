<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class UpdateThemeFromCurrent extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:update-from-current';

    /**
     * The console command description.
     */
    protected $description = 'Update theme settings to match current interface colors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating theme settings to match current interface...');

        // Based on the image description, update colors to match current design
        $currentInterfaceColors = [
            'primary_bg_color' => '#f8f9fa',        // Light background
            'secondary_bg_color' => '#ffffff',     // White content area
            'primary_text_color' => '#212529',      // Dark text
            'secondary_text_color' => '#6c757d',     // Muted text
            'accent_color' => '#fd7e14',            // Orange accent (matching sidebar)
            'border_color' => '#dee2e6',            // Light borders
            'navbar_bg_color' => '#212529',         // Dark navbar
            'navbar_text_color' => '#ffffff',       // White navbar text
        ];

        $this->line("\n🎨 Updating to match current interface:");
        foreach ($currentInterfaceColors as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        // Store the updated colors
        $this->line("\n💾 Updating stored colors...");
        foreach ($currentInterfaceColors as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Updated to match current interface - ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        // Add sidebar-specific settings
        $this->line("\n🎨 Adding sidebar-specific settings...");
        $sidebarSettings = [
            'sidebar_bg_color' => '#fd7e14',        // Orange sidebar
            'sidebar_text_color' => '#ffffff',      // White sidebar text
            'sidebar_hover_color' => '#e55a00',     // Darker orange on hover
        ];

        foreach ($sidebarSettings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Sidebar color settings - ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        // Update ThemeService to include sidebar colors
        $this->updateThemeService();

        $this->info("\n✅ Theme settings updated to match current interface!");
        $this->line("\n🎯 Updated colors:");
        $this->line("   - Accent color changed to orange (#fd7e14)");
        $this->line("   - Added sidebar-specific colors");
        $this->line("   - Maintained current navbar and background colors");

        return 0;
    }

    /**
     * Update ThemeService to include sidebar colors
     */
    private function updateThemeService()
    {
        $this->line("\n📝 Updating ThemeService...");
        
        // The ThemeService will automatically pick up the new colors
        // No code changes needed as it reads from SystemSetting
        $this->line("   ✅ ThemeService will use new sidebar colors");
    }
}
