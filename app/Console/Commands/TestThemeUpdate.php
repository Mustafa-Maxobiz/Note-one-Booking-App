<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class TestThemeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:theme-update {color} {value}';

    /**
     * The console command description.
     */
    protected $description = 'Test updating a theme color';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $color = $this->argument('color');
        $value = $this->argument('value');

        $this->info("Testing theme update: {$color} = {$value}");

        // Get current value
        $currentValue = SystemSetting::getValue($color);
        $this->line("Current value: {$currentValue}");

        // Update value
        $result = SystemSetting::setValue($color, $value, 'string', 'Theme color');
        $this->line("Update result: " . ($result ? 'Success' : 'Failed'));

        // Get updated value
        $updatedValue = SystemSetting::getValue($color);
        $this->line("Updated value: {$updatedValue}");

        // Verify
        if ($updatedValue === $value) {
            $this->info("✅ Theme update successful!");
        } else {
            $this->error("❌ Theme update failed!");
        }

        return 0;
    }
}
