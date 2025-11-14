<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Services\ZoomService;

use App\Models\Booking;

use App\Models\SessionRecording;

use Illuminate\Support\Facades\Log;



class ZoomWebhookController extends Controller

{

    /**

     * Handle Zoom webhook validation (GET request)

     */

    public function validateWebhook(Request $request)

    {

        try {

            Log::info('Zoom webhook validation request received', [

                'method' => $request->method(),

                'headers' => $request->headers->all(),

                'query' => $request->query()

            ]);

            

            // Zoom sends a GET request with challenge parameter for validation

            $challenge = $request->query('challenge');

            

            if ($challenge) {

                Log::info('Zoom webhook validation successful', ['challenge' => $challenge]);

                return response($challenge, 200)

                    ->header('Content-Type', 'text/plain');

            }

            

            // If no challenge, return success anyway

            return response('Webhook endpoint is accessible', 200)

                ->header('Content-Type', 'text/plain');

                

        } catch (\Exception $e) {

            Log::error('Error in webhook validation', [

                'error' => $e->getMessage(),

                'request' => $request->all()

            ]);

            

            return response('Webhook validation failed', 500);

        }

    }

    

    /**

     * Handle Zoom webhook for meeting ended

     */

    public function handleMeetingEnded(Request $request)

    {

        try {

            // Verify webhook signature for security

            if (!$this->verifyWebhookSignature($request)) {

                Log::warning('Invalid Zoom webhook signature for meeting ended');

                return response()->json(['status' => 'unauthorized'], 401);

            }

            Log::info('Zoom meeting ended webhook received', ['payload' => $request->all()]);

            $payload = $request->all();

            // Verify this is a meeting ended event

            if ($payload['event'] !== 'meeting.ended') {

                return response()->json(['status' => 'ignored'], 200);

            }

            $meetingId = $payload['object']['id'];

            $endTime = $payload['object']['end_time'] ?? null;

            // Find the booking for this meeting

            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();

            if (!$booking) {

                Log::warning('Booking not found for ended meeting', ['meeting_id' => $meetingId]);

                return response()->json(['status' => 'booking_not_found'], 200);

            }

            // Mark booking as completed if it's not already

            if ($booking->status !== 'completed') {

                $booking->update([

                    'status' => 'completed',

                    'end_time' => $endTime ? \Carbon\Carbon::parse($endTime) : $booking->end_time

                ]);

                Log::info('Booking automatically marked as completed via webhook', [

                    'booking_id' => $booking->id,

                    'meeting_id' => $meetingId

                ]);

                // Send notification to student about completed session

                $this->sendSessionCompletedNotification($booking);

                // Schedule recording processing notification

                $this->scheduleRecordingFetch($booking);

            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {

            Log::error('Error processing meeting ended webhook', [

                'error' => $e->getMessage(),

                'payload' => $request->all()

            ]);

            return response()->json(['status' => 'error'], 500);

        }

    }

    

    /**

     * Handle Zoom webhook for recording completion

     */

    public function handleRecordingCompleted(Request $request)

    {

        try {

            // Verify webhook signature for security

            if (!$this->verifyWebhookSignature($request)) {

                Log::warning('Invalid Zoom webhook signature for recording completed');

                return response()->json(['status' => 'unauthorized'], 401);

            }

            Log::info('Zoom webhook received', ['payload' => $request->all()]);

            

            $payload = $request->all();

            

            // Verify this is a recording completed event

            if ($payload['event'] !== 'recording.completed') {

                return response()->json(['status' => 'ignored'], 200);

            }

            

            $meetingId = $payload['object']['id'];

            $recordingFiles = $payload['object']['recording_files'] ?? [];

            

            // Find the booking for this meeting

            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();

            

            if (!$booking) {

                Log::warning('Booking not found for Zoom meeting', ['meeting_id' => $meetingId]);

                return response()->json(['status' => 'booking_not_found'], 200);

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

                    

                    $newRecordings++;

                    

                    Log::info('New Zoom recording created via webhook', [

                        'booking_id' => $booking->id,

                        'meeting_id' => $meetingId,

                        'file_name' => $recordingFile['file_name'] ?? 'Unknown'

                    ]);

                }

            }

            

            Log::info('Zoom webhook processed successfully', [

                'meeting_id' => $meetingId,

                'new_recordings' => $newRecordings

            ]);

            

            return response()->json([

                'status' => 'success',

                'new_recordings' => $newRecordings

            ], 200);

            

        } catch (\Exception $e) {

            Log::error('Error processing Zoom webhook', [

                'error' => $e->getMessage(),

                'payload' => $request->all()

            ]);

            

            return response()->json(['status' => 'error'], 500);

        }

    }

    

    /**

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
                    
                            $newRecordings++;
                    
                            Log::info('New Zoom recording created via webhook', [
                                'booking_id' => $booking->id,
                                'meeting_id' => $meetingId,
                                'file_name' => $recordingFile['file_name'] ?? 'Unknown',
                                'passcode' => $recordingFile['play_passcode'] ?? null
                            ]);
                        }
                return response()->json(['status' => 'ignored'], 200);

            }

            

            $meetingId = $payload['object']['id'];

            

            // Find the booking for this meeting

            $booking = Booking::where('zoom_meeting_id', $meetingId)->first();

            

            if (!$booking) {

                Log::warning('Booking not found for ended meeting', ['meeting_id' => $meetingId]);

                return response()->json(['status' => 'booking_not_found'], 200);

            }

            

            // Mark booking as completed if it's not already

            if ($booking->status !== 'completed') {

                $booking->update(['status' => 'completed']);

                Log::info('Booking automatically marked as completed via webhook', [

                    'booking_id' => $booking->id,

                    'meeting_id' => $meetingId

                ]);

                

                // Send notification to student about completed session

                $this->sendSessionCompletedNotification($booking);

            }

            

            // Trigger recording fetch after a delay (recordings may not be ready immediately)

            // You can use Laravel's dispatch() method here if you have a queue system

            

            return response()->json(['status' => 'success'], 200);

            

        } catch (\Exception $e) {

            Log::error('Error processing meeting ended webhook', [

                'error' => $e->getMessage(),

                'payload' => $request->all()

            ]);

            

            return response()->json(['status' => 'error'], 500);

        }

    }

    

    /**

     * Verify Zoom webhook signature

     */

    private function verifyWebhookSignature(Request $request)

    {

        // Get webhook secret from database settings
        $webhookSecret = \App\Models\SystemSetting::getValue('zoom_webhook_secret');

        

        if (!$webhookSecret) {

            Log::warning('Zoom webhook secret not configured in system settings');

            return true; // Skip verification if not configured

        }

        

        $signature = $request->header('x-zoom-signature');

        $timestamp = $request->header('x-zoom-timestamp');

        $body = $request->getContent();

        

        $expectedSignature = hash_hmac('sha256', $body, $webhookSecret);

        

        return hash_equals($expectedSignature, $signature);

    }

    

    /**

     * Send notification to student about completed session

     */

    private function sendSessionCompletedNotification($booking)

    {

        try {

            // Create notification for student

            $teacherName = $booking->teacher && $booking->teacher->user ? $booking->teacher->user->name : 'Teacher';

            \App\Models\Notification::create([

                'user_id' => $booking->student_id,

                'type' => 'session_completed',

                'title' => 'Session Completed',

                'message' => "Your session with {$teacherName} has been completed successfully.",

                'data' => json_encode([

                    'booking_id' => $booking->id,

                    'teacher_name' => $teacherName,

                    'session_date' => $booking->start_time->format('M d, Y'),

                    'session_time' => $booking->start_time->format('h:i A')

                ])

            ]);

            

            Log::info('Session completed notification sent to student', [

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

     * Schedule recording fetch after meeting ends

     */

    private function scheduleRecordingFetch($booking)

    {

        try {

            // Schedule recording fetch after 5 minutes delay

            // This gives Zoom time to process the recordings

            $studentName = $booking->student && $booking->student->user ? $booking->student->user->name : 'Student';

            \App\Models\Notification::create([

                'user_id' => $booking->teacher_id,

                'type' => 'recording_processing',

                'title' => 'Recording Processing',

                'message' => "Recordings for your session with {$studentName} are being processed. They will be available shortly.",

                'data' => json_encode([

                    'booking_id' => $booking->id,

                    'student_name' => $studentName,

                    'meeting_id' => $booking->zoom_meeting_id

                ])

            ]);

            

            Log::info('Recording processing notification sent to teacher', [

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

}

