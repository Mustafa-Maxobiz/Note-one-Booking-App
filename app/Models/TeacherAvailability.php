<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAvailability extends Model
{
    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'is_available' => 'boolean',
    ];

    /**
     * Get the teacher for this availability.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Check if a given time falls within this availability window
     */
    public function isTimeAvailable($time)
    {
        $timeObj = \Carbon\Carbon::parse($time);
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);
        
        return $timeObj->between($startTime, $endTime);
    }

    /**
     * Check if a time range falls within this availability window
     */
    public function isTimeRangeAvailable($startTime, $endTime)
    {
        $startTimeObj = \Carbon\Carbon::parse($startTime);
        $endTimeObj = \Carbon\Carbon::parse($endTime);
        $availabilityStart = \Carbon\Carbon::parse($this->start_time);
        $availabilityEnd = \Carbon\Carbon::parse($this->end_time);
        
        return $startTimeObj->gte($availabilityStart) && $endTimeObj->lte($availabilityEnd);
    }
}
