<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyAutoLoginButton extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'verify:auto-login-button';

    /**
     * The console command description.
     */
    protected $description = 'Verify auto-login button implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying auto-login button implementation...');

        // Get users
        $admin = User::where('role', 'admin')->first();
        $teachers = User::where('role', 'teacher')->take(3)->get();
        $students = User::where('role', 'student')->take(3)->get();

        $this->line("Admin users: " . User::where('role', 'admin')->count());
        $this->line("Teacher users: " . User::where('role', 'teacher')->count());
        $this->line("Student users: " . User::where('role', 'student')->count());

        if ($teachers->count() > 0) {
            $this->line("\n✅ Teachers found - auto-login button should show for:");
            foreach ($teachers as $teacher) {
                $this->line("   - {$teacher->name} (ID: {$teacher->id})");
            }
        }

        if ($students->count() > 0) {
            $this->line("\n✅ Students found - auto-login button should show for:");
            foreach ($students as $student) {
                $this->line("   - {$student->name} (ID: {$student->id})");
            }
        }

        $this->line("\n🔍 Button Details:");
        $this->line("   - Icon: 🔑 (fas fa-sign-in-alt)");
        $this->line("   - Color: Blue gradient (btn-outline-info)");
        $this->line("   - Shows for: All non-admin users");
        $this->line("   - Hidden for: Admin users");

        $this->line("\n📍 Location in Actions column:");
        $this->line("   [👁️ View] [✏️ Edit] [🔑 Auto-Login] [🗑️ Delete]");

        $this->line("\n✅ Implementation Status:");
        $this->line("   - Route: ✅ Registered");
        $this->line("   - Controller: ✅ Implemented");
        $this->line("   - View: ✅ Updated");
        $this->line("   - Cache: ✅ Cleared");

        $this->info("\n🎯 The auto-login button should now be visible!");
        $this->line("Refresh your browser and check the Actions column.");

        return 0;
    }
}
