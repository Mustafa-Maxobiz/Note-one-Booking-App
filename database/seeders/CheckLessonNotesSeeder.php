<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LessonNote;

class CheckLessonNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total = LessonNote::count();
        $this->command->info("Total lesson notes: {$total}");
        
        $visibility = LessonNote::selectRaw('visibility, count(*) as count')
            ->groupBy('visibility')
            ->get();
            
        $this->command->info('Lesson notes by visibility:');
        foreach ($visibility as $item) {
            $this->command->info("  {$item->visibility}: {$item->count}");
        }
        
        // Show some sample lesson notes
        $this->command->info('Sample lesson notes:');
        $notes = LessonNote::with(['student.user', 'teacher.user'])
            ->orderBy('lesson_date', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($notes as $note) {
            $this->command->info("  - {$note->title} ({$note->student->user->name} with {$note->teacher->user->name})");
        }
    }
}
