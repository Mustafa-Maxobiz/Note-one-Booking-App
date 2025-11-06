<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZoomService;
use App\Models\Booking;
use App\Models\SessionRecording;
use Illuminate\Support\Facades\Log;

class ManualFetchRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:manual-fetch {meeting_id? : Specific meeting ID to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually fetch recordings for a specific meeting or all meetings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $meetingId = $this->argument('meeting_id');
        
        $this->info('🎥 Manual Zoom Recordings Fetch');
        $this->info('==============================');
        
        $zoom = new ZoomService();
        
        if (!$zoom->isConfigured()) {
            $this->error('❌ Zoom credentials not configured!');
            return 1;
        }
        
        if ($meetingId) {
            $this->info("Fetching recordings for meeting: {$meetingId}");
            $this->fetchMeetingRecordings($zoom, $meetingId);
        } else {
            $this->info('Fetching recordings for all meetings...');
            
            $bookings = Booking::whereNotNull('zoom_meeting_id')
                ->where('status', 'completed')
                ->get();
                
            $this->info("Found {$bookings->count()} completed bookings with Zoom meetings");
            
            if ($bookings->count() == 0) {
                $this->warn('No completed bookings found with Zoom meetings.');
                return 0;
            }
            
            $bar = $this->output->createProgressBar($bookings->count());
            $bar->start();
            
            $newRecordings = 0;
            $errors = 0;
            
            foreach ($bookings as $booking) {
                try {
                    $bar->advance();
                    $result = $this->fetchMeetingRecordings($zoom, $booking->zoom_meeting_id, $booking);
                    $newRecordings += $result['new'];
                    $errors += $result['errors'];
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Error fetching recordings for booking', [
                        'booking_id' => $booking->id,
                        'zoom_meeting_id' => $booking->zoom_meeting_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $bar->finish();
            $this->newLine();
            
            $this->info("✅ Fetch completed!");
            $this->info("📊 New recordings: {$newRecordings}");
            $this->info("❌ Errors: {$errors}");
        }
        
        return 0;
    }
    
    private function fetchMeetingRecordings($zoom, $meetingId, $booking = null)
    {
        $newRecordings = 0;
        $errors = 0;
        
        try {
            $zoomRecordings = $zoom->getMeetingRecordings($meetingId);
            
            if ($zoomRecordings && isset($zoomRecordings['recording_files'])) {
                foreach ($zoomRecordings['recording_files'] as $zoomRecording) {
                    // Check if recording already exists
                    $existingRecording = SessionRecording::where([
                        'zoom_meeting_id' => $meetingId,
                        'recording_id' => $zoomRecording['id'] ?? null
                    ])->first();
                    
                    if (!$existingRecording) {
                        // Find booking if not provided
                        if (!$booking) {
                            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
                        }
                        
                        if ($booking) {
                            // Create new recording record
                            SessionRecording::create([
                                'booking_id' => $booking->id,
                                'zoom_meeting_id' => $meetingId,
                                'recording_id' => $zoomRecording['id'] ?? 'REC_' . rand(100000000, 999999999),
                                'recording_type' => $zoomRecording['recording_type'] ?? 'video',
                                'file_name' => $zoomRecording['file_name'] ?? 'Session_Recording_' . $booking->id . '.mp4',
                                'download_url' => $zoomRecording['download_url'] ?? '',
                                'play_url' => $zoomRecording['play_url'] ?? '',
                                'file_size' => $zoomRecording['file_size'] ?? 0,
                                'duration' => $zoomRecording['duration'] ?? 0,
                                'recording_start' => $booking->start_time,
                                'recording_end' => $booking->end_time,
                                'is_processed' => true,
                            ]);
                            
                            $newRecordings++;
                            
                            $this->line("  📹 New recording: {$zoomRecording['file_name']} ({$zoomRecording['recording_type']})");
                        }
                    }
                }
            } else {
                $this->line("  ℹ️  No recordings found for meeting {$meetingId}");
            }
            
        } catch (\Exception $e) {
            $errors++;
            $this->error("  ❌ Error fetching recordings for meeting {$meetingId}: " . $e->getMessage());
        }
        
        return ['new' => $newRecordings, 'errors' => $errors];
    }
}
