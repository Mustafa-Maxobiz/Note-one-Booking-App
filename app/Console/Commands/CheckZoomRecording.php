<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;
use App\Services\ZoomService;

class CheckZoomRecording extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:check-recording {meeting_id}';

    /**
     * The console command description.
     */
    protected $description = 'Check why a specific Zoom recording is not being fetched';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        
        $this->info("🔍 Checking Zoom recording for meeting ID: {$meetingId}");
        $this->line('');

        // Step 1: Check if booking exists
        $this->line('📋 Step 1: Checking if booking exists...');
        $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
        
        if ($booking) {
            $this->line("   ✅ Found booking: ID {$booking->id}");
            $this->line("   📅 Start time: {$booking->start_time}");
            $this->line("   📅 End time: {$booking->end_time}");
            $this->line("   👤 Teacher: " . ($booking->teacher ? $booking->teacher->user->name : 'N/A'));
            $this->line("   👤 Student: " . ($booking->student ? $booking->student->user->name : 'N/A'));
            $this->line("   📊 Status: {$booking->status}");
        } else {
            $this->line("   ❌ No booking found with meeting ID: {$meetingId}");
            $this->line("   💡 This means the meeting was not created through the system");
            return 1;
        }

        // Step 2: Check if recordings already exist
        $this->line('');
        $this->line('📋 Step 2: Checking existing recordings...');
        $existingRecordings = SessionRecording::where('zoom_meeting_id', $meetingId)->get();
        
        if ($existingRecordings->count() > 0) {
            $this->line("   ✅ Found {$existingRecordings->count()} existing recording(s):");
            foreach ($existingRecordings as $recording) {
                $this->line("      - ID: {$recording->id}, Type: {$recording->recording_type}, Status: {$recording->status}");
            }
        } else {
            $this->line("   ❌ No recordings found in database");
        }

        // Step 3: Check Zoom API credentials
        $this->line('');
        $this->line('📋 Step 3: Checking Zoom API credentials...');
        $zoom = new ZoomService();
        
        if (!$zoom->isConfigured()) {
            $this->line("   ❌ Zoom credentials not configured");
            $this->line("   💡 Check your Zoom API settings in admin panel");
            return 1;
        } else {
            $this->line("   ✅ Zoom credentials are configured");
        }

        // Step 4: Test Zoom API connection
        $this->line('');
        $this->line('📋 Step 4: Testing Zoom API connection...');
        try {
            $userInfo = $zoom->getUserInfo();
            if ($userInfo) {
                $this->line("   ✅ Zoom API connection successful");
                $this->line("   👤 Connected as: " . ($userInfo['first_name'] ?? 'Unknown') . ' ' . ($userInfo['last_name'] ?? 'User'));
            } else {
                $this->line("   ❌ Failed to get user info from Zoom API");
            }
        } catch (\Exception $e) {
            $this->line("   ❌ Zoom API connection failed: " . $e->getMessage());
        }

        // Step 5: Try to fetch recordings from Zoom
        $this->line('');
        $this->line('📋 Step 5: Fetching recordings from Zoom API...');
        try {
            $zoomRecordings = $zoom->getMeetingRecordings($meetingId);
            
            if ($zoomRecordings && isset($zoomRecordings['recording_files'])) {
                $this->line("   ✅ Found " . count($zoomRecordings['recording_files']) . " recording file(s) from Zoom:");
                
                foreach ($zoomRecordings['recording_files'] as $index => $recording) {
                    $this->line("      Recording " . ($index + 1) . ":");
                    $this->line("         - ID: " . ($recording['id'] ?? 'N/A'));
                    $this->line("         - Type: " . ($recording['recording_type'] ?? 'N/A'));
                    $this->line("         - Status: " . ($recording['status'] ?? 'N/A'));
                    $this->line("         - File size: " . ($recording['file_size'] ?? 'N/A') . " bytes");
                    $this->line("         - Play URL: " . ($recording['play_url'] ?? 'N/A'));
                }
                
                // Check if recordings are processed
                if (isset($zoomRecordings['recording_files'][0]['status'])) {
                    $status = $zoomRecordings['recording_files'][0]['status'];
                    if ($status === 'completed') {
                        $this->line("   ✅ Recordings are completed and ready");
                    } else {
                        $this->line("   ⏳ Recordings are still processing (Status: {$status})");
                        $this->line("   💡 Wait a few minutes and try again");
                    }
                }
                
            } else {
                $this->line("   ❌ No recordings found in Zoom");
                $this->line("   💡 Possible reasons:");
                $this->line("      - Recording was not enabled for this meeting");
                $this->line("      - Meeting is still in progress");
                $this->line("      - Recording processing is not complete");
            }
            
        } catch (\Exception $e) {
            $this->line("   ❌ Error fetching recordings: " . $e->getMessage());
        }

        // Step 6: Check if meeting ended
        $this->line('');
        $this->line('📋 Step 6: Checking meeting timing...');
        $now = now();
        $endTime = $booking->end_time;
        
        if ($endTime > $now) {
            $this->line("   ⏳ Meeting is still in progress (ends at {$endTime})");
            $this->line("   💡 Recordings are usually available 5-10 minutes after meeting ends");
        } else {
            $timeSinceEnd = $now->diffInMinutes($endTime);
            $this->line("   ✅ Meeting ended {$timeSinceEnd} minutes ago");
            
            if ($timeSinceEnd < 5) {
                $this->line("   💡 Recording might still be processing, wait a few more minutes");
            } else {
                $this->line("   💡 Recording should be available by now");
            }
        }

        // Step 7: Manual fetch attempt
        $this->line('');
        $this->line('📋 Step 7: Attempting manual fetch...');
        try {
            $this->call('zoom:manual-fetch', ['meeting_id' => $meetingId]);
            $this->line("   ✅ Manual fetch completed");
        } catch (\Exception $e) {
            $this->line("   ❌ Manual fetch failed: " . $e->getMessage());
        }

        $this->line('');
        $this->info('✅ Zoom recording check completed!');
        
        return 0;
    }
}
