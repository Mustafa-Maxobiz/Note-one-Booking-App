<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class TestRecordingPasscode extends Command
{
    protected $signature = 'test:recording-passcode {meeting_id?}';
    protected $description = 'Test recording passcode functionality';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        
        if ($meetingId) {
            $this->info("Testing recording passcode for meeting ID: {$meetingId}");
            
            $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
            
            if ($recordings->isEmpty()) {
                $this->error("No recordings found for meeting ID: {$meetingId}");
                return;
            }
            
            $this->info("Found " . $recordings->count() . " recordings:");
            
            foreach ($recordings as $recording) {
                $this->line("  - ID: {$recording->id}");
                $this->line("    Type: {$recording->recording_type}");
                $this->line("    File: {$recording->file_name}");
                $this->line("    Play URL: {$recording->play_url}");
                $this->line("    Download URL: {$recording->download_url}");
                $this->line("    ---");
            }
            
            $this->info("To test passcode functionality:");
            $this->line("1. Go to admin panel > Session Recordings");
            $this->line("2. Click the Play button for any recording");
            $this->line("3. If it shows 'Enter the passcode', enter the passcode in the modal");
            $this->line("4. Click 'Load Recording' to play with passcode");
            
        } else {
            $this->info("Available meeting IDs with recordings:");
            
            $meetingIds = SessionRecording::select('zoom_meeting_id')
                ->distinct()
                ->orderBy('zoom_meeting_id')
                ->pluck('zoom_meeting_id');
            
            if ($meetingIds->isEmpty()) {
                $this->warn("No recordings found in the system.");
                return;
            }
            
            foreach ($meetingIds as $id) {
                $count = SessionRecording::where('zoom_meeting_id', $id)->count();
                $this->line("  - {$id} ({$count} recordings)");
            }
            
            $this->line("");
            $this->info("Usage: php artisan test:recording-passcode {meeting_id}");
        }
    }
}
