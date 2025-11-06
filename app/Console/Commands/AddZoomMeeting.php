<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;
use App\Services\ZoomService;

class AddZoomMeeting extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:add-meeting {meeting_id} {--teacher_id=} {--student_id=} {--start_time=} {--end_time=}';

    /**
     * The console command description.
     */
    protected $description = 'Add a Zoom meeting to the system and fetch its recordings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        $teacherId = $this->option('teacher_id');
        $studentId = $this->option('student_id');
        $startTime = $this->option('start_time');
        $endTime = $this->option('end_time');
        
        $this->info("🔍 Adding Zoom meeting {$meetingId} to the system...");
        
        // Check if meeting already exists
        $existingBooking = Booking::where('zoom_meeting_id', $meetingId)->first();
        if ($existingBooking) {
            $this->line("✅ Meeting already exists in booking ID: {$existingBooking->id}");
            $this->fetchRecordingsForMeeting($meetingId, $existingBooking);
            return 0;
        }
        
        // If no teacher/student provided, create a dummy booking
        if (!$teacherId || !$studentId) {
            $this->line("⚠️  No teacher/student provided. Creating a system booking...");
            
            // Get first available teacher and student
            $teacher = \App\Models\Teacher::first();
            $student = \App\Models\Student::first();
            
            if (!$teacher || !$student) {
                $this->error("❌ No teachers or students found in the system");
                return 1;
            }
            
            $teacherId = $teacher->id;
            $studentId = $student->id;
            
            $this->line("👤 Using teacher: {$teacher->user->name} (ID: {$teacherId})");
            $this->line("👤 Using student: {$student->user->name} (ID: {$studentId})");
        }
        
        // Set default times if not provided
        if (!$startTime) {
            $startTime = now()->subHours(2)->format('Y-m-d H:i:s');
        }
        if (!$endTime) {
            $endTime = now()->subHours(1)->format('Y-m-d H:i:s');
        }
        
        // Create booking
        $booking = Booking::create([
            'teacher_id' => $teacherId,
            'student_id' => $studentId,
            'zoom_meeting_id' => $meetingId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => 60,
            'status' => 'completed',
            'price' => 0,
        ]);
        
        $this->line("✅ Created booking ID: {$booking->id}");
        
        // Fetch recordings
        $this->fetchRecordingsForMeeting($meetingId, $booking);
        
        return 0;
    }
    
    private function fetchRecordingsForMeeting($meetingId, $booking)
    {
        $this->line("\n🎥 Fetching recordings for meeting {$meetingId}...");
        
        $zoom = new ZoomService();
        
        if (!$zoom->isConfigured()) {
            $this->error("❌ Zoom credentials not configured");
            return;
        }
        
        try {
            $zoomRecordings = $zoom->getMeetingRecordings($meetingId);
            
            if ($zoomRecordings && isset($zoomRecordings['recording_files'])) {
                $this->line("✅ Found " . count($zoomRecordings['recording_files']) . " recording file(s):");
                
                foreach ($zoomRecordings['recording_files'] as $index => $recording) {
                    $this->line("   Recording " . ($index + 1) . ":");
                    $this->line("      - Type: " . ($recording['recording_type'] ?? 'N/A'));
                    $this->line("      - Status: " . ($recording['status'] ?? 'N/A'));
                    $this->line("      - File size: " . ($recording['file_size'] ?? 'N/A') . " bytes");
                    
                    // Check if recording already exists
                    $existingRecording = SessionRecording::where([
                        'zoom_meeting_id' => $meetingId,
                        'recording_id' => $recording['id'] ?? null
                    ])->first();
                    
                    if (!$existingRecording) {
                        // Create session recording
                        SessionRecording::create([
                            'booking_id' => $booking->id,
                            'zoom_meeting_id' => $meetingId,
                            'recording_id' => $recording['id'] ?? 'unknown',
                            'recording_type' => $recording['recording_type'] ?? 'unknown',
                            'file_name' => $recording['file_name'] ?? 'recording_' . ($recording['id'] ?? 'unknown') . '.mp4',
                            'file_size' => $recording['file_size'] ?? 0,
                            'play_url' => $recording['play_url'] ?? 'https://zoom.us/rec/play/not-available',
                            'download_url' => $recording['download_url'] ?? 'https://zoom.us/rec/download/not-available',
                            'duration' => $recording['duration'] ?? 0,
                            'recording_start' => $recording['recording_start'] ?? null,
                            'recording_end' => $recording['recording_end'] ?? null,
                            'is_processed' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $this->line("      ✅ Added to database");
                    } else {
                        $this->line("      ⚠️  Already exists in database");
                    }
                }
            } else {
                $this->line("❌ No recordings found for this meeting");
                $this->line("💡 Possible reasons:");
                $this->line("   - Recording was not enabled");
                $this->line("   - Meeting is still in progress");
                $this->line("   - Recording is still processing");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error fetching recordings: " . $e->getMessage());
        }
    }
}
