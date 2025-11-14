<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\EmailService;
use Carbon\Carbon;

class TestReminderEmails extends Command
{
    protected $signature = 'reminders:test {--booking-id=} {--type=24h} {--recipient=both}';
    protected $description = 'Test reminder email functionality';

    public function handle()
    {
        $bookingId = $this->option('booking-id');
        $type = $this->option('type');
        $recipient = $this->option('recipient');

        if (!$bookingId) {
            $this->error('Please provide a booking ID with --booking-id option');
            return 1;
        }

        $booking = Booking::with(['student.user', 'teacher.user'])->find($bookingId);

        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found");
            return 1;
        }

        $this->info("Testing {$type} reminder for booking #{$booking->id}");
        $this->info("Student: " . ($booking->student && $booking->student->user ? $booking->student->user->name : 'No user'));
        $this->info("Teacher: " . ($booking->teacher && $booking->teacher->user ? $booking->teacher->user->name : 'No user'));
        $this->info("Session time: " . $booking->start_time->format('Y-m-d H:i:s'));
        $this->info("Recipient: " . $recipient);

        try {
            $results = [];
            
            if ($recipient === 'both' || $recipient === 'student') {
                if ($type === '24h') {
                    $result = EmailService::sendSessionReminder($booking);
                    $results['student'] = $result;
                    $this->info('Student 24-hour reminder: ' . ($result ? 'SUCCESS' : 'FAILED'));
                } elseif ($type === '1h') {
                    $result = EmailService::sendUrgentReminder($booking);
                    $results['student'] = $result;
                    $this->info('Student 1-hour reminder: ' . ($result ? 'SUCCESS' : 'FAILED'));
                }
            }
            
            if ($recipient === 'both' || $recipient === 'teacher') {
                if ($type === '24h') {
                    $result = EmailService::sendTeacherReminder($booking);
                    $results['teacher'] = $result;
                    $this->info('Teacher 24-hour reminder: ' . ($result ? 'SUCCESS' : 'FAILED'));
                } elseif ($type === '1h') {
                    $result = EmailService::sendTeacherUrgentReminder($booking);
                    $results['teacher'] = $result;
                    $this->info('Teacher 1-hour reminder: ' . ($result ? 'SUCCESS' : 'FAILED'));
                }
            }

            if ($type !== '24h' && $type !== '1h') {
                $this->error('Invalid type. Use 24h or 1h');
                return 1;
            }

            $successCount = count(array_filter($results));
            $totalCount = count($results);
            
            if ($successCount === $totalCount && $totalCount > 0) {
                $this->info('✅ All reminder emails sent successfully!');
            } elseif ($successCount > 0) {
                $this->warn("⚠️ Some reminder emails sent successfully ({$successCount}/{$totalCount})");
            } else {
                $this->error('❌ Failed to send reminder emails. Check logs for details.');
            }

        } catch (\Exception $e) {
            $this->error('Error sending reminder: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
