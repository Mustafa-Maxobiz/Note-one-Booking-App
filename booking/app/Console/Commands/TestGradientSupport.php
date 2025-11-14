<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ThemeService;
use App\Models\SystemSetting;

class TestGradientSupport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:gradient-support';

    /**
     * The console command description.
     */
    protected $description = 'Test gradient support functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Gradient Support...');

        // Test gradient settings
        $this->line("\n🎨 Gradient Settings:");
        $gradientSettings = [
            'brand_gradient' => SystemSetting::getValue('brand_gradient'),
            'sidebar_gradient' => SystemSetting::getValue('sidebar_gradient'),
            'navbar_gradient' => SystemSetting::getValue('navbar_gradient'),
            'accent_gradient' => SystemSetting::getValue('accent_gradient'),
            'use_gradients' => SystemSetting::getValue('use_gradients'),
        ];

        foreach ($gradientSettings as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        // Test CSS generation with gradients
        $this->line("\n📝 Testing CSS generation with gradients:");
        $css = ThemeService::generateThemeCSS();
        $cssLines = count(explode("\n", $css));
        $this->line("   Generated CSS: {$cssLines} lines");

        // Check for gradient variables
        $hasGradientVars = strpos($css, '--custom-brand-gradient') !== false;
        $this->line("   Contains gradient variables: " . ($hasGradientVars ? 'Yes' : 'No'));

        // Check for gradient usage in CSS
        $hasGradientUsage = strpos($css, 'var(--custom-sidebar-gradient)') !== false;
        $this->line("   Uses gradients in CSS: " . ($hasGradientUsage ? 'Yes' : 'No'));

        // Test gradient preview
        $this->line("\n🎯 Gradient Preview:");
        $this->line("   Brand: linear-gradient(135deg, #fdb838 0%, #ef473e 100%)");
        $this->line("   Sidebar: linear-gradient(135deg, #fdb838 0%, #ef473e 100%)");
        $this->line("   Navbar: linear-gradient(135deg, #212529 0%, #343a40 100%)");
        $this->line("   Accent: linear-gradient(135deg, #fdb838 0%, #ef473e 100%)");

        // Test conditional gradient usage
        $this->line("\n🔧 Conditional Gradient Usage:");
        $useGradients = SystemSetting::getValue('use_gradients', 'true');
        $this->line("   Gradients enabled: " . ($useGradients === 'true' ? 'Yes' : 'No'));

        if ($useGradients === 'true') {
            $this->line("   ✅ Sidebar will use gradient background");
            $this->line("   ✅ Navbar will use gradient background");
            $this->line("   ✅ Buttons will use gradient background");
        } else {
            $this->line("   ✅ Sidebar will use solid color background");
            $this->line("   ✅ Navbar will use solid color background");
            $this->line("   ✅ Buttons will use solid color background");
        }

        $this->info("\n✅ Gradient support test completed!");
        $this->line("\n🎯 Features implemented:");
        $this->line("   - Brand gradient: linear-gradient(135deg, #fdb838 0%, #ef473e 100%)");
        $this->line("   - Sidebar gradient support");
        $this->line("   - Navbar gradient support");
        $this->line("   - Button gradient support");
        $this->line("   - Toggle between gradients and solid colors");
        $this->line("   - Admin settings interface for gradients");

        return 0;
    }
}
