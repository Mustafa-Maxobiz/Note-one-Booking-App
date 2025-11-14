<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class TestPasscodeField extends Command
{
    protected $signature = 'test:passcode-field';
    protected $description = 'Test passcode field logic for recordings';

    public function handle()
    {
        $this->info("Testing passcode field logic...");
        
        // Get a sample recording
        $recording = SessionRecording::whereNotNull('play_url')
            ->where('play_url', '!=', 'https://zoom.us/rec/play/not-available')
            ->first();
        
        if (!$recording) {
            $this->error("No recordings found with valid play URLs.");
            return;
        }
        
        $this->info("Sample recording found:");
        $this->line("  - ID: {$recording->id}");
        $this->line("  - Type: {$recording->recording_type}");
        $this->line("  - File: {$recording->file_name}");
        $this->line("  - Play URL: {$recording->play_url}");
        
        // Test the logic
        $playUrl = $recording->play_url;
        $isZoomRecording = str_contains($playUrl, 'zoom.us/rec/play/') && !str_contains($playUrl, 'not-available');
        
        $this->line("");
        $this->info("Passcode field logic test:");
        $this->line("  - Is Zoom recording: " . ($isZoomRecording ? 'YES' : 'NO'));
        $this->line("  - Should show passcode field: " . ($isZoomRecording ? 'YES' : 'NO'));
        
        if ($isZoomRecording) {
            $this->line("");
            $this->info("✅ Passcode field will be shown immediately for this recording!");
            $this->line("   The user will see the passcode input form right away.");
        } else {
            $this->line("");
            $this->warn("⚠️  Passcode field will only show if there's an error loading the recording.");
        }
        
        $this->line("");
        $this->info("To test in the browser:");
        $this->line("1. Go to Admin Panel > Session Recordings");
        $this->line("2. Click the Play button for recording ID: {$recording->id}");
        $this->line("3. The passcode field should appear immediately");
    }
}
