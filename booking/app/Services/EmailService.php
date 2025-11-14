<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public static function sendEmail($to, $subject, $data, $template = 'emails.default')
    {
        try {
            // Send actual email using Laravel Mail with correct syntax
            Mail::html(view($template, $data)->render(), function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            Log::info('Email Sent Successfully', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public static function sendEmailFixed($to, $subject, $data, $template = 'emails.default')
    {
        try {
            // Send actual email using Laravel Mail with correct syntax
            Mail::html(view($template, $data)->render(), function($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            Log::info('Email Sent Successfully', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public static function sendBookingRequestToTeacher(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'teacher_name' => $teacher->name,
                'student_name' => $student->name,
                'booking_date' => $booking->start_time->format('M d, Y'),
                'booking_time' => $booking->start_time->format('g:i A'),
                'booking_duration' => $booking->duration_minutes . ' minutes',
                'accept_url' => route('teacher.bookings.show', $booking),
                'decline_url' => route('teacher.bookings.show', $booking),
                'booking_notes' => $booking->notes ?? 'No additional notes',
                'student_email' => $student->email,
                'student_phone' => $student->phone ?? 'Not provided',
            ];

            // Send actual email to teacher
            return self::sendEmail(
                $teacher->email,
                'New Session Booking Request - ' . $booking->start_time->format('M d, Y g:i A'),
                $data,
                'emails.booking-request-to-teacher'
            );

        } catch (\Exception $e) {
            Log::error('Failed to send booking request email to teacher: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingAcceptedToStudent(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student_name' => $student->name,
                'teacher_name' => $teacher->name,
                'session_date' => $booking->start_time->format('M d, Y'),
                'session_time' => $booking->start_time->format('g:i A'),
                'session_duration' => $booking->duration_minutes . ' minutes',
                'zoom_join_url' => $booking->zoom_join_url ?? 'Will be provided soon',
                'session_details_url' => route('student.bookings.show', $booking),
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Session Booking Accepted! - ' . $booking->start_time->format('M d, Y g:i A'),
                $data,
                'emails.booking-accepted-to-student'
            );

        } catch (\Exception $e) {
            Log::error('Failed to send booking accepted email to student: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingRescheduledToStudent(Booking $booking, $oldStartTime = null, $oldEndTime = null)
    {
        try {
            if (!$booking->teacher || !$booking->teacher->user || !$booking->student || !$booking->student->user) {
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student_name'    => $student->name,
                'teacher_name'    => $teacher->name,
                'old_date'        => $oldStartTime ? $oldStartTime->format('M d, Y') : null,
                'old_time_range'  => ($oldStartTime && $oldEndTime) ? ($oldStartTime->format('g:i A') . ' - ' . $oldEndTime->format('g:i A')) : null,
                'new_date'        => $booking->start_time->format('M d, Y'),
                'new_time_range'  => $booking->start_time->format('g:i A') . ' - ' . $booking->end_time->format('g:i A'),
                'duration'        => $booking->duration_minutes . ' minutes',
                'zoom_join_url'   => $booking->zoom_join_url,
                'booking_details_url' => route('student.bookings.show', $booking),
            ];

            // Use a generic template if a specific one doesn't exist
            return self::sendEmail(
                $student->email,
                'Session Rescheduled - New Time Confirmed',
                $data,
                'emails.booking-rescheduled'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send reschedule email to student: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingDeclinedToStudent(Booking $booking)
    {
        try {
            // Load relationships to ensure they exist
            $booking->load(['teacher.user', 'student.user']);
            
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking decline', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking decline', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;
            
            Log::info('Preparing to send decline email', [
                'booking_id' => $booking->id,
                'student_email' => $student->email,
                'teacher_name' => $teacher->name
            ]);

            $data = [
                'student_name' => $student->name,
                'teacher_name' => $teacher->name,
                'session_date' => $booking->start_time->format('M d, Y'),
                'session_time' => $booking->start_time->format('g:i A'),
                'booking_url' => route('student.booking.create'),
                'booking_date' => $booking->start_time->format('M d, Y'),
                'booking_time' => $booking->start_time->format('g:i A'),
                'booking_duration' => $booking->duration_minutes . ' minutes',
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Session Booking Declined - ' . $booking->start_time->format('M d, Y g:i A'),
                $data,
                'emails.booking-declined-to-student'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking declined email to student: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendZoomDetailsToStudent(Booking $booking)
    {
        try {
            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student_name' => $student->name,
                'teacher_name' => $teacher->name,
                'session_date' => $booking->start_time->format('M d, Y'),
                'session_time' => $booking->start_time->format('g:i A'),
                'zoom_join_url' => $booking->zoom_join_url,
                'zoom_meeting_id' => $booking->zoom_meeting_id,
                'session_details_url' => route('student.bookings.show', $booking),
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Zoom Meeting Details for Your Session',
                $data,
                'emails.booking-confirmation-with-zoom'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send zoom details email to student: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendSessionReminder(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking reminder', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking reminder', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student' => $booking->student,
                'teacher' => $booking->teacher,
                'booking' => $booking,
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Reminder: Your Session is Tomorrow',
                $data,
                'emails.session_reminder_24h'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send session reminder email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendUrgentReminder(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for urgent reminder', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for urgent reminder', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student' => $booking->student,
                'teacher' => $booking->teacher,
                'booking' => $booking,
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'URGENT: Your Session Starts in 1 Hour!',
                $data,
                'emails.session_reminder_1h'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send urgent reminder email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendTeacherReminder(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for teacher reminder', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for teacher reminder', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student' => $booking->student,
                'teacher' => $booking->teacher,
                'booking' => $booking,
            ];

            // Send actual email to teacher
            return self::sendEmail(
                $teacher->email,
                'Reminder: Your Teaching Session is Tomorrow',
                $data,
                'emails.teacher_reminder_24h'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send teacher reminder email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingUpdateToStudent(Booking $booking, $originalStartTime, $originalEndTime)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking update', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking update', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student_name' => $student->name,
                'teacher_name' => $teacher->name,
                'booking' => $booking,
                'original_start_time' => $originalStartTime,
                'original_end_time' => $originalEndTime,
                'new_start_time' => $booking->start_time,
                'new_end_time' => $booking->end_time,
                'original_date' => $originalStartTime->format('M d, Y'),
                'original_time' => $originalStartTime->format('g:i A'),
                'new_date' => $booking->start_time->format('M d, Y'),
                'new_time' => $booking->start_time->format('g:i A'),
                'duration' => $booking->duration_minutes . ' minutes',
                'notes' => $booking->notes ?: 'No additional notes'
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Session Schedule Updated - Important Changes',
                $data,
                'emails.booking-update-to-student'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking update email to student: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingUpdateToTeacher(Booking $booking, $originalStartTime, $originalEndTime)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking update', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking update', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'teacher_name' => $teacher->name,
                'student_name' => $student->name,
                'booking' => $booking,
                'original_start_time' => $originalStartTime,
                'original_end_time' => $originalEndTime,
                'new_start_time' => $booking->start_time,
                'new_end_time' => $booking->end_time,
                'original_date' => $originalStartTime->format('M d, Y'),
                'original_time' => $originalStartTime->format('g:i A'),
                'new_date' => $booking->start_time->format('M d, Y'),
                'new_time' => $booking->start_time->format('g:i A'),
                'duration' => $booking->duration_minutes . ' minutes',
                'notes' => $booking->notes ?: 'No additional notes'
            ];

            // Send actual email to teacher
            return self::sendEmail(
                $teacher->email,
                'Session Schedule Updated - Important Changes',
                $data,
                'emails.booking-update-to-teacher'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking update email to teacher: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendTeacherUrgentReminder(Booking $booking)
    {
        try {
            // Check if teacher and student have user records
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for teacher urgent reminder', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for teacher urgent reminder', ['booking_id' => $booking->id]);
                return false;
            }

            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student' => $booking->student,
                'teacher' => $booking->teacher,
                'booking' => $booking,
            ];

            // Send actual email to teacher
            return self::sendEmail(
                $teacher->email,
                'URGENT: Your Teaching Session Starts in 1 Hour!',
                $data,
                'emails.teacher_reminder_1h'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send teacher urgent reminder email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingConfirmation(Booking $booking)
    {
        try {
            $teacher = $booking->teacher->user;
            $student = $booking->student->user;

            $data = [
                'student_name' => $student->name,
                'teacher_name' => $teacher->name,
                'session_date' => $booking->start_time->format('M d, Y'),
                'session_time' => $booking->start_time->format('g:i A'),
                'session_duration' => $booking->duration_minutes . ' minutes',
                'session_details_url' => route('student.bookings.show', $booking),
            ];

            // Send actual email to student
            return self::sendEmail(
                $student->email,
                'Session Booking Confirmation',
                $data,
                'emails.booking-confirmation-with-zoom'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingConfirmationWithZoom(Booking $booking)
    {
        try {
            // Load relationships to ensure they exist
            $booking->load(['teacher.user', 'student.user']);
            
            if (!$booking->teacher || !$booking->teacher->user) {
                Log::error('Teacher user not found for booking confirmation', ['booking_id' => $booking->id]);
                return false;
            }
            
            if (!$booking->student || !$booking->student->user) {
                Log::error('Student user not found for booking confirmation', ['booking_id' => $booking->id]);
                return false;
            }
            
            $teacher = $booking->teacher->user;
            $student = $booking->student->user;
            
            Log::info('Preparing to send confirmation email', [
                'booking_id' => $booking->id,
                'student_email' => $student->email,
                'teacher_name' => $teacher->name
            ]);

            $data = [
                'student_name' => $student->name,
                'student_email' => $student->email,
                'teacher_name' => $teacher->name,
                'booking_date' => $booking->start_time->format('M d, Y'),
                'booking_time' => $booking->start_time->format('g:i A'),
                'booking_duration' => $booking->duration_minutes . ' minutes',
                'notes' => $booking->notes ?? 'No additional notes',
                'zoom_join_url' => $booking->zoom_join_url ?? 'Will be provided by your teacher',
                'zoom_meeting_id' => $booking->zoom_meeting_id ?? 'Will be provided by your teacher',
                'zoom_password' => $booking->zoom_password ?? 'Will be provided by your teacher',
                'booking_details_url' => route('student.bookings.show', $booking),
                'teacher_email' => $teacher->email,
                'teacher_phone' => $teacher->phone ?? 'Not provided',
            ];

            // Send email using the template
            return self::sendEmail(
                $student->email,
                'Session Confirmed - Zoom Meeting Details',
                $data,
                'emails.booking-confirmation-with-zoom'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation with zoom details email: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendBookingReminder(Booking $booking)
    {
        return self::sendSessionReminder($booking);
    }

    public static function sendZoomDetails(Booking $booking)
    {
        return self::sendZoomDetailsToStudent($booking);
    }

    public static function sendAccountCreationEmail(User $user, $password)
    {
        try {
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'password' => $password,
                'login_url' => route('login'),
                'dashboard_url' => $user->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'),
            ];

            // Send email using the template
            return self::sendEmail(
                $user->email,
                'Welcome to Online Lesson Booking System - Your Account Details',
                $data,
                'emails.account-creation'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send account creation email: ' . $e->getMessage());
            return false;
        }
    }

}
