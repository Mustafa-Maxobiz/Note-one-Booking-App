<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\View;

class TestAutoLoginButton extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:auto-login-button';

    /**
     * The console command description.
     */
    protected $description = 'Test auto-login button visibility and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing auto-login button implementation...');

        // Get some test users
        $admin = User::where('role', 'admin')->first();
        $teacher = User::where('role', 'teacher')->first();
        $student = User::where('role', 'student')->first();

        if (!$admin) {
            $this->error('No admin user found!');
            return 1;
        }

        if (!$teacher && !$student) {
            $this->error('No non-admin users found!');
            return 1;
        }

        $this->line("Admin User: {$admin->name} (ID: {$admin->id})");
        
        if ($teacher) {
            $this->line("Teacher User: {$teacher->name} (ID: {$teacher->id})");
        }
        
        if ($student) {
            $this->line("Student User: {$student->name} (ID: {$student->id})");
        }

        // Test route generation
        $this->info('Testing route generation...');
        
        if ($teacher) {
            $route = route('admin.users.auto-login', $teacher);
            $this->line("✅ Auto-login route for teacher: {$route}");
        }
        
        if ($student) {
            $route = route('admin.users.auto-login', $student);
            $this->line("✅ Auto-login route for student: {$route}");
        }

        // Test return route
        $returnRoute = route('admin.users.return-to-admin');
        $this->line("✅ Return to admin route: {$returnRoute}");

        $this->info('Auto-login button should be visible in the admin users listing page.');
        $this->line('');
        $this->line('📍 Location: Admin → Users page → Actions column');
        $this->line('🔍 Look for: Blue button with sign-in icon (🔑)');
        $this->line('👥 Shows for: All non-admin users (teachers and students)');
        $this->line('🚫 Hidden for: Admin users (security)');
        $this->line('');
        $this->line('If you don\'t see the button:');
        $this->line('1. Make sure you\'re logged in as an admin');
        $this->line('2. Check that there are non-admin users in the system');
        $this->line('3. Clear browser cache and refresh the page');
        $this->line('4. Check browser console for any JavaScript errors');

        return 0;
    }
}
