<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;

class TestMeetingFiles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:meeting-files {meeting_id}';

    /**
     * The console command description.
     */
    protected $description = 'Test the getMeetingFiles method';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        
        $this->info("Testing meeting files for: {$meetingId}");
        
        try {
            $files = SessionRecording::where('zoom_meeting_id', $meetingId)
                ->orderBy('recording_type')
                ->get()
                ->map(function($recording) {
                    return [
                        'id' => $recording->id,
                        'recording_type' => $recording->recording_type,
                        'file_name' => $recording->file_name,
                        'formatted_file_size' => $recording->formatted_file_size,
                        'play_url' => $recording->play_url,
                        'download_url' => $recording->download_url,
                    ];
                });

            $this->line("Found {$files->count()} files:");
            foreach ($files as $file) {
                $this->line("- {$file['recording_type']}: {$file['file_name']} ({$file['formatted_file_size']})");
            }
            
            $response = [
                'success' => true,
                'files' => $files
            ];
            
            $this->line("\nJSON Response:");
            $this->line(json_encode($response, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
