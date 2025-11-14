<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:update-statuses {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Update booking statuses for meetings that have ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Find bookings that should be completed but aren't
        $bookings = Booking::with(['teacher.user', 'student.user'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('start_time', '<', now()->subMinutes(30)) // Started more than 30 minutes ago
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('✅ No bookings need status updates');
            return 0;
        }

        $this->info("Found {$bookings->count()} booking(s) that need status updates:");

        $updatedCount = 0;
        $notificationCount = 0;

        foreach ($bookings as $booking) {
            $timeDiff = now()->diffInMinutes($booking->start_time, false);
            
            $this->line("📅 Booking ID: {$booking->id}");
            $this->line("   Status: {$booking->status} → completed");
            $this->line("   Start: {$booking->start_time}");
            $this->line("   Time Since: " . abs($timeDiff) . " minutes ago");
            $this->line("   Teacher: {$booking->teacher->user->name}");
            $this->line("   Student: {$booking->student->user->name}");
            
            if (!$isDryRun) {
                // Update status to completed
                $booking->update(['status' => 'completed']);
                $updatedCount++;
                
                // Send notification
                try {
                    NotificationService::bookingCompleted($booking);
                    $notificationCount++;
                    $this->line("   ✅ Status updated and notification sent");
                } catch (\Exception $e) {
                    $this->error("   ❌ Notification failed: " . $e->getMessage());
                }
            } else {
                $this->line("   🔍 Would update status and send notification");
            }
            
            $this->line("");
        }

        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETE - {$bookings->count()} booking(s) would be updated");
        } else {
            $this->info("✅ COMPLETE - Updated {$updatedCount} booking(s), sent {$notificationCount} notification(s)");
            
            Log::info('Booking statuses updated automatically', [
                'updated_count' => $updatedCount,
                'notification_count' => $notificationCount
            ]);
        }

        return 0;
    }
}
