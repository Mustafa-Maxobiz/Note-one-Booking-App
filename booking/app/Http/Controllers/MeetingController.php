<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{

    /**
     * Handle teacher starting a meeting
     */
    public function startMeeting(Booking $booking)
    {
        // Verify user is the teacher for this booking
        if (!Auth::check() || Auth::user()->role != 'teacher' || !Auth::user()->teacher || Auth::user()->teacher->id != $booking->teacher_id) {
            abort(403, 'Unauthorized access');
        }

        // Check if booking has zoom details
        if (!$booking->zoom_start_url) {
            return redirect()->back()->with('error', 'Meeting details not available yet.');
        }

        try {
            // Send notification to student
            $student = $booking->student->user;
            $teacher = $booking->teacher->user;

            // Create in-app notification
            \App\Services\NotificationService::sendNotification(
                $student->id,
                'meeting_started',
                'Meeting Started',
                "Your teacher {$teacher->name} has started the meeting. Click to join!",
                ['booking_id' => $booking->id, 'join_url' => $booking->zoom_join_url]
            );

            // Send email to student
            \App\Services\EmailService::sendEmail(
                $student->email,
                'Meeting Started - Join Now!',
                [
                    'booking' => $booking,
                    'teacher' => $teacher,
                    'student' => $student,
                    'join_url' => $booking->zoom_join_url
                ],
                'emails.meeting-started-to-student'
            );

            Log::info('Meeting started notification sent', [
                'booking_id' => $booking->id,
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'teacher_name' => $teacher->name,
                'student_name' => $student->name
            ]);

            // Redirect to Zoom meeting
            return redirect($booking->zoom_start_url);

        } catch (\Exception $e) {
            Log::error('Failed to send meeting started notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            // Still redirect to Zoom even if notification fails
            return redirect($booking->zoom_start_url);
        }
    }

    /**
     * Handle student joining a meeting
     */
    public function joinMeeting(Booking $booking)
    {
        // Verify user is the student for this booking
        if (!Auth::check() || Auth::user()->role != 'student' || !Auth::user()->student || Auth::user()->student->id != $booking->student_id) {
            abort(403, 'Unauthorized access');
        }

        // Check if booking has zoom details
        if (!$booking->zoom_join_url) {
            return redirect()->back()->with('error', 'Meeting details not available yet.');
        }

        try {
            // Send notification to teacher
            $student = $booking->student->user;
            $teacher = $booking->teacher->user;

            // Create in-app notification
            \App\Services\NotificationService::sendNotification(
                $teacher->id,
                'student_joined_meeting',
                'Student Joined Meeting',
                "Your student {$student->name} has joined the meeting.",
                ['booking_id' => $booking->id, 'student_name' => $student->name]
            );

            // Send email to teacher
            \App\Services\EmailService::sendEmail(
                $teacher->email,
                'Student Joined Your Meeting',
                [
                    'booking' => $booking,
                    'teacher' => $teacher,
                    'student' => $student,
                    'start_url' => $booking->zoom_start_url
                ],
                'emails.student-joined-meeting-to-teacher'
            );

            Log::info('Student joined meeting notification sent', [
                'booking_id' => $booking->id,
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'teacher_name' => $teacher->name,
                'student_name' => $student->name
            ]);

            // Redirect to Zoom meeting
            return redirect($booking->zoom_join_url);

        } catch (\Exception $e) {
            Log::error('Failed to send student joined meeting notification', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            // Still redirect to Zoom even if notification fails
            return redirect($booking->zoom_join_url);
        }
    }
}
