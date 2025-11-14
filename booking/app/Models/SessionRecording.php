<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'zoom_meeting_id',
        'recording_id',
        'recording_type',
        'file_name',
        'download_url',
        'play_url',
        'passcode',
        'file_size',
        'duration',
        'recording_start',
        'recording_end',
        'is_processed'
    ];

    protected $casts = [
        'recording_start' => 'datetime',
        'recording_end' => 'datetime',
        'is_processed' => 'boolean'
    ];

    public function session()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function getFormattedDurationAttribute()
    {
        // First try to use the duration field
        if ($this->duration && $this->duration > 0) {
            $duration = $this->duration;
        } 
        // If duration is 0 or null, try to calculate from start/end times
        elseif ($this->recording_start && $this->recording_end) {
            $duration = $this->recording_end->diffInSeconds($this->recording_start);
        } 
        // If we can't calculate, return N/A
        else {
            return 'N/A';
        }
        
        // Handle negative duration (timezone issues)
        if ($duration < 0) {
            $duration = abs($duration);
        }
        
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }
}
