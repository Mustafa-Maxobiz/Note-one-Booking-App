<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ThemeService;

class TestThemeCSS extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:theme-css';

    /**
     * The console command description.
     */
    protected $description = 'Test theme CSS generation and serving';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing theme CSS...');

        // Test CSS generation
        $this->line("\n📝 Testing CSS generation:");
        $css = ThemeService::generateThemeCSS();
        $cssLines = count(explode("\n", $css));
        $this->line("   Generated CSS: {$cssLines} lines");

        // Check for key CSS elements
        $checks = [
            'CSS Variables' => strpos($css, '--custom-') !== false,
            'Body Styles' => strpos($css, 'body {') !== false,
            'Sidebar Styles' => strpos($css, '.sidebar {') !== false,
            'Navbar Styles' => strpos($css, '.navbar {') !== false,
            'Button Styles' => strpos($css, '.btn-primary {') !== false,
            'Gradient Support' => strpos($css, '--custom-brand-gradient') !== false,
        ];

        foreach ($checks as $check => $result) {
            $this->line("   {$check}: " . ($result ? '✅' : '❌'));
        }

        // Test theme route
        $this->line("\n🌐 Testing theme route:");
        $routeUrl = route('theme.css');
        $this->line("   Route URL: {$routeUrl}");

        // Test if route is accessible
        try {
            $response = \Illuminate\Support\Facades\Http::get($routeUrl);
            $this->line("   HTTP Status: " . $response->status());
            $this->line("   Content Type: " . $response->header('Content-Type'));
            $this->line("   Content Length: " . strlen($response->body()) . " bytes");
        } catch (\Exception $e) {
            $this->line("   ❌ Route test failed: " . $e->getMessage());
        }

        // Show sample CSS
        $this->line("\n📄 Sample CSS (first 10 lines):");
        $cssLines = explode("\n", $css);
        for ($i = 0; $i < min(10, count($cssLines)); $i++) {
            $this->line("   " . $cssLines[$i]);
        }

        $this->info("\n✅ Theme CSS test completed!");
        return 0;
    }
}
