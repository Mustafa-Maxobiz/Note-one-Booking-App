<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonNote;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;

class SecondTeacherNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::with('user')->first();
        $teachers = Teacher::with('user')->get();

        if ($teachers->count() > 1) {
            $secondTeacher = $teachers->skip(1)->first();
            
            LessonNote::create([
                'student_id' => $student->id,
                'teacher_id' => $secondTeacher->id,
                'lesson_date' => Carbon::now()->subDays(8)->setTime(14, 0),
                'title' => 'Blues Basics - 12-Bar Blues',
                'content' => 'Introduced the 12-bar blues progression today. Student learned the basic chord progression (I-IV-V) and we practiced it in different keys. The student showed good understanding of the blues feel. We also worked on some basic blues licks using the pentatonic scale. Homework: Practice the 12-bar blues progression.',
                'visibility' => 'student_and_teacher'
            ]);
            
            $this->command->info('Lesson note from second teacher created!');
        } else {
            $this->command->info('Only one teacher found, skipping second teacher note.');
        }
    }
}
