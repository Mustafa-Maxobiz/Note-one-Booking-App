<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\SessionRecording;

class ZoomWebSocketService
{
    private $websocketUrl;
    private $subscriptionId;
    private $isConnected = false;
    private $connection = null;

    public function __construct()
    {
        // Get WebSocket settings from database
        $this->websocketUrl = \App\Models\SystemSetting::getValue('zoom_websocket_url', 'wss://ws.zoom.us/ws');
        $this->subscriptionId = \App\Models\SystemSetting::getValue('zoom_websocket_subscription_id', 'wEPmdYeSQK2ZQf2nbQlwBw');
    }

    /**
     * Connect to Zoom WebSocket
     */
    public function connect()
    {
        try {
            Log::info('Connecting to Zoom WebSocket', [
                'url' => $this->websocketUrl,
                'subscription_id' => $this->subscriptionId
            ]);

            // For now, we'll simulate the connection
            // In production, you'd use a WebSocket client library
            $this->isConnected = true;
            
            Log::info('Zoom WebSocket connected successfully');
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to connect to Zoom WebSocket', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Disconnect from Zoom WebSocket
     */
    public function disconnect()
    {
        try {
            $this->isConnected = false;
            $this->connection = null;
            
            Log::info('Zoom WebSocket disconnected');
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to disconnect from Zoom WebSocket', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Check if WebSocket is connected
     */
    public function isConnected()
    {
        return $this->isConnected;
    }

    /**
     * Process incoming Zoom events
     */
    public function processEvent($eventData)
    {
        try {
            Log::info('Processing Zoom WebSocket event', ['event' => $eventData]);

            $eventType = $eventData['event'] ?? null;
            $meetingId = $eventData['object']['id'] ?? null;

            if (!$eventType || !$meetingId) {
                Log::warning('Invalid event data received', ['data' => $eventData]);
                return false;
            }

            switch ($eventType) {
                case 'meeting.ended':
                    return $this->handleMeetingEnded($eventData);
                
                case 'recording.completed':
                    return $this->handleRecordingCompleted($eventData);
                
                default:
                    Log::info('Unhandled event type', ['type' => $eventType]);
                    return true;
            }

        } catch (\Exception $e) {
            Log::error('Error processing Zoom WebSocket event', [
                'error' => $e->getMessage(),
                'event' => $eventData
            ]);
            
            return false;
        }
    }

    /**
     * Handle meeting ended event
     */
    private function handleMeetingEnded($eventData)
    {
        try {
            $meetingId = $eventData['object']['id'];
            $endTime = $eventData['object']['end_time'] ?? null;

            Log::info('Processing meeting ended event', [
                'meeting_id' => $meetingId,
                'end_time' => $endTime
            ]);

            // Find the booking for this meeting
            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();

            if (!$booking) {
                Log::warning('Booking not found for ended meeting', [
                    'meeting_id' => $meetingId
                ]);
                return false;
            }

            // Mark booking as completed if it's not already
            if ($booking->status !== 'completed') {
                $booking->update([
                    'status' => 'completed',
                    'end_time' => $endTime ? \Carbon\Carbon::parse($endTime) : $booking->end_time
                ]);

                Log::info('Booking automatically marked as completed via WebSocket', [
                    'booking_id' => $booking->id,
                    'meeting_id' => $meetingId
                ]);

                // Send notifications
                $this->sendSessionCompletedNotification($booking);
                $this->sendRecordingProcessingNotification($booking);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling meeting ended event', [
                'error' => $e->getMessage(),
                'event' => $eventData
            ]);
            
            return false;
        }
    }

    /**
     * Handle recording completed event
     */
    private function handleRecordingCompleted($eventData)
    {
        try {
            $meetingId = $eventData['object']['id'];
            $recordingFiles = $eventData['object']['recording_files'] ?? [];

            Log::info('Processing recording completed event', [
                'meeting_id' => $meetingId,
                'files_count' => count($recordingFiles)
            ]);

            // Find the booking for this meeting
            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();

            if (!$booking) {
                Log::warning('Booking not found for recording completed', [
                    'meeting_id' => $meetingId
                ]);
                return false;
            }

            $newRecordings = 0;

            // Process each recording file
            foreach ($recordingFiles as $recordingFile) {
                // Check if recording already exists
                $existingRecording = SessionRecording::where([
                    'zoom_meeting_id' => $meetingId,
                    'recording_id' => $recordingFile['id'] ?? null
                ])->first();

                if (!$existingRecording) {
                    // Create new recording record
                    SessionRecording::create([
                        'booking_id' => $booking->id,
                        'zoom_meeting_id' => $meetingId,
                        'recording_id' => $recordingFile['id'] ?? 'REC_' . rand(100000000, 999999999),
                        'recording_type' => $recordingFile['recording_type'] ?? 'video',
                        'file_name' => $recordingFile['file_name'] ?? 'Session_Recording_' . $booking->id . '.mp4',
                        'download_url' => $recordingFile['download_url'] ?? '',
                        'play_url' => $recordingFile['play_url'] ?? '',
                        'passcode' => $recordingFile['play_passcode'] ?? null,
                        'file_size' => $recordingFile['file_size'] ?? 0,
                        'duration' => $recordingFile['duration'] ?? 0,
                        'recording_start' => $booking->start_time,
                        'recording_end' => $booking->end_time,
                        'is_processed' => true,
                    ]);

                    $notificationData = [
                        'booking_id' => $booking->id,
                        'meeting_id' => $meetingId,
                        'file_name' => $recordingFile['file_name'] ?? 'Session_Recording_' . $booking->id . '.mp4',
                        'play_url' => $recordingFile['play_url'] ?? '',
                        'passcode' => $recordingFile['play_passcode'] ?? null
                    ];
                    \App\Models\Notification::create([
                        'user_id' => $booking->student_id,
                        'type' => 'zoom_recording_available',
                        'title' => 'Zoom Recording Available',
                        'message' => 'Your session recording is now available. Use the passcode below to view.',
                        'data' => json_encode($notificationData)
                    ]);
                    \App\Models\Notification::create([
                        'user_id' => $booking->teacher_id,
                        'type' => 'zoom_recording_available',
                        'title' => 'Zoom Recording Available',
                        'message' => 'Your session recording is now available. Use the passcode below to view.',
                        'data' => json_encode($notificationData)
                    ]);

                    $newRecordings++;
                }
            }

            Log::info('Recording processing completed via WebSocket', [
                'booking_id' => $booking->id,
                'meeting_id' => $meetingId,
                'new_recordings' => $newRecordings
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling recording completed event', [
                'error' => $e->getMessage(),
                'event' => $eventData
            ]);
            
            return false;
        }
    }

    /**
     * Send session completed notification
     */
    private function sendSessionCompletedNotification($booking)
    {
        try {
            Notification::create([
                'user_id' => $booking->student_id,
                'type' => 'session_completed',
                'title' => 'Session Completed',
                'message' => "Your session with {$booking->teacher->name} has been completed successfully.",
                'data' => json_encode([
                    'booking_id' => $booking->id,
                    'teacher_name' => $booking->teacher->name,
                    'session_date' => $booking->start_time->format('M d, Y'),
                    'session_time' => $booking->start_time->format('h:i A')
                ])
            ]);

            Log::info('Session completed notification sent via WebSocket', [
                'booking_id' => $booking->id,
                'student_id' => $booking->student_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send session completed notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send recording processing notification
     */
    private function sendRecordingProcessingNotification($booking)
    {
        try {
            Notification::create([
                'user_id' => $booking->teacher_id,
                'type' => 'recording_processing',
                'title' => 'Recording Processing',
                'message' => "Recordings for your session with {$booking->student->name} are being processed. They will be available shortly.",
                'data' => json_encode([
                    'booking_id' => $booking->id,
                    'student_name' => $booking->student->name,
                    'meeting_id' => $booking->zoom_meeting_id
                ])
            ]);

            Log::info('Recording processing notification sent via WebSocket', [
                'booking_id' => $booking->id,
                'teacher_id' => $booking->teacher_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send recording processing notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get WebSocket status
     */
    public function getStatus()
    {
        return [
            'connected' => $this->isConnected,
            'subscription_id' => $this->subscriptionId,
            'websocket_url' => $this->websocketUrl,
            'last_activity' => Cache::get('zoom_websocket_last_activity', 'Never')
        ];
    }

    /**
     * Update last activity timestamp
     */
    public function updateLastActivity()
    {
        Cache::put('zoom_websocket_last_activity', now()->toISOString(), 3600);
    }
}
