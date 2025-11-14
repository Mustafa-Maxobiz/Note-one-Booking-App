<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    protected $table = 'bookings';
    protected $fillable = [
        'teacher_id',
        'student_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_password',
        'notes',
        'price',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Get the teacher for this session.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the student for this session.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the payments for this session.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    /**
     * Get the feedback for this session.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'booking_id');
    }

    /**
     * Get the session recordings for this session.
     */
    public function sessionRecordings(): HasMany
    {
        return $this->hasMany(SessionRecording::class, 'booking_id');
    }
}
