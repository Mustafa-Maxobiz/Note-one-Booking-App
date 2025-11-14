<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SessionRecording;
use App\Models\Booking;

class ViewRecordings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:view-recordings {meeting_id?}';

    /**
     * The console command description.
     */
    protected $description = 'View recordings for a specific meeting or all recordings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        
        if ($meetingId) {
            $this->info("🎥 Viewing recordings for meeting ID: {$meetingId}");
            
            $recordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
            
            if ($recordings->count() == 0) {
                $this->line("❌ No recordings found for meeting ID: {$meetingId}");
                return 0;
            }
            
            $this->line("\n📊 Found {$recordings->count()} recording(s):");
            $this->line('ID | Type | File Size | Status | Play URL');
            $this->line('---|------|-----------|--------|---------');
            
            foreach ($recordings as $recording) {
                $playUrl = $recording->play_url ? 'Available' : 'N/A';
                $this->line("{$recording->id} | {$recording->recording_type} | {$recording->formatted_file_size} | {$recording->status} | {$playUrl}");
            }
            
        } else {
            $this->info("🎥 Viewing all recordings...");
            
            $recordings = SessionRecording::with('booking')->orderBy('created_at', 'desc')->take(10)->get();
            
            if ($recordings->count() == 0) {
                $this->line("❌ No recordings found in the system");
                return 0;
            }
            
            $this->line("\n📊 Recent recordings:");
            $this->line('ID | Meeting ID | Type | File Size | Booking ID | Created');
            $this->line('---|------------|------|-----------|------------|--------');
            
            foreach ($recordings as $recording) {
                $this->line("{$recording->id} | {$recording->zoom_meeting_id} | {$recording->recording_type} | {$recording->formatted_file_size} | {$recording->booking_id} | {$recording->created_at->format('Y-m-d H:i')}");
            }
        }
        
        $this->line("\n💡 To view recordings in the admin panel:");
        $this->line("   Visit: /admin/session-recordings");
        
        return 0;
    }
}
