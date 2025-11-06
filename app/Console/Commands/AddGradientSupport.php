<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class AddGradientSupport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'theme:add-gradient-support';

    /**
     * The console command description.
     */
    protected $description = 'Add gradient support to theme system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding gradient support to theme system...');

        // Add gradient settings
        $gradientSettings = [
            'brand_gradient' => 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)',
            'sidebar_gradient' => 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)',
            'navbar_gradient' => 'linear-gradient(135deg, #212529 0%, #343a40 100%)',
            'accent_gradient' => 'linear-gradient(135deg, #fdb838 0%, #ef473e 100%)',
        ];

        $this->line("\n🎨 Adding gradient settings:");
        foreach ($gradientSettings as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        // Store gradient settings
        $this->line("\n💾 Storing gradient settings...");
        foreach ($gradientSettings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Gradient setting - ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        // Add gradient type settings
        $gradientTypeSettings = [
            'use_gradients' => 'true',
            'gradient_type' => 'linear',
            'gradient_direction' => '135deg',
        ];

        $this->line("\n🎨 Adding gradient type settings:");
        foreach ($gradientTypeSettings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'description' => 'Gradient type setting - ' . now()->format('Y-m-d H:i:s')
                ]
            );
            $this->line("   ✅ {$key}: {$value}");
        }

        $this->info("\n✅ Gradient support added successfully!");
        $this->line("\n🎯 Gradient settings added:");
        $this->line("   - Brand gradient: linear-gradient(135deg, #fdb838 0%, #ef473e 100%)");
        $this->line("   - Sidebar gradient: Same as brand");
        $this->line("   - Navbar gradient: Dark gradient");
        $this->line("   - Accent gradient: Same as brand");
        $this->line("   - Gradient controls: Enable/disable, type, direction");

        return 0;
    }
}
