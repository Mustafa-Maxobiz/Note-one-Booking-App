<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;

class FindPasswordForRecordings extends Command
{
    protected $signature = 'find:password-for-recordings';
    protected $description = 'Find password for meetings that have recordings';

    public function handle()
    {
        $this->info("🔍 Finding passwords for meetings with recordings...");
        $this->line("");
        
        // Get all meetings that have recordings
        $meetingsWithRecordings = SessionRecording::select('zoom_meeting_id')
            ->distinct()
            ->pluck('zoom_meeting_id');
        
        $this->info("Meetings with recordings:");
        foreach ($meetingsWithRecordings as $meetingId) {
            $this->line("  - {$meetingId}");
        }
        $this->line("");
        
        // Check each meeting for passwords
        foreach ($meetingsWithRecordings as $meetingId) {
            $this->info("Checking meeting ID: {$meetingId}");
            
            // Find booking with this meeting ID
            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
            
            if ($booking) {
                $this->line("  ✅ Found booking: {$booking->id}");
                $this->line("  - Teacher: {$booking->teacher->user->name}");
                $this->line("  - Student: {$booking->student->user->name}");
                
                if ($booking->zoom_password) {
                    $this->line("  🎉 PASSWORD: {$booking->zoom_password}");
                } else {
                    $this->line("  ⚠️  No password found");
                }
            } else {
                $this->line("  ❌ No booking found");
            }
            $this->line("");
        }
        
        // Also check if there are any bookings with passwords that might match
        $this->info("All bookings with passwords:");
        $bookingsWithPasswords = Booking::whereNotNull('zoom_password')
            ->where('zoom_password', '!=', '')
            ->get();
        
        foreach ($bookingsWithPasswords as $booking) {
            $this->line("  - Meeting ID: {$booking->zoom_meeting_id} | Password: {$booking->zoom_password}");
        }
        
        $this->line("");
        $this->info("💡 Based on your database table, try these passwords:");
        $this->line("  - For meeting 83312278098: Try 920266 (from meeting 1707899474)");
        $this->line("  - Or try: 476056 (from meeting -815360184)");
    }
}
