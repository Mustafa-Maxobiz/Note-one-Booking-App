<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class GetZoomPassword extends Command
{
    protected $signature = 'get:zoom-password {meeting_id?}';
    protected $description = 'Get the zoom_password for a specific meeting';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("🔍 Looking for zoom_password for meeting ID: {$meetingId}");
        $this->line("");
        
        // Find booking with this zoom_meeting_id
        $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
        
        if ($booking) {
            $this->info("✅ Found booking for meeting ID: {$meetingId}");
            $this->line("  - Booking ID: {$booking->id}");
            $this->line("  - Teacher: {$booking->teacher->user->name}");
            $this->line("  - Student: {$booking->student->user->name}");
            $this->line("");
            
            if ($booking->zoom_password) {
                $this->info("🎉 ZOOM PASSWORD FOUND:");
                $this->line("  📝 Passcode: {$booking->zoom_password}");
                $this->line("");
                $this->info("✅ You can now use this passcode in the recording modal!");
                $this->line("   Just enter: {$booking->zoom_password}");
            } else {
                $this->warn("⚠️  No zoom_password found for this booking.");
            }
            
            // Show other zoom details
            $this->line("");
            $this->info("📋 Other Zoom details:");
            $this->line("  - Join URL: " . ($booking->zoom_join_url ?? 'Not set'));
            $this->line("  - Start URL: " . ($booking->zoom_start_url ?? 'Not set'));
            
        } else {
            $this->error("❌ No booking found for meeting ID: {$meetingId}");
            $this->line("");
            $this->info("Available meetings with passwords:");
            
            $bookings = Booking::whereNotNull('zoom_password')
                ->where('zoom_password', '!=', '')
                ->get();
            
            if ($bookings->count() > 0) {
                foreach ($bookings as $booking) {
                    $this->line("  - Meeting ID: {$booking->zoom_meeting_id} | Password: {$booking->zoom_password}");
                }
            } else {
                $this->warn("No bookings with zoom passwords found.");
            }
        }
        
        $this->line("");
        $this->info("🧪 To test the passcode:");
        $this->line("1. Go to Admin Panel > Session Recordings");
        $this->line("2. Click Play on any recording from meeting {$meetingId}");
        $this->line("3. Enter the passcode: " . ($booking->zoom_password ?? 'NOT FOUND'));
        $this->line("4. Click 'Load Recording'");
    }
}
