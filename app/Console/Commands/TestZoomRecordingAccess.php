<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class TestZoomRecordingAccess extends Command
{
    protected $signature = 'test:zoom-recording-access {meeting_id?}';
    protected $description = 'Test different methods to access Zoom recordings';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("🔍 Testing Zoom recording access for meeting: {$meetingId}");
        $this->line("");
        
        $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        
        if ($recordings->isEmpty()) {
            $this->error("❌ No recordings found for meeting ID: {$meetingId}");
            return;
        }
        
        $this->info("Found " . $recordings->count() . " recordings:");
        $this->line("");
        
        foreach ($recordings as $recording) {
            $this->line("Recording ID: {$recording->id}");
            $this->line("  Type: {$recording->recording_type}");
            $this->line("  File: {$recording->file_name}");
            $this->line("  Play URL: {$recording->play_url}");
            $this->line("  Download URL: {$recording->download_url}");
            $this->line("");
            
            // Test different URL formats
            $this->info("🧪 Testing different URL formats:");
            
            // 1. Original URL
            $this->line("  1. Original: {$recording->play_url}");
            
            // 2. With passcode parameter
            $passcode = '920266';
            $separator = str_contains($recording->play_url, '?') ? '&' : '?';
            $urlWithPasscode = $recording->play_url . $separator . 'passcode=' . urlencode($passcode);
            $this->line("  2. With passcode: {$urlWithPasscode}");
            
            // 3. With pwd parameter (like join URLs)
            $urlWithPwd = $recording->play_url . $separator . 'pwd=' . urlencode($passcode);
            $this->line("  3. With pwd: {$urlWithPwd}");
            
            // 4. With password parameter
            $urlWithPassword = $recording->play_url . $separator . 'password=' . urlencode($passcode);
            $this->line("  4. With password: {$urlWithPassword}");
            
            // 5. With meeting ID as passcode
            $urlWithMeetingId = $recording->play_url . $separator . 'passcode=' . urlencode($meetingId);
            $this->line("  5. With meeting ID: {$urlWithMeetingId}");
            
            $this->line("");
        }
        
        $this->info("💡 Possible issues:");
        $this->line("  - Zoom recordings might require authentication via cookies/session");
        $this->line("  - The recording might be expired or moved");
        $this->line("  - The recording might require a different authentication method");
        $this->line("  - The recording might be accessible only through Zoom's web interface");
        
        $this->line("");
        $this->info("🔧 Alternative solutions:");
        $this->line("  1. Try accessing the recording directly in a new browser tab");
        $this->line("  2. Check if the recording is still available on Zoom");
        $this->line("  3. Try downloading the recording instead of playing it");
        $this->line("  4. Contact the meeting host for access");
        
        $this->line("");
        $this->info("🧪 Test these URLs manually:");
        foreach ($recordings as $recording) {
            $this->line("  - Original: {$recording->play_url}");
            $this->line("  - Download: {$recording->download_url}");
        }
    }
}
