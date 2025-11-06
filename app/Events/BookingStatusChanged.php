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

class BookingStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;
    public $newStatus;
    public $teacherId;
    public $studentId;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusMessages = [
            'confirmed' => [
                'title' => 'Booking Accepted!',
                'message' => 'Your booking has been accepted by the teacher',
                'icon' => '✅',
                'color' => 'success'
            ],
            'cancelled' => [
                'title' => 'Booking Declined',
                'message' => 'Your booking has been declined by the teacher',
                'icon' => '❌',
                'color' => 'danger'
            ],
            'completed' => [
                'title' => 'Session Completed',
                'message' => 'Your session has been marked as completed',
                'icon' => '🎓',
                'color' => 'info'
            ]
        ];

        $statusInfo = $statusMessages[$this->newStatus] ?? [
            'title' => 'Booking Status Updated',
            'message' => 'Your booking status has been updated',
            'icon' => '📝',
            'color' => 'warning'
        ];

        return [
            'id' => $this->booking->id,
            'type' => 'booking_status_changed',
            'title' => $statusInfo['title'],
            'message' => $statusInfo['message'],
            'icon' => $statusInfo['icon'],
            'color' => $statusInfo['color'],
            'teacher_name' => $this->booking->teacher->user->name,
            'student_name' => $this->booking->student->user->name,
            'date' => $this->booking->start_time->format('M d, Y'),
            'time' => $this->booking->start_time->format('g:i A'),
            'duration' => $this->booking->duration_minutes . ' minutes',
            'price' => '$' . number_format($this->booking->price, 2),
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'zoom_join_url' => $this->booking->zoom_join_url,
            'zoom_meeting_id' => $this->booking->zoom_meeting_id,
            'updated_at' => $this->booking->updated_at->diffForHumans(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.status_changed';
    }
}
