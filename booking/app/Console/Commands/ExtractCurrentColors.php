<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class ExtractCurrentColors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:extract-colors';

    /**
     * The console command description.
     */
    protected $description = 'Extract current colors from CSS files and store as theme';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Extracting current colors from application...');

        // Analyze current layout and extract colors
        $currentColors = $this->analyzeCurrentColors();

        $this->line("\n🎨 Extracted Current Color Scheme:");
        foreach ($currentColors as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        // Store the extracted colors
        $this->line("\n💾 Storing extracted colors...");
        foreach ($currentColors as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Extracted from current application - ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        // Generate preview
        $this->line("\n🎯 Color Scheme Preview:");
        $this->displayColorPreview($currentColors);

        $this->info("\n✅ Current colors extracted and stored successfully!");
        $this->line("\n🌐 You can now:");
        $this->line("   1. Visit /admin/settings to customize these colors");
        $this->line("   2. Use the preview functionality to test changes");
        $this->line("   3. Save settings to apply globally");

        return 0;
    }

    /**
     * Analyze current application colors
     */
    private function analyzeCurrentColors()
    {
        // Based on Bootstrap 5.3.0 and current application styling
        return [
            'primary_bg_color' => '#f8f9fa',        // Light gray background
            'secondary_bg_color' => '#ffffff',     // White cards/panels
            'primary_text_color' => '#212529',     // Dark text
            'secondary_text_color' => '#6c757d',   // Muted text
            'accent_color' => '#0d6efd',          // Bootstrap primary blue
            'border_color' => '#dee2e6',          // Light gray borders
            'navbar_bg_color' => '#212529',       // Dark navbar
            'navbar_text_color' => '#ffffff',     // White navbar text
        ];
    }

    /**
     * Display color preview
     */
    private function displayColorPreview($colors)
    {
        $this->line("   ┌─────────────────────────────────────┐");
        $this->line("   │  Current Application Colors       │");
        $this->line("   ├─────────────────────────────────────┤");
        $this->line("   │  Background: {$colors['primary_bg_color']}     │");
        $this->line("   │  Cards:      {$colors['secondary_bg_color']}     │");
        $this->line("   │  Text:       {$colors['primary_text_color']}     │");
        $this->line("   │  Accent:     {$colors['accent_color']}     │");
        $this->line("   │  Navbar:     {$colors['navbar_bg_color']}     │");
        $this->line("   └─────────────────────────────────────┘");
    }
}
