<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Encryptable;

class Teacher extends Model
{
    use SoftDeletes, Auditable, Encryptable;
    protected $fillable = [
        'user_id',
        'bio',
        'qualifications',
        'hourly_rate',
        'timezone',
        'teaching_style',
        'zoom_api_key',
        'zoom_api_secret',
        'is_verified',
        'is_available',
        'experience_years',
    ];

    protected $casts = [
        'teaching_style' => 'array',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    /**
     * Encrypted attributes.
     */
    protected $encrypted = [
        'zoom_api_key',
        'zoom_api_secret',
    ];

    /**
     * Get the user that owns the teacher profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the availabilities for the teacher.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class);
    }

    /**
     * Get the sessions for the teacher.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the bookings for the teacher (alias for sessions).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the payments for the teacher.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the feedback for the teacher.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Check if teacher is available on a specific day and time
     */
    public function isAvailableOn($dayOfWeek, $startTime, $endTime = null)
    {
        $query = $this->availabilities()
            ->where('day_of_week', strtolower($dayOfWeek))
            ->where('is_available', true);

        if ($endTime) {
            $query->where('start_time', '<=', $startTime)
                  ->where('end_time', '>=', $endTime);
        } else {
            $query->where('start_time', '<=', $startTime)
                  ->where('end_time', '>=', $startTime);
        }

        return $query->exists();
    }

    /**
     * Get available time slots for a specific day
     */
    public function getAvailableTimeSlots($dayOfWeek)
    {
        return $this->availabilities()
            ->where('day_of_week', strtolower($dayOfWeek))
            ->where('is_available', true)
            ->get();
    }
}
