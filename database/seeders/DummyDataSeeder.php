<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Feedback;
use App\Models\TeacherAvailability;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 additional users (teachers and students)
        $teacherUsers = [];
        $studentUsers = [];

        for ($i = 1; $i <= 10; $i++) {
            // Create teacher users
            $teacherUser = User::create([
                'name' => "Teacher {$i}",
                'email' => "teacher{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => "+123456789{$i}",
                'is_active' => true,
            ]);
            $teacherUsers[] = $teacherUser;

            // Create student users
            $studentUser = User::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => "+987654321{$i}",
                'is_active' => true,
            ]);
            $studentUsers[] = $studentUser;
        }

        // Create 10 teacher profiles
        $qualifications = ['MSc Mathematics', 'PhD Physics', 'MA English', 'BSc Chemistry', 'MEd Education'];
        $teachingStyles = [['interactive', 'problem-solving'], ['conversational', 'grammar-focused'], ['hands-on', 'experimental']];
        
        foreach ($teacherUsers as $index => $teacherUser) {
            Teacher::create([
                'user_id' => $teacherUser->id,
                'bio' => "Experienced teacher with expertise in multiple subjects. Passionate about helping students achieve their goals.",
                'qualifications' => $qualifications[$index % count($qualifications)],
                'hourly_rate' => rand(30, 80),
                'timezone' => 'America/New_York',
                'teaching_style' => json_encode($teachingStyles[$index % count($teachingStyles)]),
                'teaching_style' => json_encode($teachingStyles[$index % count($teachingStyles)]),
                'is_verified' => true,
                'is_available' => true,
            ]);
        }

        // Create 10 student profiles
        $levels = ['beginner', 'intermediate', 'advanced'];
        $learningGoals = [
            'Improve mathematics skills and prepare for college entrance exams.',
            'Learn Spanish for travel and cultural understanding.',
            'Master computer programming fundamentals.',
            'Enhance English writing and speaking abilities.',
            'Understand advanced physics concepts.'
        ];

        foreach ($studentUsers as $index => $studentUser) {
            Student::create([
                'user_id' => $studentUser->id,
                'date_of_birth' => Carbon::now()->subYears(rand(15, 25)),
                'level' => $levels[$index % count($levels)],
                'learning_goals' => $learningGoals[$index % count($learningGoals)],
                'learning_goals' => $learningGoals[$index % count($learningGoals)],
                'timezone' => 'America/New_York',
                'parent_name' => "Parent of {$studentUser->name}",
                'parent_email' => "parent{$index}@example.com",
                'parent_phone' => "+111222333{$index}",
            ]);
        }

        // Create 10 teacher availabilities
        $teachers = Teacher::all();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($teachers as $teacher) {
            for ($i = 0; $i < 3; $i++) { // 3 availabilities per teacher
                TeacherAvailability::create([
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $days[array_rand($days)],
                    'start_time' => Carbon::createFromTime(rand(9, 17), 0, 0),
                    'end_time' => Carbon::createFromTime(rand(18, 21), 0, 0),
                    'is_available' => true,
                ]);
            }
        }

        // Create 10 sessions
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        
        for ($i = 1; $i <= 10; $i++) {
            $teacher = Teacher::inRandomOrder()->first();
            $student = Student::inRandomOrder()->first();
            $startTime = Carbon::now()->addDays(rand(1, 30))->setTime(rand(9, 17), 0, 0);
            $duration = rand(30, 120);
            $price = ($teacher->hourly_rate / 60) * $duration;
            
            Booking::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes($duration),
                'duration_minutes' => $duration,
                'status' => $statuses[array_rand($statuses)],
                'zoom_meeting_id' => "zoom_{$i}_" . rand(100000, 999999),
                'zoom_join_url' => "https://zoom.us/j/" . rand(100000000, 999999999),
                'notes' => "Session notes for booking {$i}",
                'price' => $price,
            ]);
        }

        // Create 10 payments
        $sessions = Booking::all();
        $paymentMethods = ['stripe', 'paypal', 'credit_card'];
        $paymentStatuses = ['pending', 'completed', 'failed'];
        
        foreach ($sessions as $booking) {
            Payment::create([
                'booking_id' => $booking->id,
                'student_id' => $booking->student_id,
                'teacher_id' => $booking->teacher_id,
                'amount' => $booking->price,
                'status' => $paymentStatuses[array_rand($paymentStatuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'transaction_id' => 'txn_' . rand(100000, 999999),
                'payment_details' => json_encode(['method' => 'online', 'processed_at' => now()]),
                'paid_at' => $booking->status === 'completed' ? now() : null,
            ]);
        }

        // Create 10 feedback records
        $feedbackTypes = ['teacher_to_student', 'student_to_teacher'];
        
        foreach ($sessions as $booking) {
            if ($booking->status === 'completed') {
                // Student feedback for teacher
                Feedback::create([
                    'booking_id' => $booking->id,
                    'teacher_id' => $booking->teacher_id,
                    'student_id' => $booking->student_id,
                    'rating' => rand(3, 5),
                    'comment' => "Great session! The teacher was very helpful and explained concepts clearly.",
                    'type' => 'student_to_teacher',
                    'is_public' => true,
                ]);

                // Teacher feedback for student
                Feedback::create([
                    'booking_id' => $booking->id,
                    'teacher_id' => $booking->teacher_id,
                    'student_id' => $booking->student_id,
                    'rating' => rand(3, 5),
                    'comment' => "Student was engaged and showed good understanding of the material.",
                    'type' => 'teacher_to_student',
                    'is_public' => false,
                ]);
            }
        }

        // Create 10 system settings
        $settings = [
            'app_name' => 'Online Lesson Booking System',
            'app_description' => 'A comprehensive online lesson booking system for teachers and students.',
            'contact_email' => 'admin@example.com',
            'contact_phone' => '+1234567890',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'lesson_duration_default' => 60,
            'cancellation_policy_hours' => 24,
            'max_lessons_per_day' => 8,
            'min_lesson_duration' => 30,
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::setValue($key, $value);
        }

        // Create sample notifications
        $users = User::all();
        $sessions = Booking::all();
        
        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $booking = $sessions->random();
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => ['booking_requested', 'booking_accepted', 'booking_reminder'][array_rand(['booking_requested', 'booking_accepted', 'booking_reminder'])],
                    'title' => 'Sample Notification ' . $i,
                    'message' => 'This is a sample notification message for testing purposes.',
                    'data' => ['booking_id' => $booking->id],
                    'is_read' => rand(0, 1),
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        $this->command->info('Dummy data created successfully!');
        $this->command->info('- 10 additional teacher users with profiles');
        $this->command->info('- 10 additional student users with profiles');
        $this->command->info('- Multiple teacher availabilities');
        $this->command->info('- 10 sessions with various statuses');
        $this->command->info('- 10 payments with different statuses');
        $this->command->info('- Multiple feedback records');
        $this->command->info('- 10 system settings');
        $this->command->info('- Sample notifications for all users');
    }
}
