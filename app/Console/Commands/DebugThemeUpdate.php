<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class DebugThemeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'debug:theme-update';

    /**
     * The console command description.
     */
    protected $description = 'Debug theme settings update functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging theme settings update...');

        // Test setting a value
        $this->line("\n🔧 Testing setValue method:");
        $testKey = 'test_theme_color';
        $testValue = '#ff0000';
        
        $result = SystemSetting::setValue($testKey, $testValue, 'string', 'Test color');
        $this->line("   setValue result: " . ($result ? 'Success' : 'Failed'));
        
        // Test getting the value
        $retrievedValue = SystemSetting::getValue($testKey);
        $this->line("   Retrieved value: {$retrievedValue}");
        $this->line("   Values match: " . ($retrievedValue === $testValue ? 'Yes' : 'No'));

        // Test updating existing value
        $this->line("\n🔄 Testing update functionality:");
        $newValue = '#00ff00';
        $updateResult = SystemSetting::setValue($testKey, $newValue, 'string', 'Updated test color');
        $this->line("   Update result: " . ($updateResult ? 'Success' : 'Failed'));
        
        $updatedValue = SystemSetting::getValue($testKey);
        $this->line("   Updated value: {$updatedValue}");
        $this->line("   Update successful: " . ($updatedValue === $newValue ? 'Yes' : 'No'));

        // Test theme-specific settings
        $this->line("\n🎨 Testing theme settings:");
        $themeSettings = [
            'primary_bg_color' => '#f0f0f0',
            'accent_color' => '#ff6600',
            'use_gradients' => 'false',
        ];

        foreach ($themeSettings as $key => $value) {
            $this->line("   Setting {$key}: {$value}");
            $result = SystemSetting::setValue($key, $value, 'string', 'Theme setting');
            $retrieved = SystemSetting::getValue($key);
            $this->line("   Result: " . ($result ? 'Success' : 'Failed') . " | Retrieved: {$retrieved}");
        }

        // Check database records
        $this->line("\n📊 Database records:");
        $allSettings = SystemSetting::all();
        $this->line("   Total settings: " . $allSettings->count());
        
        foreach ($allSettings as $setting) {
            $this->line("   {$setting->key}: {$setting->value}");
        }

        // Clean up test data
        SystemSetting::where('key', $testKey)->delete();
        $this->line("\n🧹 Cleaned up test data");

        $this->info("\n✅ Debug completed!");
        return 0;
    }
}
