<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class CheckRecordingDuration extends Command
{
    protected $signature = 'check:recording-duration {meeting_id?}';
    protected $description = 'Check recording duration values in database';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("Checking duration values for meeting ID: {$meetingId}");
        
        $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        
        if ($recordings->isEmpty()) {
            $this->error("No recordings found for meeting ID: {$meetingId}");
            return;
        }
        
        $this->info("Found " . $recordings->count() . " recordings:");
        $this->line("");
        
        foreach ($recordings as $recording) {
            $this->line("Recording ID: {$recording->id}");
            $this->line("  Type: {$recording->recording_type}");
            $this->line("  Duration (raw): " . ($recording->duration ?? 'NULL'));
            $this->line("  Duration (formatted): " . $recording->formatted_duration);
            $this->line("  File Size (raw): " . ($recording->file_size ?? 'NULL'));
            $this->line("  File Size (formatted): " . $recording->formatted_file_size);
            $this->line("  Recording Start: " . ($recording->recording_start ?? 'NULL'));
            $this->line("  Recording End: " . ($recording->recording_end ?? 'NULL'));
            $this->line("  ---");
        }
        
        // Check if we can calculate duration from start/end times
        $this->line("");
        $this->info("Checking if we can calculate duration from start/end times:");
        
        foreach ($recordings as $recording) {
            if ($recording->recording_start && $recording->recording_end) {
                $duration = $recording->recording_end->diffInSeconds($recording->recording_start);
                $this->line("  Recording {$recording->id}: Calculated duration = {$duration} seconds");
            } else {
                $this->line("  Recording {$recording->id}: Cannot calculate (missing start/end times)");
            }
        }
    }
}
