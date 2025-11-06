<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class ShowAllBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:show {--limit=20} {--status=} {--teacher=} {--student=}';

    /**
     * The console command description.
     */
    protected $description = 'Show all bookings with detailed information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $status = $this->option('status');
        $teacherId = $this->option('teacher');
        $studentId = $this->option('student');
        
        $this->info('📋 All Bookings in the System');
        $this->line('================================');
        
        $query = Booking::with(['teacher.user', 'student.user', 'sessionRecordings']);
        
        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        
        $bookings = $query->orderBy('start_time', 'desc')->limit($limit)->get();
        
        if ($bookings->count() == 0) {
            $this->line('❌ No bookings found with the specified criteria');
            return 0;
        }
        
        $this->line("\n📊 Found {$bookings->count()} booking(s):");
        $this->line('');
        
        foreach ($bookings as $booking) {
            $this->line("🆔 Booking ID: {$booking->id}");
            $this->line("   📅 Start: {$booking->start_time}");
            $this->line("   📅 End: {$booking->end_time}");
            $this->line("   ⏱️  Duration: {$booking->duration_minutes} minutes");
            $this->line("   💰 Price: $" . number_format($booking->price, 2));
            $this->line("   📊 Status: {$booking->status}");
            
            if ($booking->zoom_meeting_id) {
                $this->line("   🎥 Zoom Meeting: {$booking->zoom_meeting_id}");
            } else {
                $this->line("   🎥 Zoom Meeting: Not set");
            }
            
            if ($booking->teacher) {
                $this->line("   👨‍🏫 Teacher: {$booking->teacher->user->name} (ID: {$booking->teacher_id})");
            } else {
                $this->line("   👨‍🏫 Teacher: Not found (ID: {$booking->teacher_id})");
            }
            
            if ($booking->student) {
                $this->line("   👨‍🎓 Student: {$booking->student->user->name} (ID: {$booking->student_id})");
            } else {
                $this->line("   👨‍🎓 Student: Not found (ID: {$booking->student_id})");
            }
            
            // Show recordings count
            $recordingsCount = $booking->sessionRecordings->count();
            if ($recordingsCount > 0) {
                $this->line("   🎬 Recordings: {$recordingsCount} file(s)");
            } else {
                $this->line("   🎬 Recordings: None");
            }
            
            $this->line("   📝 Notes: " . ($booking->notes ?: 'None'));
            $this->line("   🕒 Created: {$booking->created_at->format('Y-m-d H:i:s')}");
            $this->line("   🕒 Updated: {$booking->updated_at->format('Y-m-d H:i:s')}");
            $this->line('   ' . str_repeat('-', 50));
        }
        
        // Summary statistics
        $this->line("\n📈 Summary Statistics:");
        $this->line("   Total Bookings: " . Booking::count());
        $this->line("   Completed: " . Booking::where('status', 'completed')->count());
        $this->line("   Confirmed: " . Booking::where('status', 'confirmed')->count());
        $this->line("   Cancelled: " . Booking::where('status', 'cancelled')->count());
        $this->line("   With Zoom: " . Booking::whereNotNull('zoom_meeting_id')->count());
        $this->line("   With Recordings: " . Booking::whereHas('sessionRecordings')->count());
        
        $this->line("\n💡 Commands:");
        $this->line("   php artisan bookings:show --status=completed");
        $this->line("   php artisan bookings:show --teacher=1");
        $this->line("   php artisan bookings:show --student=1");
        $this->line("   php artisan zoom:view-recordings {meeting_id}");
        
        return 0;
    }
}
