<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Booking;
use App\Models\Notification;

class CreateNotifications extends Command
{
    protected $signature = 'notifications:create';
    protected $description = 'Create sample notifications for testing';

    public function handle()
    {
        $users = User::all();
        $bookings = Booking::all();
        
        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $booking = $bookings->random();
                Notification::create([
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
        
        $this->info('Sample notifications created successfully!');
    }
}
