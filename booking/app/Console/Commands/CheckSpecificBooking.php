<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class CheckSpecificBooking extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:check {booking_id}';

    /**
     * The console command description.
     */
    protected $description = 'Check details of a specific booking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        
        $this->info("🔍 Checking Booking ID: {$bookingId}");
        $this->line('================================');
        
        $booking = Booking::with(['teacher.user', 'student.user', 'sessionRecordings'])->find($bookingId);
        
        if (!$booking) {
            $this->error("❌ Booking ID {$bookingId} not found");
            return 1;
        }
        
        $this->line("🆔 Booking ID: {$booking->id}");
        $this->line("📅 Start Time: {$booking->start_time}");
        $this->line("📅 End Time: {$booking->end_time}");
        $this->line("⏱️  Duration: {$booking->duration_minutes} minutes");
        $this->line("💰 Price: $" . number_format($booking->price, 2));
        $this->line("📊 Status: {$booking->status}");
        
        if ($booking->zoom_meeting_id) {
            $this->line("🎥 Zoom Meeting ID: {$booking->zoom_meeting_id}");
        } else {
            $this->line("🎥 Zoom Meeting ID: Not set");
        }
        
        if ($booking->teacher) {
            $this->line("👨‍🏫 Teacher: {$booking->teacher->user->name} (ID: {$booking->teacher_id})");
        } else {
            $this->line("👨‍🏫 Teacher: Not found (ID: {$booking->teacher_id})");
        }
        
        if ($booking->student) {
            $this->line("👨‍🎓 Student: {$booking->student->user->name} (ID: {$booking->student_id})");
        } else {
            $this->line("👨‍🎓 Student: Not found (ID: {$booking->student_id})");
        }
        
        // Show recordings
        $recordingsCount = $booking->sessionRecordings->count();
        if ($recordingsCount > 0) {
            $this->line("🎬 Recordings: {$recordingsCount} file(s)");
            foreach ($booking->sessionRecordings as $recording) {
                $this->line("   - Type: {$recording->recording_type}, Size: {$recording->formatted_file_size}");
            }
        } else {
            $this->line("🎬 Recordings: None");
        }
        
        $this->line("📝 Notes: " . ($booking->notes ?: 'None'));
        $this->line("🕒 Created: {$booking->created_at->format('Y-m-d H:i:s')}");
        $this->line("🕒 Updated: {$booking->updated_at->format('Y-m-d H:i:s')}");
        
        // Check if this booking has a Zoom meeting
        if ($booking->zoom_meeting_id) {
            $this->line("\n💡 This booking has a Zoom meeting!");
            $this->line("   To fetch recordings: php artisan zoom:add-meeting {$booking->zoom_meeting_id}");
            $this->line("   To view recordings: php artisan zoom:view-recordings {$booking->zoom_meeting_id}");
        } else {
            $this->line("\n💡 This booking doesn't have a Zoom meeting");
            $this->line("   To add a Zoom meeting: Update the booking in admin panel");
        }
        
        return 0;
    }
}
