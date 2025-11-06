<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PerformanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting performance test data creation...');

        // Create test users
        $users = [];
        for ($i = 1; $i <= 100; $i++) {
            $users[] = User::create([
                'name' => "Test User {$i}",
                'email' => "testuser{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => $i <= 20 ? 'teacher' : 'student',
                'phone' => "+123456789{$i}",
                'is_active' => true,
            ]);
        }

        // Create teacher profiles
        $teachers = [];
        foreach (array_slice($users, 0, 20) as $user) {
            $teachers[] = Teacher::create([
                'user_id' => $user->id,
                'bio' => "Test teacher bio for {$user->name}",
                'qualifications' => 'Test Qualifications',
                'hourly_rate' => rand(30, 80),
                'timezone' => 'UTC',
                'is_verified' => true,
                'is_available' => true,
            ]);
        }

        // Create student profiles
        $students = [];
        foreach (array_slice($users, 20) as $user) {
            $students[] = Student::create([
                'user_id' => $user->id,
                'date_of_birth' => Carbon::now()->subYears(rand(15, 25)),
                'level' => ['beginner', 'intermediate', 'advanced'][rand(0, 2)],
                'timezone' => 'UTC',
            ]);
        }

        // Create bookings
        $bookings = [];
        for ($i = 1; $i <= 500; $i++) {
            $teacher = $teachers[array_rand($teachers)];
            $student = $students[array_rand($students)];
            $startTime = Carbon::now()->addDays(rand(1, 30))->setTime(rand(9, 17), 0, 0);
            $duration = rand(30, 120);
            
            $bookings[] = Booking::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes($duration),
                'duration_minutes' => $duration,
                'status' => ['pending', 'confirmed', 'completed', 'cancelled'][rand(0, 3)],
                'price' => ($teacher->hourly_rate / 60) * $duration,
            ]);
        }

        // Create payments
        foreach ($bookings as $booking) {
            Payment::create([
                'booking_id' => $booking->id,
                'student_id' => $booking->student_id,
                'teacher_id' => $booking->teacher_id,
                'amount' => $booking->price,
                'status' => ['pending', 'completed', 'failed'][rand(0, 2)],
                'payment_method' => ['stripe', 'paypal', 'credit_card'][rand(0, 2)],
                'transaction_id' => 'txn_' . rand(100000, 999999),
            ]);
        }

        // Create feedback
        foreach (array_slice($bookings, 0, 200) as $booking) {
            if ($booking->status === 'completed') {
                Feedback::create([
                    'booking_id' => $booking->id,
                    'teacher_id' => $booking->teacher_id,
                    'student_id' => $booking->student_id,
                    'rating' => rand(3, 5),
                    'comment' => "Test feedback for booking {$booking->id}",
                    'type' => 'student_to_teacher',
                    'is_public' => true,
                ]);
            }
        }

        // Create notifications
        foreach ($users as $user) {
            for ($i = 1; $i <= 10; $i++) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'test_notification',
                    'title' => "Test Notification {$i}",
                    'message' => "Test notification message for {$user->name}",
                    'is_read' => rand(0, 1),
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Performance test data created successfully!');
        $this->command->info('- 100 test users (20 teachers, 80 students)');
        $this->command->info('- 500 test bookings');
        $this->command->info('- 500 test payments');
        $this->command->info('- 200 test feedback records');
        $this->command->info('- 1000 test notifications');
    }
}
