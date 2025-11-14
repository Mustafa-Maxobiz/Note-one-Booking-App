<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Student extends Model
{
    use SoftDeletes, Auditable;
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'level',
        'grade',
        'learning_style',
        'learning_goals',
        'preferred_subjects',
        'timezone',
        'parent_name',
        'parent_email',
        'parent_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'preferred_subjects' => 'array',
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sessions for the student.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the bookings for the student (alias for sessions).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the payments for the student.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the feedback for the student.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
