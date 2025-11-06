<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class LessonNote extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'booking_id',
        'lesson_date',
        'title',
        'content',
        'attachments',
        'visibility',
        'additional_students',
    ];

    protected $casts = [
        'lesson_date' => 'datetime',
        'attachments' => 'array',
        'additional_students' => 'array',
    ];

    /**
     * Get the student for this lesson note.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the teacher for this lesson note.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the booking for this lesson note.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope to get notes visible to students.
     */
    public function scopeVisibleToStudent($query)
    {
        return $query->where('visibility', 'student_and_teacher');
    }

    /**
     * Scope to get notes for a specific student.
     * Includes notes where student is primary student OR in additional_students.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where(function($q) use ($studentId) {
            $q->where('student_id', $studentId)
              ->orWhereJsonContains('additional_students', $studentId);
        });
    }

    /**
     * Scope to get notes by a specific teacher.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Get the lesson number for this student.
     */
    public function getLessonNumberAttribute()
    {
        $studentId = $this->student_id;
        $lessonDate = $this->lesson_date;
        
        return static::where('student_id', $studentId)
            ->where('lesson_date', '<=', $lessonDate)
            ->orderBy('lesson_date')
            ->orderBy('id')
            ->count();
    }

    /**
     * Get formatted lesson date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->lesson_date->format('d M Y');
    }

    /**
     * Get the display title with lesson number.
     */
    public function getDisplayTitleAttribute()
    {
        return "Lesson {$this->lesson_number} — {$this->formatted_date} — {$this->title}";
    }

    /**
     * Get all students for this lesson note (primary + additional).
     */
    public function getAllStudents()
    {
        $students = collect([$this->student_id]);
        
        if ($this->additional_students && is_array($this->additional_students)) {
            $students = $students->merge($this->additional_students);
        }
        
        return $students->unique()->values();
    }

    /**
     * Check if a student is associated with this lesson note.
     */
    public function hasStudent($studentId)
    {
        return $this->student_id == $studentId || 
               (is_array($this->additional_students) && in_array($studentId, $this->additional_students));
    }
}
