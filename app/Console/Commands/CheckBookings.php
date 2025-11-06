<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class CheckBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:bookings';

    /**
     * The console command description.
     */
    protected $description = 'Check recent bookings with Zoom meeting IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📋 Checking recent bookings with Zoom meeting IDs...');
        
        $bookings = Booking::whereNotNull('zoom_meeting_id')
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();
        
        if ($bookings->count() == 0) {
            $this->line('❌ No bookings found with Zoom meeting IDs');
            return 0;
        }
        
        $this->line("\n📊 Recent bookings with Zoom meetings:");
        $this->line('ID | Meeting ID | Start Time | Status');
        $this->line('---|------------|------------|--------');
        
        foreach ($bookings as $booking) {
            $this->line("{$booking->id} | {$booking->zoom_meeting_id} | {$booking->start_time} | {$booking->status}");
        }
        
        $this->line("\n💡 If your meeting ID (83312278098) is not in this list, it means:");
        $this->line("   1. The meeting was created outside the system");
        $this->line("   2. The meeting ID was not saved properly");
        $this->line("   3. The meeting was created manually in Zoom");
        
        return 0;
    }
}
