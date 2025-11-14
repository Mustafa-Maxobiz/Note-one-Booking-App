<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonNote;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;

class AdditionalLessonNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::with('user')->first();
        $teacher = Teacher::with('user')->first();

        if (!$student || !$teacher) {
            $this->command->info('No students or teachers found.');
            return;
        }

        // Create additional lesson notes
        $additionalNotes = [
            [
                'title' => 'First Lesson - Guitar Basics',
                'content' => 'Student\'s very first guitar lesson! Introduced proper posture, how to hold the guitar, and basic finger exercises. Student was excited and eager to learn. We covered the parts of the guitar and how to tune it. Homework: Practice holding the guitar and basic finger exercises.',
                'lesson_date' => Carbon::now()->subDays(21)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Open Chords - C, G, D',
                'content' => 'Introduced the first three open chords. Student struggled with finger placement initially but improved throughout the lesson. We practiced switching between C and G chords. The student\'s finger strength is developing well. Homework: Practice C and G chord changes.',
                'lesson_date' => Carbon::now()->subDays(17)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Rhythm and Timing',
                'content' => 'Worked on basic rhythm patterns and timing today. Student learned to count beats and play along with a metronome. We practiced simple strumming patterns and the student showed good progress. Timing is crucial for guitar playing. Homework: Practice with metronome at 60 BPM.',
                'lesson_date' => Carbon::now()->subDays(10)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ],
            [
                'title' => 'Song: "Wonderwall" by Oasis',
                'content' => 'Started learning "Wonderwall" today! Student was excited about this song. We covered the chord progression (Em, G, D, C) and the strumming pattern. The student picked up the chord changes well. We also worked on the rhythm and timing. This is a great song for practicing chord changes. Homework: Practice the chord progression.',
                'lesson_date' => Carbon::now()->subDays(4)->setTime(15, 0),
                'visibility' => 'student_and_teacher'
            ]
        ];

        foreach ($additionalNotes as $noteData) {
            LessonNote::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'lesson_date' => $noteData['lesson_date'],
                'title' => $noteData['title'],
                'content' => $noteData['content'],
                'visibility' => $noteData['visibility']
            ]);
        }

        $this->command->info('Additional lesson notes created successfully!');
    }
}
