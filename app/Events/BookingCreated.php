<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $teacherId;
    public $studentId;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->teacherId = $booking->teacher_id;
        $this->studentId = $booking->student_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('teacher.' . $this->teacherId),
            new PrivateChannel('student.' . $this->studentId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'type' => 'booking_created',
            'title' => 'New Booking Request',
            'message' => 'You have received a new booking request',
            'teacher_name' => $this->booking->teacher->user->name,
            'student_name' => $this->booking->student->user->name,
            'date' => $this->booking->start_time->format('M d, Y'),
            'time' => $this->booking->start_time->format('g:i A'),
            'duration' => $this->booking->duration_minutes . ' minutes',
            'price' => '$' . number_format($this->booking->price, 2),
            'status' => $this->booking->status,
            'created_at' => $this->booking->created_at->diffForHumans(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }
}
