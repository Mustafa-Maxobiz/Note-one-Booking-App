<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class SendDynamicNotifications extends Command
{
    protected $signature = 'notifications:send-dynamic';
    protected $description = 'Send dynamic notifications for upcoming sessions (24h and 1h reminders)';

    public function handle()
    {
        $this->info('🔔 Starting dynamic notification system...');
        
        $now = Carbon::now();
        
        // Get all active bookings
        $bookings = Booking::with(['student.user', 'teacher.user'])
            ->where('start_time', '>', $now)
            ->where('start_time', '<=', $now->copy()->addDays(2)) // Next 2 days
            ->where('status', 'confirmed')
            ->get();
        
        $this->info("Found {$bookings->count()} active bookings to check for notifications");
        
        if ($bookings->count() == 0) {
            $this->info('No bookings need notifications at this time.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();
        
        $notifications24h = 0;
        $notifications1h = 0;
        $errors = 0;
        
        foreach ($bookings as $booking) {
            try {
                $bar->advance();
                
                // Send dynamic notifications
                $booking->sendDynamicNotifications();
                
                // Count notifications sent
                if ($booking->notification_sent_24h) {
                    $notifications24h++;
                }
                if ($booking->notification_sent_1h) {
                    $notifications1h++;
                }
                
            } catch (\Exception $e) {
                $errors++;
                \Log::error('Error sending dynamic notification', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Dynamic notifications completed!");
        $this->info("📧 24-hour reminders sent: {$notifications24h}");
        $this->info("🚨 1-hour reminders sent: {$notifications1h}");
        $this->info("❌ Errors: {$errors}");
        
        if ($notifications24h > 0 || $notifications1h > 0) {
            $this->info("🎉 Students have been notified about their upcoming lessons!");
        }
        
        return 0;
    }
}
