<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\SessionRecording;
use App\Services\ZoomService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchZoomRecordings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zoom:fetch-recordings 
                            {--force : Force fetch even if recently processed}
                            {--meeting-id= : Fetch recordings for specific meeting ID}
                            {--hours=24 : Only fetch recordings from meetings in last N hours}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch Zoom recordings for all or specific meetings';

    protected $zoomService;

    public function __construct()
    {
        parent::__construct();
        $this->zoomService = new ZoomService();
    }

    /**
     * Extract passcode from recording file data or meeting data
     */
    private function extractPasscode($recordingFile, $meetingData = null)
    {
        // First check individual recording file for passcode
        $possibleFields = [
            'play_passcode',
            'passcode', 
            'password',
            'meeting_passcode',
            'recording_passcode'
        ];
        
        foreach ($possibleFields as $field) {
            if (!empty($recordingFile[$field])) {
                return trim($recordingFile[$field]);
            }
        }
        
        // If no passcode in recording file, check meeting-level passcodes
        if ($meetingData) {
            $meetingPasscodeFields = [
                'password',  // This is the simple passcode like "0?NoVwyf"
                'recording_play_passcode',  // This is the long encrypted one
                'meeting_passcode'
            ];
            
            foreach ($meetingPasscodeFields as $field) {
                if (!empty($meetingData[$field])) {
                    return trim($meetingData[$field]);
                }
            }
        }
        
        return null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎬 Starting Zoom Recording Fetch Process...');
        $this->info('=====================================');

        $startTime = now();
        $totalProcessed = 0;
        $totalNewRecordings = 0;
        $errors = 0;

        try {
            // Check if Zoom service is configured
            if (!$this->zoomService->isConfigured()) {
                $this->error('❌ Zoom service is not properly configured');
                $this->error('Please check your ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, and ZOOM_ACCOUNT_ID');
                return 1;
            }

            $this->info('✅ Zoom service is configured');

            // Get bookings to process
            $bookings = $this->getBookingsToProcess();
            
            if ($bookings->count() == 0) {
                $this->info('ℹ️  No bookings found to process');
                return 0;
            }

            $this->info("📋 Found {$bookings->count()} bookings to process");
            $this->newLine();

            // Create progress bar
            $progressBar = $this->output->createProgressBar($bookings->count());
            $progressBar->start();

            foreach ($bookings as $booking) {
                try {
                    $result = $this->processBookingRecordings($booking);
                    $totalNewRecordings += $result['new_recordings'];
                    $totalProcessed++;
                } catch (\Exception $e) {
                    $errors++;
                    Log::error('Error processing booking recordings', [
                        'booking_id' => $booking->id,
                        'meeting_id' => $booking->zoom_meeting_id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Summary
            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info('🎉 Recording Fetch Complete!');
            $this->info('============================');
            $this->info("📊 Processed: {$totalProcessed} bookings");
            $this->info("🆕 New recordings: {$totalNewRecordings}");
            $this->info("❌ Errors: {$errors}");
            $this->info("⏱️  Duration: {$duration} seconds");

            // Log the summary
            Log::info('Zoom recording fetch completed', [
                'total_processed' => $totalProcessed,
                'new_recordings' => $totalNewRecordings,
                'errors' => $errors,
                'duration' => $duration
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Fatal error: ' . $e->getMessage());
            Log::error('Fatal error in zoom:fetch-recordings command', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Get bookings to process based on options
     */
    private function getBookingsToProcess()
    {
        $query = Booking::whereNotNull('zoom_meeting_id');

        // If specific meeting ID provided
        if ($meetingId = $this->option('meeting-id')) {
            $query->where('zoom_meeting_id', $meetingId);
            $this->info("🎯 Targeting specific meeting ID: {$meetingId}");
        } else {
            // Only process recent bookings unless forced
            $hours = (int) $this->option('hours');
            
            if (!$this->option('force')) {
                $cutoffTime = Carbon::now()->subHours($hours);
                $query->where('start_time', '>=', $cutoffTime);
                $this->info("📅 Processing bookings from last {$hours} hours");
            } else {
                $this->info("🔄 Force mode: Processing all bookings");
            }
        }

        return $query->get();
    }

    /**
     * Process recordings for a single booking
     */
    private function processBookingRecordings($booking)
    {
        $meetingId = $booking->zoom_meeting_id;
        $newRecordings = 0;

        // Get recordings from Zoom API
        $zoomRecordings = $this->zoomService->getMeetingRecordings($meetingId);

        if (!$zoomRecordings || !isset($zoomRecordings['recording_files'])) {
            return ['new_recordings' => 0];
        }

        $recordingFiles = $zoomRecordings['recording_files'];

        // Process each recording file
        foreach ($recordingFiles as $recordingFile) {
            // Check if recording already exists
            $existingRecording = SessionRecording::where([
                'zoom_meeting_id' => $meetingId,
                'recording_id' => $recordingFile['id'] ?? null
            ])->first();

            if (!$existingRecording) {
                // Extract passcode from recording file or meeting data
                $passcode = $this->extractPasscode($recordingFile, $zoomRecordings);
                
                // Create new recording record
                SessionRecording::create([
                    'booking_id' => $booking->id,
                    'zoom_meeting_id' => $meetingId,
                    'recording_id' => $recordingFile['id'] ?? 'REC_' . rand(100000000, 999999999),
                    'recording_type' => $recordingFile['recording_type'] ?? 'video',
                    'file_name' => $recordingFile['file_name'] ?? 'Session_Recording_' . $booking->id . '.mp4',
                    'download_url' => $recordingFile['download_url'] ?? '',
                    'play_url' => $recordingFile['play_url'] ?? '',
                    'passcode' => $passcode,
                    'file_size' => $recordingFile['file_size'] ?? 0,
                    'duration' => $recordingFile['duration'] ?? 0,
                    'recording_start' => $booking->start_time,
                    'recording_end' => $booking->end_time,
                    'is_processed' => true,
                ]);

                $newRecordings++;

                // Log new recording
                Log::info('New Zoom recording created', [
                    'booking_id' => $booking->id,
                    'meeting_id' => $meetingId,
                    'recording_type' => $recordingFile['recording_type'] ?? 'video',
                    'file_name' => $recordingFile['file_name'] ?? 'Unknown',
                    'has_passcode' => !empty($passcode),
                    'passcode_source' => !empty($recordingFile['play_passcode']) ? 'recording_file' : 'meeting_data'
                ]);
            }
        }

        return ['new_recordings' => $newRecordings];
    }
}
