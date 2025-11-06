<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\TeacherAvailability;
use Illuminate\Database\Seeder;

class TeacherAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all available and verified teachers
        $teachers = Teacher::where('is_available', true)
            ->where('is_verified', true)
            ->get();

        foreach ($teachers as $teacher) {
            // Skip if teacher already has availabilities
            if ($teacher->availabilities()->count() > 0) {
                continue;
            }

            // Create availability for each day of the week
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($days as $day) {
                // Skip weekends for some teachers (randomly)
                if (in_array($day, ['saturday', 'sunday']) && rand(1, 3) === 1) {
                    continue;
                }

                // Create availability with different time ranges
                $startHour = rand(8, 10); // Start between 8-10 AM
                $endHour = rand(18, 22);  // End between 6-10 PM
                
                TeacherAvailability::create([
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $day,
                    'start_time' => sprintf('%02d:00:00', $startHour),
                    'end_time' => sprintf('%02d:00:00', $endHour),
                    'is_available' => true,
                ]);
            }
        }
    }
}
