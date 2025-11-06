<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ThemeService;

class TestThemeRoute extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:theme-route';

    /**
     * The console command description.
     */
    protected $description = 'Test theme route directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing theme route directly...');

        try {
            // Test ThemeService method directly
            $this->line("\n🔧 Testing ThemeService::getThemeCSSFile():");
            $response = ThemeService::getThemeCSSFile();
            $this->line("   Response type: " . get_class($response));
            $this->line("   Status code: " . $response->getStatusCode());
            $this->line("   Content type: " . $response->headers->get('Content-Type'));
            $this->line("   Content length: " . strlen($response->getContent()) . " bytes");

            // Test CSS generation
            $this->line("\n📝 Testing CSS generation:");
            $css = ThemeService::generateThemeCSS();
            $this->line("   CSS length: " . strlen($css) . " characters");
            $this->line("   CSS lines: " . count(explode("\n", $css)));

            // Test route URL generation
            $this->line("\n🌐 Testing route URL:");
            $url = route('theme.css');
            $this->line("   Route URL: {$url}");

            // Test if we can access the route
            $this->line("\n🔍 Testing route access:");
            $this->line("   Route exists: " . (route('theme.css') ? 'Yes' : 'No'));

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->line("   File: " . $e->getFile());
            $this->line("   Line: " . $e->getLine());
        }

        $this->info("\n✅ Theme route test completed!");
        return 0;
    }
}
