<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
        ]);

        // Create Teacher Users
        $teacher1 = User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'phone' => '+1234567891',
        ]);

        $teacher2 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah.johnson@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'phone' => '+1234567892',
        ]);

        // Create Teacher Profiles
        Teacher::create([
            'user_id' => $teacher1->id,
            'bio' => 'Experienced mathematics teacher with 10+ years of teaching experience.',
            'qualifications' => 'MSc Mathematics, Teaching Certificate',
            'hourly_rate' => 50.00,
            'timezone' => 'America/New_York',
            'teaching_style' => json_encode(['interactive', 'visual']),
            'teaching_style' => json_encode(['interactive', 'problem-solving']),
            'is_verified' => true,
        ]);

        Teacher::create([
            'user_id' => $teacher2->id,
            'bio' => 'Passionate language teacher specializing in English and Spanish.',
            'qualifications' => 'MA Linguistics, TESOL Certificate',
            'hourly_rate' => 45.00,
            'timezone' => 'America/New_York',
            'teaching_style' => json_encode(['conversational', 'grammar-focused']),
            'teaching_style' => json_encode(['conversational', 'grammar-focused']),
            'is_verified' => true,
        ]);

        // Create Student Users
        $student1 = User::create([
            'name' => 'Mike Wilson',
            'email' => 'mike.wilson@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1234567893',
        ]);

        $student2 = User::create([
            'name' => 'Emily Davis',
            'email' => 'emily.davis@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1234567894',
        ]);

        // Create Student Profiles
        Student::create([
            'user_id' => $student1->id,
            'date_of_birth' => '2005-03-15',
            'level' => 'intermediate',
            'learning_goals' => 'Improve mathematics skills and prepare for college entrance exams.',
            'learning_goals' => 'Improve problem-solving skills and learn programming',
            'timezone' => 'America/New_York',
        ]);

        Student::create([
            'user_id' => $student2->id,
            'date_of_birth' => '2006-07-22',
            'level' => 'beginner',
            'learning_goals' => 'Learn Spanish for travel and cultural understanding.',
            'learning_goals' => 'Learn Spanish conversation and improve English writing',
            'timezone' => 'America/New_York',
        ]);
    }
}
