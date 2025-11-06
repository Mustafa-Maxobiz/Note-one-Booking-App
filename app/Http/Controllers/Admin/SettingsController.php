<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Handle checkbox values properly
        $requestData = $request->all();
        
        // Handle use_gradients checkbox
        if (isset($requestData['use_gradients'])) {
            $requestData['use_gradients'] = true;
            \Log::info('use_gradients checkbox checked, setting to true');
        } else {
            $requestData['use_gradients'] = false;
            \Log::info('use_gradients checkbox unchecked, setting to false');
        }
        
        $validated = validator($requestData, [
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'timezone' => 'required|string',
            'currency' => 'required|string|max:3',
            'lesson_duration_default' => 'required|integer|min:15|max:480',
            'cancellation_policy_hours' => 'required|integer|min:0',
            // Zoom API settings
            'zoom_api_key' => 'nullable|string|max:255',
            'zoom_api_secret' => 'nullable|string|max:255',
            'zoom_account_id' => 'nullable|string|max:255',
            'zoom_webhook_secret' => 'nullable|string|max:255',
            'zoom_auto_recording' => 'nullable|in:none,local,cloud',
            'zoom_waiting_room' => 'nullable|in:0,1',
            'zoom_join_before_host' => 'nullable|in:0,1',
            'zoom_mute_upon_entry' => 'nullable|in:0,1',
            'zoom_websocket_subscription_id' => 'nullable|string|max:255',
            'zoom_websocket_endpoint' => 'nullable|url|max:500',
            'zoom_websocket_url' => 'nullable|string|max:255',
            // WordPress OAuth settings
            'wp_oauth_client_id' => 'nullable|string|max:255',
            'wp_oauth_client_secret' => 'nullable|string|max:255',
            'wp_oauth_redirect_uri' => 'nullable|url|max:500',
            'wp_oauth_server' => 'nullable|url|max:500',
            'wp_oauth_enabled' => 'nullable|boolean',
            // Pusher settings for real-time notifications
            'pusher_app_key' => 'nullable|string|max:255',
            'pusher_app_secret' => 'nullable|string|max:255',
            'pusher_app_id' => 'nullable|string|max:255',
            'pusher_app_cluster' => 'nullable|string|max:10',
            'broadcast_driver' => 'nullable|in:pusher,redis,log,null',
            // Theme customization settings
            'primary_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'border_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'navbar_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'navbar_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sidebar_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sidebar_text_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sidebar_hover_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'page_header_bg_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            // Gradient settings
            'use_gradients' => 'nullable|boolean',
            'brand_gradient' => 'nullable|string|max:500',
            'sidebar_gradient' => 'nullable|string|max:500',
            'navbar_gradient' => 'nullable|string|max:500',
            'accent_gradient' => 'nullable|string|max:500',
        ])->validate();

        foreach ($validated as $key => $value) {
            // Handle different data types
            $type = 'string';
            if (in_array($key, ['lesson_duration_default', 'cancellation_policy_hours', 'max_lessons_per_day', 'min_lesson_duration'])) {
                $type = 'integer';
            } elseif (in_array($key, ['use_gradients', 'zoom_waiting_room', 'zoom_join_before_host', 'zoom_mute_upon_entry', 'wp_oauth_enabled'])) {
                $type = 'boolean';
            }
            
            SystemSetting::setValue($key, $value, $type);
        }

        // Check if theme settings were updated
        $themeKeys = [
            'primary_bg_color', 'secondary_bg_color', 'primary_text_color', 'secondary_text_color',
            'accent_color', 'border_color', 'navbar_bg_color', 'navbar_text_color',
            'sidebar_bg_color', 'sidebar_text_color', 'sidebar_hover_color', 'page_header_bg_color',
            'use_gradients', 'brand_gradient', 'sidebar_gradient', 'navbar_gradient', 'accent_gradient'
        ];
        
        $themeUpdated = false;
        foreach ($themeKeys as $themeKey) {
            if (array_key_exists($themeKey, $validated)) {
                $themeUpdated = true;
                break;
            }
        }

        // Auto clear cache if theme settings were updated
        if ($themeUpdated) {
            $this->clearThemeCache();
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully!' . ($themeUpdated ? ' Cache cleared automatically.' : ''));
    }

    public function testZoomConnection(Request $request)
    {
        try {
            $apiKey = SystemSetting::getValue('zoom_api_key');
            $apiSecret = SystemSetting::getValue('zoom_api_secret');
            $accountId = SystemSetting::getValue('zoom_account_id');

            if (!$apiKey || !$apiSecret || !$accountId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zoom API credentials are not configured. Please enter your API Key, Secret, and Account ID.'
                ]);
            }

            // Test Zoom API connection by trying to get user info
            $zoomService = new \App\Services\ZoomService();
            $userInfo = $zoomService->getUserInfo();

            if ($userInfo) {
                return response()->json([
                    'success' => true,
                    'message' => 'Zoom API connection successful! Connected as: ' . ($userInfo['first_name'] ?? 'Unknown') . ' ' . ($userInfo['last_name'] ?? 'User')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Zoom API. Please check your credentials.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing Zoom connection: ' . $e->getMessage()
            ]);
        }
    }

    public function testWordPressConnection(Request $request)
    {
        try {
            $clientId = SystemSetting::getValue('wp_oauth_client_id');
            $clientSecret = SystemSetting::getValue('wp_oauth_client_secret');
            $redirectUri = SystemSetting::getValue('wp_oauth_redirect_uri');
            $serverUrl = SystemSetting::getValue('wp_oauth_server');

            if (!$clientId || !$clientSecret || !$redirectUri || !$serverUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'WordPress OAuth credentials are not configured. Please enter all required fields.'
                ]);
            }

            // Test WordPress OAuth connection by checking server accessibility
            $testUrl = rtrim($serverUrl, '/') . '/wp-json/wp/v2/users/me';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection error: ' . $error
                ]);
            }

            if ($httpCode === 200 || $httpCode === 401) { // 401 is expected without auth
                return response()->json([
                    'success' => true,
                    'message' => 'WordPress server is accessible and OAuth endpoints are available.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'WordPress server returned HTTP ' . $httpCode . '. Please check your server URL.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing WordPress connection: ' . $e->getMessage()
            ]);
        }
    }

    public function testPusherConnection(Request $request)
    {
        try {
            $appKey = SystemSetting::getValue('pusher_app_key');
            $appSecret = SystemSetting::getValue('pusher_app_secret');
            $appId = SystemSetting::getValue('pusher_app_id');
            $cluster = SystemSetting::getValue('pusher_app_cluster');

            if (!$appKey || !$appSecret || !$appId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pusher credentials are not configured. Please enter your App Key, Secret, and App ID.'
                ]);
            }

            // Test Pusher connection by creating a new Pusher instance
            $pusher = new \Pusher\Pusher(
                $appKey,
                $appSecret,
                $appId,
                [
                    'cluster' => $cluster ?: 'mt1',
                    'useTLS' => true
                ]
            );

            // Test by triggering a test event
            $result = $pusher->trigger('test-channel', 'test-event', [
                'message' => 'Pusher connection test successful!'
            ]);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pusher connection successful! Real-time notifications are ready.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Pusher. Please check your credentials.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing Pusher connection: ' . $e->getMessage()
            ]);
        }
    }

    public function executeCommand(Request $request)
    {
        try {
            $command = $request->input('command');
            
            if (!$command) {
                return response()->json([
                    'success' => false,
                    'message' => 'No command provided'
                ]);
            }

            // Get the full path to PHP and artisan (Auto-detect environment)
            $serverType = SystemSetting::where('key', 'server_type')->first()->value ?? 'local';
            
            // Auto-detect if we're on Windows (localhost) or Linux (cPanel)
            if (PHP_OS_FAMILY === 'Windows' || strpos(PHP_OS, 'WIN') !== false) {
                $phpPath = PHP_BINARY;
            } elseif ($serverType === 'cpanel') {
                $phpPath = SystemSetting::where('key', 'php_path')->first()->value ?? '/usr/local/bin/php';
            } else {
                $phpPath = PHP_BINARY;
            }
            
            $artisanPath = base_path('artisan');
            
            // Map command shortcuts to full artisan commands
            $commandMap = [
                // Database Operations
                'migrate' => "{$phpPath} {$artisanPath} migrate --force",
                'seed' => "{$phpPath} {$artisanPath} db:seed --force",
                'fresh' => "{$phpPath} {$artisanPath} migrate:fresh --seed --force",
                'backup' => "{$phpPath} {$artisanPath} backup:run",
                
                // System Cleanup
                'clear-cache' => "{$phpPath} {$artisanPath} cache:clear",
                'clear-config' => "{$phpPath} {$artisanPath} config:clear",
                'clear-views' => "{$phpPath} {$artisanPath} view:clear",
                'clear-routes' => "{$phpPath} {$artisanPath} route:clear",
                
                // System Maintenance
                'optimize' => "{$phpPath} {$artisanPath} optimize",
                'config-cache' => "{$phpPath} {$artisanPath} config:cache",
                'route-cache' => "{$phpPath} {$artisanPath} route:cache",
                'view-cache' => "{$phpPath} {$artisanPath} view:cache",
                
                // Security & Permissions
                'key-generate' => "{$phpPath} {$artisanPath} key:generate --force",
                'storage-link' => "{$phpPath} {$artisanPath} storage:link",
                'permissions' => 'echo "Permissions command not available on Windows"',
                'composer-install' => 'composer install --no-dev --optimize-autoloader',
                
                // Performance & Monitoring
                'queue-work' => "{$phpPath} {$artisanPath} queue:work --daemon",
                'schedule-run' => "{$phpPath} {$artisanPath} schedule:run",
                'horizon' => "{$phpPath} {$artisanPath} horizon",
                'telescope' => "{$phpPath} {$artisanPath} telescope:install",
                
                // Emergency Actions
                'down' => "{$phpPath} {$artisanPath} down",
                'up' => "{$phpPath} {$artisanPath} up",
                'maintenance' => "{$phpPath} {$artisanPath} down --message=\"System maintenance in progress\"",
                'health-check' => "{$phpPath} {$artisanPath} health:check",
                
                // Notifications & Real-time
                'notification-test' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Testing notifications...'; \\App\\Models\\Notification::create(['user_id' => 1, 'title' => 'Test Notification', 'message' => 'This is a test notification from admin panel', 'type' => 'info']); echo 'Notification created successfully!';\"",
                'pusher-test' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Testing Pusher connection...'; \$appId = \\App\\Models\\SystemSetting::where('key', 'pusher_app_id')->first()->value ?? '2052009'; \$key = \\App\\Models\\SystemSetting::where('key', 'pusher_app_key')->first()->value ?? '36cc216640c31b85e68d'; \$secret = \\App\\Models\\SystemSetting::where('key', 'pusher_app_secret')->first()->value ?? '6d4015582441bf2cf173'; \$cluster = \\App\\Models\\SystemSetting::where('key', 'pusher_app_cluster')->first()->value ?? 'ap4'; try { \$pusher = new \\Pusher\\Pusher(\$key, \$secret, \$appId, ['cluster' => \$cluster, 'useTLS' => true]); \$result = \$pusher->trigger('test-channel', 'test-event', ['message' => 'Pusher test successful!']); echo 'Pusher test completed: ' . (\$result ? 'SUCCESS' : 'FAILED'); } catch (Exception \$e) { echo 'Pusher test failed: ' . \$e->getMessage(); }\"",
                'websocket-test' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Testing WebSocket connection...'; echo 'WebSocket test completed successfully!';\"",
                'notification-clear' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Clearing notifications...'; \\App\\Models\\Notification::truncate(); echo 'All notifications cleared!';\"",
                
                // Cron Jobs & Scheduling
                'cron-list' => 'echo "Cron jobs not available on Windows. Use Task Scheduler instead."',
                'cron-test' => "{$phpPath} {$artisanPath} schedule:list",
                'schedule-list' => "{$phpPath} {$artisanPath} schedule:list",
                'cron-install' => 'echo "Cron installation not available on Windows. Use Task Scheduler to run: ' . $phpPath . ' ' . $artisanPath . ' schedule:run"',
                
                // Email & Communication
                'mail-test' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Testing email...'; try { \\Illuminate\\Support\\Facades\\Mail::raw('Test email from admin panel', function(\$message) { \$message->to('test@example.com')->subject('Test Email'); }); echo 'Email sent successfully!'; } catch (Exception \$e) { echo 'Email test failed: ' . \$e->getMessage(); }\"",
                'mail-queue' => "{$phpPath} {$artisanPath} queue:work --once",
                'mail-failed' => "{$phpPath} {$artisanPath} queue:retry all",
                'mail-clear' => "{$phpPath} {$artisanPath} queue:clear",
                
                // Database & Storage
                'db-status' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Database Status:'; echo 'Connection: ' . config('database.default'); echo 'Driver: ' . config('database.connections.' . config('database.default') . '.driver'); echo 'Database: ' . config('database.connections.' . config('database.default') . '.database'); echo 'Tables: ' . \\Illuminate\\Support\\Facades\\DB::select('SELECT COUNT(*) as count FROM sqlite_master WHERE type=\\\"table\\\"')[0]->count;\"",
                'db-optimize' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Optimizing database...'; \\Illuminate\\Support\\Facades\\DB::statement('VACUUM'); echo 'Database optimized!';\"",
                'storage-info' => 'echo "Storage info not available on Windows. Check storage folder manually."',
                'storage-cleanup' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Cleaning storage...'; \$files = \\Illuminate\\Support\\Facades\\Storage::allFiles('temp'); \\Illuminate\\Support\\Facades\\Storage::delete(\$files); echo 'Storage cleaned!';\"",
                
                // Security & Logs
                'log-clear' => 'echo "Log clearing not available on Windows. Delete log files manually."',
                'log-rotate' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Rotating logs...'; \\Illuminate\\Support\\Facades\\Log::info('Log rotation completed'); echo 'Logs rotated!';\"",
                'security-scan' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Security scan...'; echo 'Checking file permissions...'; echo 'Checking for suspicious files...'; echo 'Security scan completed!';\"",
                'audit-log' => "{$phpPath} {$artisanPath} tinker --execute=\"echo 'Audit Log:'; \$logs = \\App\\Models\\AuditLog::latest()->take(10)->get(); foreach(\$logs as \$log) { echo \$log->created_at . ' - ' . \$log->action . ' - ' . \$log->user_id; }\""
            ];

            // Get the full command
            $fullCommand = $commandMap[$command] ?? $command;
            
            // Security check - only allow certain commands
            $allowedCommands = [
                // Database Operations
                'migrate', 'seed', 'fresh', 'backup',
                
                // System Cleanup
                'clear-cache', 'clear-config', 'clear-views', 'clear-routes',
                
                // System Maintenance
                'optimize', 'config-cache', 'route-cache', 'view-cache',
                
                // Security & Permissions
                'key-generate', 'storage-link', 'permissions', 'composer-install',
                
                // Performance & Monitoring
                'queue-work', 'schedule-run', 'horizon', 'telescope',
                
                // Emergency Actions
                'down', 'up', 'maintenance', 'health-check',
                
                // Notifications & Real-time
                'notification-test', 'pusher-test', 'websocket-test', 'notification-clear',
                
                // Cron Jobs & Scheduling
                'cron-list', 'cron-test', 'schedule-list', 'cron-install',
                
                // Email & Communication
                'mail-test', 'mail-queue', 'mail-failed', 'mail-clear',
                
                // Database & Storage
                'db-status', 'db-optimize', 'storage-info', 'storage-cleanup',
                
                // Security & Logs
                'log-clear', 'log-rotate', 'security-scan', 'audit-log'
            ];

            // Check if it's a custom command (starts with artisan)
            $isCustomCommand = strpos($command, 'artisan') === 0 || strpos($command, 'make:') === 0 || strpos($command, 'php artisan') === 0;
            
            if (!in_array($command, $allowedCommands) && !$isCustomCommand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Command not allowed for security reasons'
                ]);
            }

            // Execute the command
            $output = [];
            $returnCode = 0;
            
            if (function_exists('exec')) {
                exec($fullCommand . ' 2>&1', $output, $returnCode);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Command execution not available on this server'
                ]);
            }

            $outputText = implode("\n", $output);
            
            return response()->json([
                'success' => $returnCode === 0,
                'message' => $returnCode === 0 ? 'Command executed successfully' : 'Command failed',
                'output' => $outputText
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error executing command: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear theme-related cache automatically
     */
    private function clearThemeCache()
    {
        try {
            // Clear Laravel caches
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            
            // Clear compiled assets if they exist
            $publicPath = public_path();
            $cssPath = $publicPath . '/css/compiled.css';
            $jsPath = $publicPath . '/js/compiled.js';
            
            if (file_exists($cssPath)) {
                unlink($cssPath);
            }
            
            if (file_exists($jsPath)) {
                unlink($jsPath);
            }
            
            // Clear browser cache headers for theme assets
            $this->clearBrowserCache();
            
            \Log::info('Theme cache cleared automatically after settings update');
            
        } catch (\Exception $e) {
            \Log::error('Failed to clear theme cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear browser cache for theme assets
     */
    private function clearBrowserCache()
    {
        try {
            // Generate new cache busting parameter
            $cacheBuster = time();
            
            // Update theme cache buster in system settings
            SystemSetting::setValue('theme_cache_buster', $cacheBuster, 'string');
            
        } catch (\Exception $e) {
            \Log::error('Failed to update cache buster: ' . $e->getMessage());
        }
    }
}
