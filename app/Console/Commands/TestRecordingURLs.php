<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class TestRecordingURLs extends Command
{
    protected $signature = 'test:recording-urls {meeting_id?}';
    protected $description = 'Test recording URLs to debug passcode field logic';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("Testing recording URLs for meeting ID: {$meetingId}");
        $this->line("");
        
        $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        
        if ($recordings->isEmpty()) {
            $this->error("No recordings found for meeting ID: {$meetingId}");
            return;
        }
        
        foreach ($recordings as $recording) {
            $this->line("Recording ID: {$recording->id}");
            $this->line("  Type: {$recording->recording_type}");
            $this->line("  Play URL: {$recording->play_url}");
            
            // Test the JavaScript logic
            $playUrl = $recording->play_url;
            $isZoomRecording = str_contains($playUrl, 'zoom.us/rec/play/');
            $isNotAvailable = str_contains($playUrl, 'not-available');
            $shouldShowPasscode = $isZoomRecording && !$isNotAvailable;
            
            $this->line("  Is Zoom recording: " . ($isZoomRecording ? 'YES' : 'NO'));
            $this->line("  Contains 'not-available': " . ($isNotAvailable ? 'YES' : 'NO'));
            $this->line("  Should show passcode field: " . ($shouldShowPasscode ? 'YES' : 'NO'));
            $this->line("  ---");
        }
        
        $this->line("");
        $this->info("To test in browser:");
        $this->line("1. Open browser developer tools (F12)");
        $this->line("2. Go to Console tab");
        $this->line("3. Go to Admin Panel > Session Recordings");
        $this->line("4. Click Play on any recording");
        $this->line("5. Check console for debug messages");
    }
}
