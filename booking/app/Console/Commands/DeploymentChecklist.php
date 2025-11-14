<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class DeploymentChecklist extends Command
{
    protected $signature = 'deploy:checklist';
    protected $description = 'Check deployment readiness and provide deployment commands';

    public function handle()
    {
        $this->info("🚀 LARAVEL BOOKING SYSTEM - DEPLOYMENT CHECKLIST");
        $this->line("=================================================");
        $this->line("");
        
        $this->checkEnvironment();
        $this->checkDatabase();
        $this->checkStorage();
        $this->checkPermissions();
        $this->showDeploymentCommands();
        $this->showSecurityChecklist();
        
        $this->line("");
        $this->info("🎉 DEPLOYMENT CHECKLIST COMPLETED!");
        $this->line("Follow the commands above to deploy your application successfully.");
    }
    
    private function checkEnvironment()
    {
        $this->info("1️⃣ ENVIRONMENT CHECK");
        
        if (File::exists('.env')) {
            $this->line("   ✅ .env file exists");
        } else {
            $this->line("   ❌ .env file missing - Run: cp .env.example .env");
        }
        
        if (config('app.key')) {
            $this->line("   ✅ Application key is set");
        } else {
            $this->line("   ❌ Application key missing - Run: php artisan key:generate");
        }
        
        $this->line("");
    }
    
    private function checkDatabase()
    {
        $this->info("2️⃣ DATABASE CHECK");
        
        try {
            DB::connection()->getPdo();
            $this->line("   ✅ Database connection successful");
            
            // Check if migrations have been run
            $migrationCount = DB::table('migrations')->count();
            if ($migrationCount > 0) {
                $this->line("   ✅ Database migrations have been run ({$migrationCount} migrations)");
            } else {
                $this->line("   ⚠️  No migrations found - Run: php artisan migrate");
            }
        } catch (\Exception $e) {
            $this->line("   ❌ Database connection failed: " . $e->getMessage());
        }
        
        $this->line("");
    }
    
    private function checkStorage()
    {
        $this->info("3️⃣ STORAGE CHECK");
        
        if (File::exists('storage/app/public')) {
            $this->line("   ✅ Storage directory exists");
        } else {
            $this->line("   ❌ Storage directory missing");
        }
        
        if (File::exists('public/storage')) {
            $this->line("   ✅ Storage link exists");
        } else {
            $this->line("   ❌ Storage link missing - Run: php artisan storage:link");
        }
        
        if (is_writable('storage')) {
            $this->line("   ✅ Storage directory is writable");
        } else {
            $this->line("   ❌ Storage directory not writable - Check permissions");
        }
        
        $this->line("");
    }
    
    private function checkPermissions()
    {
        $this->info("4️⃣ PERMISSIONS CHECK");
        
        $directories = ['storage', 'bootstrap/cache'];
        
        foreach ($directories as $dir) {
            if (File::exists($dir)) {
                if (is_writable($dir)) {
                    $this->line("   ✅ {$dir} is writable");
                } else {
                    $this->line("   ❌ {$dir} not writable - Run: chmod -R 775 {$dir}");
                }
            } else {
                $this->line("   ❌ {$dir} directory missing");
            }
        }
        
        $this->line("");
    }
    
    private function showDeploymentCommands()
    {
        $this->info("5️⃣ DEPLOYMENT COMMANDS");
        $this->line("");
        
        $this->line("📦 INSTALL DEPENDENCIES:");
        $this->line("   composer install --optimize-autoloader --no-dev");
        $this->line("");
        
        $this->line("🔧 ENVIRONMENT SETUP:");
        $this->line("   cp .env.example .env");
        $this->line("   php artisan key:generate");
        $this->line("   # Configure database and other settings in .env");
        $this->line("");
        
        $this->line("🗄️ DATABASE SETUP:");
        $this->line("   php artisan migrate --force");
        $this->line("   php artisan db:seed --force  # Optional - for initial data");
        $this->line("");
        
        $this->line("📁 STORAGE SETUP:");
        $this->line("   php artisan storage:link");
        $this->line("   chmod -R 755 storage");
        $this->line("   chmod -R 755 bootstrap/cache");
        $this->line("   chown -R www-data:www-data storage");
        $this->line("   chown -R www-data:www-data bootstrap/cache");
        $this->line("");
        
        $this->line("⚡ OPTIMIZATION:");
        $this->line("   php artisan config:cache");
        $this->line("   php artisan route:cache");
        $this->line("   php artisan view:cache");
        $this->line("   php artisan event:cache");
        $this->line("");
        
        $this->line("🔄 QUEUE SETUP (if using queues):");
        $this->line("   php artisan queue:work --daemon");
        $this->line("");
        
        $this->line("⏰ CRON JOBS:");
        $this->line("   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1");
        $this->line("");
    }
    
    private function showSecurityChecklist()
    {
        $this->info("6️⃣ SECURITY CHECKLIST");
        $this->line("");
        
        $this->line("🔐 ENVIRONMENT SECURITY:");
        $this->line("   • Set APP_ENV=production in .env");
        $this->line("   • Set APP_DEBUG=false in .env");
        $this->line("   • Use strong database passwords");
        $this->line("   • Configure proper CORS settings");
        $this->line("");
        
        $this->line("📁 FILE PERMISSIONS:");
        $this->line("   sudo chown -R www-data:www-data /path/to/your/project");
        $this->line("   find /path/to/your/project -type d -exec chmod 755 {} \\;");
        $this->line("   find /path/to/your/project -type f -exec chmod 644 {} \\;");
        $this->line("   chmod -R 775 /path/to/your/project/storage");
        $this->line("   chmod -R 775 /path/to/your/project/bootstrap/cache");
        $this->line("");
        
        $this->line("🌐 WEB SERVER CONFIGURATION:");
        $this->line("   • Point document root to /path/to/your/project/public");
        $this->line("   • Install SSL certificate for HTTPS");
        $this->line("   • Redirect HTTP to HTTPS");
        $this->line("   • Update APP_URL in .env to use HTTPS");
        $this->line("");
        
        $this->line("📧 EMAIL CONFIGURATION:");
        $this->line("   • Configure SMTP settings in .env");
        $this->line("   • Test email sending functionality");
        $this->line("");
        
        $this->line("🔔 REAL-TIME NOTIFICATIONS:");
        $this->line("   • Configure Pusher settings in .env");
        $this->line("   • Set BROADCAST_DRIVER=pusher");
        $this->line("");
    }
}
