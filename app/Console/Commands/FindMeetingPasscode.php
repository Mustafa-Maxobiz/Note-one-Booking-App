<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;

class FindMeetingPasscode extends Command
{
    protected $signature = 'find:meeting-passcode {meeting_id?}';
    protected $description = 'Help find meeting passcode for Zoom recordings';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("🔍 Finding passcode for meeting ID: {$meetingId}");
        $this->line("");
        
        // Check if we have a booking for this meeting
        $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
        
        if ($booking) {
            $this->info("✅ Found booking for this meeting:");
            $this->line("  - Booking ID: {$booking->id}");
            $this->line("  - Teacher: {$booking->teacher->user->name}");
            $this->line("  - Student: {$booking->student->user->name}");
            $this->line("  - Session Date: {$booking->session_date}");
            $this->line("  - Session Time: {$booking->session_time}");
            $this->line("");
            
            // Check if there's a passcode stored in the booking
            if ($booking->zoom_password) {
                $this->info("🎉 PASSCODE FOUND in booking:");
                $this->line("  Passcode: {$booking->zoom_password}");
                $this->line("");
                $this->info("You can now use this passcode in the recording modal!");
            } else {
                $this->warn("⚠️  No passcode stored in booking record.");
            }
        } else {
            $this->warn("⚠️  No booking found for meeting ID: {$meetingId}");
        }
        
        $this->line("");
        $this->info("🔑 How to find the passcode:");
        $this->line("1. Check your Zoom meeting settings");
        $this->line("2. Look in the original meeting invitation email");
        $this->line("3. Check the meeting details in your Zoom account");
        $this->line("4. Contact the meeting host (teacher)");
        $this->line("");
        
        $this->info("💡 Common passcode formats:");
        $this->line("  - 6-digit numbers: 123456");
        $this->line("  - 8-digit numbers: 12345678");
        $this->line("  - Alphanumeric: ABC123");
        $this->line("  - Sometimes it's the meeting ID itself");
        $this->line("");
        
        $this->info("🧪 To test the passcode:");
        $this->line("1. Go to Admin Panel > Session Recordings");
        $this->line("2. Click Play on any recording from meeting {$meetingId}");
        $this->line("3. Enter the passcode in the modal");
        $this->line("4. Click 'Load Recording'");
        
        // Check if we can get more info from the recordings
        $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        if ($recordings->count() > 0) {
            $this->line("");
            $this->info("📹 Recording details:");
            $this->line("  - Total recordings: {$recordings->count()}");
            $this->line("  - Recording types: " . $recordings->pluck('recording_type')->unique()->implode(', '));
            $this->line("  - All recordings require the same passcode");
        }
    }
}
