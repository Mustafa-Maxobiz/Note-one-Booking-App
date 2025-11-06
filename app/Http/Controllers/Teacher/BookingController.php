<?php



namespace App\Http\Controllers\Teacher;



use App\Http\Controllers\Controller;

use App\Models\Booking;

use App\Models\Teacher;

use App\Models\SystemSetting;

use App\Services\NotificationService;

use App\Services\EmailService;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;



class BookingController extends Controller

{

    public function index()

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        

        $bookings = Booking::where('teacher_id', $teacher->id)

            ->with(['student.user'])

            ->orderBy('id', 'desc')

            ->paginate(10);



        return view('teacher.bookings.index', compact('bookings'));

    }



    public function show($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to view this booking.');

        }



        $booking->load(['student.user', 'teacher.user', 'feedback', 'sessionRecordings']);

        

        return view('teacher.bookings.show', compact('booking'));

    }



    public function accept($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to accept this booking.');

        }



        // Create Zoom meeting

        $zoomService = new \App\Services\ZoomService();

        $student = $booking->student->user;

        $teacherUser = $booking->teacher->user;

        

        $meetingTopic = "Session with {$student->name} - {$booking->start_time->format('M d, Y g:i A')}";

        // Pass the Carbon instance directly to ZoomService, it will handle timezone conversion
        $startTime = $booking->start_time;

        

        $zoomMeeting = $zoomService->createMeeting($meetingTopic, $startTime, $booking->duration_minutes);

        

        if ($zoomMeeting) {

            // Update booking with Zoom details
            // ALWAYS extract meeting ID from join URL to ensure consistency
            $zoomMeeting = $this->extractCorrectMeetingId($zoomMeeting);
            $meetingId = $zoomMeeting['id'];

            $booking->update([

                'status' => 'confirmed',

                'zoom_meeting_id' => $meetingId,

                'zoom_join_url' => $zoomMeeting['join_url'],

                'zoom_start_url' => $zoomMeeting['start_url'],

                'zoom_password' => $zoomMeeting['password'],

            ]);

            

            \Log::info('Zoom meeting created successfully', [

                'booking_id' => $booking->id,

                'zoom_meeting_id' => $zoomMeeting['id'],

                'zoom_join_url' => $zoomMeeting['join_url']

            ]);

        } else {

            // If Zoom meeting creation fails, still accept the booking but log the error

            $booking->update(['status' => 'confirmed']);

            \Log::error('Failed to create Zoom meeting for booking', [

                'booking_id' => $booking->id,

                'teacher_id' => $teacher->id,

                'student_id' => $booking->student_id

            ]);

        }

        

        // Send notification to student

        NotificationService::bookingAccepted($booking);

        

        // Send confirmation email with Zoom details to student

        EmailService::sendBookingConfirmationWithZoom($booking);

        

        $successMessage = $zoomMeeting 

            ? 'Booking accepted successfully. Zoom meeting created and confirmation email sent to student.'

            : 'Booking accepted successfully. Confirmation email sent to student. (Zoom meeting creation failed - please create manually)';

        

        return redirect()->route('teacher.bookings.show', $booking)

            ->with('success', $successMessage);

    }



    public function decline($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to decline this booking.');

        }



        // Check if cancellation is allowed (based on system setting)
        $cancellationPolicyHours = SystemSetting::getValue('cancellation_policy_hours', 24);
        $hoursUntilStart = now()->diffInHours($booking->start_time, false);
        if ($hoursUntilStart <= $cancellationPolicyHours && $hoursUntilStart > 0) {
            return redirect()->route('teacher.bookings.show', $booking)
                ->with('error', "Cancellations are not allowed within {$cancellationPolicyHours} hours of the scheduled time.");
        }

        $booking->update(['status' => 'cancelled']);

        // Delete/End Zoom meeting if it exists
        if ($booking->zoom_meeting_id) {
            try {
                $zoomService = new \App\Services\ZoomService();
                
                // Try to delete the meeting first
                $deleteResult = $zoomService->deleteMeeting($booking->zoom_meeting_id);
                
                if ($deleteResult) {
                    \Log::info('Zoom meeting deleted on individual decline', [
                        'booking_id' => $booking->id,
                        'meeting_id' => $booking->zoom_meeting_id
                    ]);
                } else {
                    // If deletion fails, try to end the meeting
                    $endResult = $zoomService->endMeeting($booking->zoom_meeting_id);
                    
                    if ($endResult) {
                        \Log::info('Zoom meeting ended on individual decline', [
                            'booking_id' => $booking->id,
                            'meeting_id' => $booking->zoom_meeting_id
                        ]);
                    }
                }
                
                // Clear Zoom details from booking
                $booking->update([
                    'zoom_meeting_id' => null,
                    'zoom_join_url' => null,
                    'zoom_start_url' => null,
                    'zoom_password' => null
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to handle Zoom meeting on individual decline', [
                    'booking_id' => $booking->id,
                    'meeting_id' => $booking->zoom_meeting_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send notification to student

        NotificationService::bookingDeclined($booking);

        

        return redirect()->route('teacher.bookings.show', $booking)

            ->with('success', 'Booking declined successfully.');

    }



    public function complete($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to complete this booking.');

        }

        // Check if the meeting start time has passed
        if ($booking->start_time > now()) {
            return redirect()->route('teacher.bookings.show', $booking)
                ->with('error', 'Cannot mark session as completed before the meeting start time. Meeting starts at ' . $booking->start_time->format('M d, Y g:i A'));
        }

        $booking->update(['status' => 'completed']);

        // End the Zoom meeting if it exists and is still active
        if ($booking->zoom_meeting_id) {
            try {
                $zoomService = new \App\Services\ZoomService();
                $result = $zoomService->endMeeting($booking->zoom_meeting_id);
                
                if ($result) {
                    Log::info('Zoom meeting ended when booking marked as completed', [
                        'booking_id' => $booking->id,
                        'meeting_id' => $booking->zoom_meeting_id
                    ]);
                } else {
                    Log::warning('Zoom meeting end failed - may need additional scopes', [
                        'booking_id' => $booking->id,
                        'meeting_id' => $booking->zoom_meeting_id,
                        'note' => 'Booking still marked as completed. Zoom meeting may need to be ended manually.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to end Zoom meeting when marking booking as completed', [
                    'booking_id' => $booking->id,
                    'meeting_id' => $booking->zoom_meeting_id,
                    'error' => $e->getMessage(),
                    'note' => 'Booking still marked as completed. Zoom meeting may need to be ended manually.'
                ]);
            }
        }

        // Send notification to student
        NotificationService::bookingCompleted($booking);

        

        return redirect()->route('teacher.bookings.show', $booking)

            ->with('success', 'Session marked as completed.');

    }



    public function edit($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to edit this booking.');

        }



        return view('teacher.bookings.edit', compact('booking'));

    }



    public function update(Request $request, $id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to update this booking.');

        }



        $validated = $request->validate([

            'start_date' => 'required|date|after_or_equal:today',

            'start_time' => 'required|date_format:H:i',

            'duration_minutes' => 'required|in:30,60,90,120,150,180,240,300,360,480',

            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',

            'notes' => 'nullable|string|max:1000',

            'zoom_join_url' => 'nullable|url|max:500',

        ]);

        // Combine date and time into a single datetime
        $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['start_date'] . ' ' . $validated['start_time']);
        
        // Check if the combined datetime is in the future
        if ($startDateTime->isPast()) {
            return redirect()->route('teacher.bookings.edit', $booking)
                ->with('error', 'The selected date and time must be in the future.')
                ->withInput();
        }
        
        // Update the validated data with the combined datetime
        $validated['start_time'] = $startDateTime;

        // Capture old data for comparison/notifications
        $oldStartTime = $booking->start_time?->copy();
        $oldEndTime = $booking->end_time?->copy();
        $oldStatus = $booking->status;

        // Recalculate end_time based on provided start_time and duration
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes((int) $validated['duration_minutes']);

        $booking->update(array_merge($validated, [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]));

        // If time changed, notify student and update Zoom
        $timeChanged = !$oldStartTime || !$oldEndTime || !$oldStartTime->equalTo($booking->start_time) || !$oldEndTime->equalTo($booking->end_time);

        if ($timeChanged) {
            // In-app notification
            \App\Services\NotificationService::sendNotification(
                $booking->student->user_id,
                'booking_rescheduled',
                'Session Rescheduled',
                sprintf(
                    'Your session with %s has been rescheduled to %s (%d minutes).',
                    $booking->teacher->user->name,
                    $booking->start_time->format('M d, Y g:i A'),
                    $booking->duration_minutes
                ),
                ['booking_id' => $booking->id]
            );

            // Email to student
            if (class_exists(\App\Services\EmailService::class)) {
                \App\Services\EmailService::sendBookingRescheduledToStudent($booking, $oldStartTime, $oldEndTime);
            }

            // Update Zoom meeting time if exists
            if (!empty($booking->zoom_meeting_id)) {
                try {
                    $zoom = new \App\Services\ZoomService();
                    // Format ISO8601 UTC
                    $utcStart = (clone $booking->start_time)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
                    $zoom->updateMeeting($booking->zoom_meeting_id, [
                        'start_time' => $utcStart,
                        'duration' => $booking->duration_minutes,
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to update Zoom meeting on reschedule', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Handle status changes
        $statusChanged = $oldStatus !== $validated['status'];
        if ($statusChanged) {
            // Check cancellation policy for cancellations (based on system setting)
            if ($validated['status'] === 'cancelled') {
                $cancellationPolicyHours = SystemSetting::getValue('cancellation_policy_hours', 24);
                $hoursUntilStart = now()->diffInHours($booking->start_time, false);
                if ($hoursUntilStart <= $cancellationPolicyHours && $hoursUntilStart > 0) {
                    return redirect()->route('teacher.bookings.edit', $booking)
                        ->with('error', "Cancellations are not allowed within {$cancellationPolicyHours} hours of the scheduled time.")
                        ->withInput();
                }
            }
            
            // Log status change for debugging
            \Log::info('Booking status changed', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'student_email' => $booking->student->user->email ?? 'No student email',
                'teacher_email' => $booking->teacher->user->email ?? 'No teacher email'
            ]);
            
            switch ($validated['status']) {
                case 'confirmed':
                    // Send confirmation email and notification for any status change to confirmed
                    \Log::info('Sending confirmation email', ['booking_id' => $booking->id, 'old_status' => $oldStatus]);
                    \App\Services\NotificationService::bookingAccepted($booking);
                    $emailResult = \App\Services\EmailService::sendBookingConfirmationWithZoom($booking);
                    \Log::info('Confirmation email result', ['booking_id' => $booking->id, 'email_sent' => $emailResult]);
                    break;
                    
                case 'cancelled':
                    // Send cancellation email and notification
                    if (in_array($oldStatus, ['pending', 'confirmed'])) {
                        \Log::info('Sending cancellation email', ['booking_id' => $booking->id]);
                        \App\Services\NotificationService::bookingDeclined($booking);
                        $emailResult = \App\Services\EmailService::sendBookingDeclinedToStudent($booking);
                        \Log::info('Cancellation email result', ['booking_id' => $booking->id, 'email_sent' => $emailResult]);
                        
                        // Delete/End Zoom meeting if it exists
                        if ($booking->zoom_meeting_id) {
                            try {
                                $zoomService = new \App\Services\ZoomService();
                                
                                // Try to delete the meeting first (permanent removal)
                                $deleteResult = $zoomService->deleteMeeting($booking->zoom_meeting_id);
                                
                                if ($deleteResult) {
                                    \Log::info('Zoom meeting deleted successfully on cancellation', [
                                        'booking_id' => $booking->id,
                                        'meeting_id' => $booking->zoom_meeting_id
                                    ]);
                                } else {
                                    // If deletion fails, try to end the meeting instead
                                    \Log::warning('Zoom meeting deletion failed, trying to end meeting', [
                                        'booking_id' => $booking->id,
                                        'meeting_id' => $booking->zoom_meeting_id
                                    ]);
                                    
                                    $endResult = $zoomService->endMeeting($booking->zoom_meeting_id);
                                    
                                    if ($endResult) {
                                        \Log::info('Zoom meeting ended successfully on cancellation', [
                                            'booking_id' => $booking->id,
                                            'meeting_id' => $booking->zoom_meeting_id
                                        ]);
                                    } else {
                                        \Log::error('Both Zoom meeting deletion and ending failed on cancellation', [
                                            'booking_id' => $booking->id,
                                            'meeting_id' => $booking->zoom_meeting_id,
                                            'note' => 'Meeting may need to be manually deleted/ended in Zoom'
                                        ]);
                                    }
                                }
                                
                                // Clear Zoom details from booking
                                $booking->update([
                                    'zoom_meeting_id' => null,
                                    'zoom_join_url' => null,
                                    'zoom_start_url' => null,
                                    'zoom_password' => null
                                ]);
                                
                            } catch (\Exception $e) {
                                \Log::error('Failed to handle Zoom meeting on cancellation', [
                                    'booking_id' => $booking->id,
                                    'meeting_id' => $booking->zoom_meeting_id,
                                    'error' => $e->getMessage(),
                                    'note' => 'Booking cancelled but Zoom meeting may still exist'
                                ]);
                            }
                        }
                    }
                    break;
                    
                case 'completed':
                    // Send completion notification
                    if ($oldStatus === 'confirmed') {
                        \Log::info('Sending completion notification', ['booking_id' => $booking->id]);
                        \App\Services\NotificationService::bookingCompleted($booking);
                    }
                    break;
            }
        }

        return redirect()->route('teacher.bookings.show', $booking)

            ->with('success', 'Booking updated successfully.');

    }



    // Additional resource methods (not used but required for resource routes)

    public function create()

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        

        return redirect()->route('teacher.bookings.index');

    }



    public function store(Request $request)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        

        return redirect()->route('teacher.bookings.index');

    }



    public function destroy($id)

    {

        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('teacher.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this teacher

        if ($booking->teacher_id != $teacher->id) {

            return redirect()->route('teacher.bookings.index')
                ->with('error', 'You do not have permission to delete this booking.');

        }



        $booking->update(['status' => 'cancelled']);

        

        return redirect()->route('teacher.bookings.index')

            ->with('success', 'Booking cancelled successfully.');

    }



    public function bulkActions(Request $request)

    {

        $request->validate([

            'action' => 'required|in:accept,decline,complete',

            'booking_ids' => 'required|array|min:1',

            'booking_ids.*' => 'exists:bookings,id'

        ]);



        $teacher = Auth::user()->teacher;

        

        // Check if teacher profile exists

        if (!$teacher) {

            return response()->json([

                'success' => false,

                'message' => 'Please complete your teacher profile first.'

            ], 403);

        }

        $bookingIds = $request->input('booking_ids');

        $action = $request->input('action');

        $updatedCount = 0;



        try {

            // Ensure all bookings belong to this teacher

            $bookings = Booking::whereIn('id', $bookingIds)

                ->where('teacher_id', $teacher->id)

                ->get();



            if ($bookings->count() !== count($bookingIds)) {

                return response()->json([

                    'success' => false,

                    'message' => 'Some bookings do not belong to you or do not exist.'

                ], 403);

            }



            switch ($action) {

                case 'accept':

                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->where('teacher_id', $teacher->id)

                        ->where('status', 'pending')

                        ->update(['status' => 'confirmed']);

                    

                                         // Send notifications and confirmation emails for accepted bookings

                     $acceptedBookings = Booking::whereIn('id', $bookingIds)

                         ->where('teacher_id', $teacher->id)

                         ->where('status', 'confirmed')

                         ->get();

                     

                     foreach ($acceptedBookings as $booking) {

                         NotificationService::bookingAccepted($booking);

                         EmailService::sendBookingConfirmationWithZoom($booking);

                     }

                    

                    $message = "{$updatedCount} booking(s) accepted successfully.";

                    break;



                case 'decline':

                    // Check 24-hour rule for each booking before declining
                    $bookingsToDecline = Booking::whereIn('id', $bookingIds)
                        ->where('teacher_id', $teacher->id)
                        ->where('status', 'pending')
                        ->get();

                    $validBookings = [];
                    $invalidBookings = [];

                    $cancellationPolicyHours = SystemSetting::getValue('cancellation_policy_hours', 24);
                    
                    foreach ($bookingsToDecline as $booking) {
                        $hoursUntilStart = now()->diffInHours($booking->start_time, false);
                        if ($hoursUntilStart <= $cancellationPolicyHours && $hoursUntilStart > 0) {
                            $invalidBookings[] = $booking;
                        } else {
                            $validBookings[] = $booking->id;
                        }
                    }

                    if (!empty($invalidBookings)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Some bookings cannot be declined within {$cancellationPolicyHours} hours of the scheduled time."
                        ], 400);
                    }

                    $updatedCount = Booking::whereIn('id', $validBookings)
                        ->where('teacher_id', $teacher->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);

                    

                    // Send notifications for declined bookings

                    $declinedBookings = Booking::whereIn('id', $bookingIds)

                        ->where('teacher_id', $teacher->id)

                        ->where('status', 'cancelled')

                        ->get();

                    

                    foreach ($declinedBookings as $booking) {
                        NotificationService::bookingDeclined($booking);
                        
                        // Delete/End Zoom meeting if it exists
                        if ($booking->zoom_meeting_id) {
                            try {
                                $zoomService = new \App\Services\ZoomService();
                                
                                // Try to delete the meeting first
                                $deleteResult = $zoomService->deleteMeeting($booking->zoom_meeting_id);
                                
                                if ($deleteResult) {
                                    \Log::info('Zoom meeting deleted in bulk decline', [
                                        'booking_id' => $booking->id,
                                        'meeting_id' => $booking->zoom_meeting_id
                                    ]);
                                } else {
                                    // If deletion fails, try to end the meeting
                                    $endResult = $zoomService->endMeeting($booking->zoom_meeting_id);
                                    
                                    if ($endResult) {
                                        \Log::info('Zoom meeting ended in bulk decline', [
                                            'booking_id' => $booking->id,
                                            'meeting_id' => $booking->zoom_meeting_id
                                        ]);
                                    }
                                }
                                
                                // Clear Zoom details from booking
                                $booking->update([
                                    'zoom_meeting_id' => null,
                                    'zoom_join_url' => null,
                                    'zoom_start_url' => null,
                                    'zoom_password' => null
                                ]);
                                
                            } catch (\Exception $e) {
                                \Log::error('Failed to handle Zoom meeting in bulk decline', [
                                    'booking_id' => $booking->id,
                                    'meeting_id' => $booking->zoom_meeting_id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }

                    

                    $message = "{$updatedCount} booking(s) declined successfully.";

                    break;



                case 'complete':

                    // Only allow completion for bookings where start time has passed
                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->where('teacher_id', $teacher->id)

                        ->where('status', 'confirmed')

                        ->where('start_time', '<=', now())

                        ->update(['status' => 'completed']);

                    

                    // Send notifications for completed bookings and end Zoom meetings
                    $completedBookings = Booking::whereIn('id', $bookingIds)

                        ->where('teacher_id', $teacher->id)

                        ->where('status', 'completed')

                        ->get();

                    

                    foreach ($completedBookings as $booking) {
                        // End the Zoom meeting if it exists
                        if ($booking->zoom_meeting_id) {
                            try {
                                $zoomService = new \App\Services\ZoomService();
                                $result = $zoomService->endMeeting($booking->zoom_meeting_id);
                                
                                if ($result) {
                                    Log::info('Zoom meeting ended in bulk complete', [
                                        'booking_id' => $booking->id,
                                        'meeting_id' => $booking->zoom_meeting_id
                                    ]);
                                } else {
                                    Log::warning('Zoom meeting end failed in bulk complete - may need additional scopes', [
                                        'booking_id' => $booking->id,
                                        'meeting_id' => $booking->zoom_meeting_id,
                                        'note' => 'Booking still marked as completed. Zoom meeting may need to be ended manually.'
                                    ]);
                                }
                            } catch (\Exception $e) {
                                Log::error('Failed to end Zoom meeting in bulk complete', [
                                    'booking_id' => $booking->id,
                                    'meeting_id' => $booking->zoom_meeting_id,
                                    'error' => $e->getMessage(),
                                    'note' => 'Booking still marked as completed. Zoom meeting may need to be ended manually.'
                                ]);
                            }
                        }

                        NotificationService::bookingCompleted($booking);
                    }

                    

                    $message = "{$updatedCount} booking(s) marked as completed successfully.";

                    break;



                default:

                    return response()->json([

                        'success' => false,

                        'message' => 'Invalid action specified.'

                    ], 400);

            }



            return response()->json([

                'success' => true,

                'message' => $message,

                'updated_count' => $updatedCount

            ]);



        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => 'An error occurred while performing the bulk action: ' . $e->getMessage()

            ], 500);

        }

    }

    /**
     * Extract correct meeting ID from join URL to prevent negative IDs
     */
    private function extractCorrectMeetingId($meeting)
    {
        if (!isset($meeting['join_url'])) {
            \Log::warning('Zoom meeting response missing join_url', ['meeting' => $meeting]);
            return $meeting;
        }

        // Extract meeting ID from join URL using regex
        if (preg_match('/\/j\/(\d+)/', $meeting['join_url'], $matches)) {
            $correctMeetingId = $matches[1];
            
            // Always use the ID from URL, even if API returned a different one
            if (isset($meeting['id']) && $meeting['id'] != $correctMeetingId) {
                \Log::info('Zoom meeting ID corrected from join URL', [
                    'original_id' => $meeting['id'],
                    'corrected_id' => $correctMeetingId,
                    'join_url' => $meeting['join_url']
                ]);
            }
            
            $meeting['id'] = $correctMeetingId;
            
            // Validate that the ID is positive
            if ($correctMeetingId < 0) {
                \Log::error('Extracted meeting ID is negative', [
                    'meeting_id' => $correctMeetingId,
                    'join_url' => $meeting['join_url']
                ]);
            }
        } else {
            \Log::error('Could not extract meeting ID from join URL', [
                'join_url' => $meeting['join_url']
            ]);
        }

        return $meeting;
    }

}

