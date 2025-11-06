<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonNote;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Booking;
use Carbon\Carbon;

class LessonNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing students and teachers
        $students = Student::with('user')->get();
        $teachers = Teacher::with('user')->get();
        
        if ($students->isEmpty() || $teachers->isEmpty()) {
            $this->command->info('No students or teachers found. Please run UserSeeder first.');
            return;
        }

        $student = $students->first();
        $teacher = $teachers->first();

        // Create sample lesson notes for the first student
        $lessonNotes = [
            [
                'title' => 'G Major Scale',
                'content' => 'Introduced the G major scale today. Student showed good understanding of the basic finger positions. We practiced ascending and descending the scale slowly, focusing on proper finger placement and smooth transitions between notes. Homework: Practice the scale for 15 minutes daily.',
                'lesson_date' => Carbon::now()->subDays(14)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => '"Levitating" by Dua Lipa',
                'content' => 'Started working on "Levitating" by Dua Lipa. Student was excited about learning this song! We covered the chord progression (Am, F, C, G) and the strumming pattern. The student picked up the chord changes quickly. We also worked on the rhythm and timing. Homework: Practice the chord changes and strumming pattern.',
                'lesson_date' => Carbon::now()->subDays(7)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Minor Pentatonic Pattern 1',
                'content' => 'Introduced the minor pentatonic scale in pattern 1 position. Student struggled initially with the finger stretching but improved throughout the lesson. We practiced the scale in different keys and worked on some basic blues licks. Focus on keeping fingers close to the fretboard. Homework: Practice the pentatonic pattern and try to create simple melodies.',
                'lesson_date' => Carbon::now()->subDays(3)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Barre Chords - F Major',
                'content' => 'First attempt at barre chords today. Student found the F major barre chord challenging but made good progress. We worked on proper thumb positioning and finger strength exercises. The student was determined and didn\'t give up easily. This is a milestone lesson! Homework: Practice the F major barre chord for 10 minutes daily.',
                'lesson_date' => Carbon::now()->subDays(1)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Fingerpicking Basics',
                'content' => 'Introduced basic fingerpicking patterns using the thumb and first three fingers. Student showed good coordination and picked up the Travis picking pattern quickly. We worked on "Dust in the Wind" as an example. The student\'s finger independence is improving well. Homework: Practice the fingerpicking pattern with a metronome.',
                'lesson_date' => Carbon::now()->subHours(6)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ]
        ];

        // Create lesson notes for the first student
        foreach ($lessonNotes as $index => $noteData) {
            LessonNote::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'lesson_date' => $noteData['lesson_date'],
                'title' => $noteData['title'],
                'content' => $noteData['content'],
                'visibility' => $noteData['visibility'],
                'attachments' => null
            ]);
        }

        // Create a lesson note for another student if available
        if ($students->count() > 1) {
            $secondStudent = $students->skip(1)->first();
            
            LessonNote::create([
                'student_id' => $secondStudent->id,
                'teacher_id' => $teacher->id,
                'lesson_date' => Carbon::now()->subDays(5)->setTime(16, 0),
                'title' => 'Basic Chords - C, G, Am, F',
                'content' => 'Introduced the four basic open chords today. Student had some difficulty with the F chord but made progress. We practiced chord transitions and simple strumming patterns. The student is showing good rhythm sense. Homework: Practice chord changes between C, G, Am, and F.',
                'visibility' => 'student_and_teacher',
                'attachments' => null
            ]);

            LessonNote::create([
                'student_id' => $secondStudent->id,
                'teacher_id' => $teacher->id,
                'lesson_date' => Carbon::now()->subDays(2)->setTime(16, 0),
                'title' => 'Strumming Patterns',
                'content' => 'Worked on different strumming patterns today. Student learned the basic down-up strum and the "island strum" pattern. We applied these to simple songs. The student\'s timing is improving well. Homework: Practice the strumming patterns with a metronome.',
                'visibility' => 'student_and_teacher',
                'attachments' => null
            ]);
        }

        // Create a teacher-only note
        LessonNote::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'lesson_date' => Carbon::now()->subDays(10)->setTime(15, 0),
            'title' => 'Student Assessment Notes',
            'content' => 'Student shows strong motivation and learns quickly. Has good ear for music but needs to work on finger strength for barre chords. Consider introducing more finger exercises. Parent mentioned student practices 30 minutes daily which is excellent.',
            'visibility' => 'teacher_only',
            'attachments' => null
        ]);

        $this->command->info('Lesson notes test data created successfully!');
        $this->command->info('Created lesson notes for students: ' . $student->user->name);
        if ($students->count() > 1) {
            $this->command->info('And for: ' . $students->skip(1)->first()->user->name);
        }
    }
}
