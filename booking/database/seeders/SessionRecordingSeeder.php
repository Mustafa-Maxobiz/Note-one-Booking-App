<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionRecording;
use App\Models\Booking;

class SessionRecordingSeeder extends Seeder
{
    public function run(): void
    {
        // Get some completed bookings to add recordings to
        $completedBookings = Booking::where('status', 'completed')->take(5)->get();
        
        if ($completedBookings->isEmpty()) {
            // If no completed bookings, get any bookings
            $completedBookings = Booking::take(5)->get();
        }

        foreach ($completedBookings as $booking) {
            // Create multiple recording types for each booking
            $recordingTypes = [
                [
                    'recording_type' => 'video',
                    'file_name' => 'Session_Recording_Video_' . $booking->id . '.mp4',
                    'duration' => rand(1800, 3600), // 30-60 minutes
                    'file_size' => rand(50000000, 200000000), // 50-200 MB
                ],
                [
                    'recording_type' => 'audio',
                    'file_name' => 'Session_Recording_Audio_' . $booking->id . '.m4a',
                    'duration' => rand(1800, 3600), // 30-60 minutes
                    'file_size' => rand(10000000, 50000000), // 10-50 MB
                ],
                [
                    'recording_type' => 'chat',
                    'file_name' => 'Session_Chat_' . $booking->id . '.txt',
                    'duration' => rand(1800, 3600), // 30-60 minutes
                    'file_size' => rand(1000, 10000), // 1-10 KB
                ]
            ];

            foreach ($recordingTypes as $type) {
                $recordingStart = $booking->start_time;
                $recordingEnd = $recordingStart->copy()->addSeconds($type['duration']);

                SessionRecording::create([
                    'booking_id' => $booking->id,
                    'zoom_meeting_id' => $booking->zoom_meeting_id ?? 'ZOOM_' . rand(100000000, 999999999),
                    'recording_id' => 'REC_' . rand(100000000, 999999999),
                    'recording_type' => $type['recording_type'],
                    'file_name' => $type['file_name'],
                    'download_url' => 'https://zoom.us/recording/download/' . rand(100000000, 999999999),
                    'play_url' => 'https://zoom.us/recording/play/' . rand(100000000, 999999999),
                    'file_size' => $type['file_size'],
                    'duration' => $type['duration'],
                    'recording_start' => $recordingStart,
                    'recording_end' => $recordingEnd,
                    'is_processed' => true,
                ]);
            }
        }

        $this->command->info('Session recordings seeded successfully!');
    }
}
