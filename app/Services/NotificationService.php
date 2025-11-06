<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function sendNotification($userId, $type, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function lessonBooked(Booking $booking)
    {
        // Notify teacher
        self::sendNotification(
            $booking->teacher->user_id,
            'booking_created',
            'New Booking Request',
            "You have a new booking request from {$booking->student->user->name} on " . $booking->start_time->format('M d, Y g:i A'),
            ['booking_id' => $booking->id, 'student_id' => $booking->student_id]
        );

        // Notify student
        self::sendNotification(
            $booking->student->user_id,
            'booking_created',
            'Booking Confirmation',
            "Your booking with {$booking->teacher->user->name} has been created for " . $booking->start_time->format('M d, Y g:i A'),
            ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
        );
    }

    public static function bookingAccepted(Booking $booking)
    {
        self::sendNotification(
            $booking->student->user_id,
            'booking_accepted',
            'Booking Accepted',
            "Great news! {$booking->teacher->user->name} has accepted your booking on " . $booking->start_time->format('M d, Y g:i A'),
            ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
        );
    }

    public static function bookingDeclined(Booking $booking)
    {
        self::sendNotification(
            $booking->student->user_id,
            'booking_declined',
            'Booking Declined',
            "Unfortunately, {$booking->teacher->user->name} has declined your booking on " . $booking->start_time->format('M d, Y g:i A'),
            ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
        );
    }

    public static function bookingCancelled(Booking $booking)
    {
        if ($booking->student) {
            self::sendNotification(
                $booking->student->user_id,
                'booking_cancelled',
                'Booking Cancelled',
                "Your booking with {$booking->teacher->user->name} on " . $booking->start_time->format('M d, Y g:i A') . " has been cancelled.",
                ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
            );
        }

        if ($booking->teacher) {
            self::sendNotification(
                $booking->teacher->user_id,
                'booking_cancelled',
                'Booking Cancelled',
                "Your booking with {$booking->student->user->name} on " . $booking->start_time->format('M d, Y g:i A') . " has been cancelled.",
                ['booking_id' => $booking->id, 'student_id' => $booking->student_id]
            );
        }
    }

    public static function bookingReminder(Booking $booking)
    {
        // Send reminder 1 hour before booking
        if ($booking->student) {
            self::sendNotification(
                $booking->student->user_id,
                'booking_reminder',
                'Booking Reminder',
                "Reminder: You have a booking with {$booking->teacher->user->name} in 1 hour.",
                ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
            );
        }

        if ($booking->teacher) {
            self::sendNotification(
                $booking->teacher->user_id,
                'booking_reminder',
                'Booking Reminder',
                "Reminder: You have a booking with {$booking->student->user->name} in 1 hour.",
                ['booking_id' => $booking->id, 'student_id' => $booking->student_id]
            );
        }
    }

    public static function bookingCompleted(Booking $booking)
    {
        if ($booking->student) {
            self::sendNotification(
                $booking->student->user_id,
                'booking_completed',
                'Booking Completed',
                "Your booking with {$booking->teacher->user->name} has been marked as completed. Don't forget to leave feedback!",
                ['booking_id' => $booking->id, 'teacher_id' => $booking->teacher_id]
            );
        }

        if ($booking->teacher) {
            self::sendNotification(
                $booking->teacher->user_id,
                'booking_completed',
                'Booking Completed',
                "Your booking with {$booking->student->user->name} has been marked as completed. Don't forget to leave feedback!",
                ['booking_id' => $booking->id, 'student_id' => $booking->student_id]
            );
        }
    }

    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
}
