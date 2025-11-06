<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class CaptureCurrentTheme extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:capture-current';

    /**
     * The console command description.
     */
    protected $description = 'Capture current color scheme and store as theme settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Capturing current color scheme...');

        // Current Bootstrap 5.3.0 default color scheme
        $currentColors = [
            'primary_bg_color' => '#f8f9fa',        // Bootstrap light background
            'secondary_bg_color' => '#ffffff',     // Bootstrap white
            'primary_text_color' => '#212529',     // Bootstrap dark text
            'secondary_text_color' => '#6c757d',   // Bootstrap muted text
            'accent_color' => '#0d6efd',          // Bootstrap primary blue
            'border_color' => '#dee2e6',          // Bootstrap border color
            'navbar_bg_color' => '#212529',       // Bootstrap dark navbar
            'navbar_text_color' => '#ffffff',    // White text on dark navbar
        ];

        $this->line("\n🎨 Current Bootstrap 5.3.0 Color Scheme:");
        foreach ($currentColors as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        // Store the colors
        $this->line("\n💾 Storing current colors...");
        foreach ($currentColors as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Current Bootstrap color scheme - captured on ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        // Verify storage
        $this->line("\n🔍 Verifying stored settings:");
        foreach ($currentColors as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting && $setting->value === $value) {
                $this->line("   ✅ {$key}: {$setting->value}");
            } else {
                $this->line("   ❌ {$key}: Failed to store");
            }
        }

        $this->info("\n✅ Current color scheme captured and stored successfully!");
        $this->line("\n🎯 Next steps:");
        $this->line("   1. Visit /admin/settings to see the captured colors");
        $this->line("   2. Use the color pickers to customize as needed");
        $this->line("   3. Preview changes before saving");
        $this->line("   4. Save settings to apply globally");

        return 0;
    }
}
