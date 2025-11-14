<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;

class HandleZoomPasscode extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:handle-passcode {meeting_id} {--passcode=} {--update-url=}';

    /**
     * The console command description.
     */
    protected $description = 'Handle Zoom recording passcode issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        $passcode = $this->option('passcode');
        $updateUrl = $this->option('update-url');
        
        $this->info("🔐 Handling Zoom Passcode for Meeting: {$meetingId}");
        $this->line('===============================================');
        
        // Find the booking
        $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
        
        if (!$booking) {
            $this->error("❌ No booking found with meeting ID: {$meetingId}");
            return 1;
        }
        
        $this->line("✅ Found booking ID: {$booking->id}");
        $this->line("👨‍🏫 Teacher: " . ($booking->teacher ? $booking->teacher->user->name : 'Unknown'));
        $this->line("👨‍🎓 Student: " . ($booking->student ? $booking->student->user->name : 'Unknown'));
        $this->line("📅 Session: {$booking->start_time} - {$booking->end_time}");
        
        $this->line("\n🔍 Checking for existing recordings...");
        $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        
        if ($recordings->count() > 0) {
            $this->line("✅ Found {$recordings->count()} recording(s):");
            foreach ($recordings as $recording) {
                $this->line("   - ID: {$recording->id}");
                $this->line("   - Type: {$recording->recording_type}");
                $this->line("   - Play URL: {$recording->play_url}");
                $this->line("   - Download URL: {$recording->download_url}");
                $this->line("   - File Size: {$recording->formatted_file_size}");
                $this->line("   - Status: {$recording->status}");
                $this->line("   " . str_repeat('-', 40));
            }
        } else {
            $this->line("❌ No recordings found in database");
        }
        
        $this->line("\n🔐 Zoom Recording Passcode Solutions:");
        $this->line("=====================================");
        
        $this->line("\n1️⃣ **Check Zoom Account Settings:**");
        $this->line("   - Go to Zoom web portal → Settings → Recording");
        $this->line("   - Look for 'Require passcode for viewing shared cloud recordings'");
        $this->line("   - Disable this setting for future recordings");
        
        $this->line("\n2️⃣ **Find the Passcode:**");
        $this->line("   - Check the original Zoom meeting invitation email");
        $this->line("   - Look in Zoom web portal → Recordings → Cloud Recordings");
        $this->line("   - The passcode is usually 6-10 digits");
        
        $this->line("\n3️⃣ **Common Passcode Locations:**");
        $this->line("   - Meeting invitation email");
        $this->line("   - Zoom web portal recordings section");
        $this->line("   - Meeting host's Zoom account");
        $this->line("   - Sometimes in the meeting title or description");
        
        $this->line("\n4️⃣ **Alternative Solutions:**");
        $this->line("   - Download the recording directly from Zoom web portal");
        $this->line("   - Re-share the recording without passcode");
        $this->line("   - Contact the meeting host for the passcode");
        
        if ($passcode) {
            $this->line("\n💾 Storing passcode for future reference...");
            // Store passcode in booking notes or create a system setting
            $booking->update([
                'notes' => ($booking->notes ? $booking->notes . "\n" : '') . "Zoom Recording Passcode: {$passcode}"
            ]);
            $this->line("✅ Passcode stored in booking notes");
        }
        
        if ($updateUrl) {
            $this->line("\n🔗 Updating recording URL...");
            // Update the play_url with the new URL
            SessionRecording::where('zoom_meeting_id', $meetingId)->update([
                'play_url' => $updateUrl
            ]);
            $this->line("✅ Recording URL updated");
        }
        
        $this->line("\n🎯 Next Steps:");
        $this->line("1. Find the passcode from the sources mentioned above");
        $this->line("2. Enter the passcode when prompted");
        $this->line("3. If you find a direct download link, update it using:");
        $this->line("   php artisan zoom:handle-passcode {$meetingId} --update-url='NEW_URL'");
        
        $this->line("\n💡 Pro Tip: For future meetings, disable passcode requirement in Zoom settings!");
        
        return 0;
    }
}
