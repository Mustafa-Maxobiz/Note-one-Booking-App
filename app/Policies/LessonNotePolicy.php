<?php

namespace App\Policies;

use App\Models\LessonNote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any lesson notes.
     */
    public function viewAny(User $user)
    {
        return true; // All authenticated users can view lesson notes
    }

    /**
     * Determine whether the user can view the lesson note.
     */
    public function view(User $user, LessonNote $lessonNote)
    {
        // Students can view notes visible to them
        if ($user->isStudent() && $user->student && $lessonNote->student_id === $user->student->id) {
            return $lessonNote->visibility === 'student_and_teacher';
        }

        // Teachers can view their own notes or notes for students they teach
        if ($user->isTeacher() && $user->teacher) {
            return $lessonNote->teacher_id === $user->teacher->id || 
                   $this->hasTeachingRelationship($user, $lessonNote->student_id);
        }

        // Admins can view all notes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create lesson notes.
     */
    public function create(User $user)
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the lesson note.
     */
    public function update(User $user, LessonNote $lessonNote)
    {
        // Teachers can update their own notes
        if ($user->isTeacher() && $user->teacher) {
            return $lessonNote->teacher_id === $user->teacher->id;
        }

        // Admins can update all notes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the lesson note.
     */
    public function delete(User $user, LessonNote $lessonNote)
    {
        // Teachers can delete their own notes
        if ($user->isTeacher() && $user->teacher) {
            return $lessonNote->teacher_id === $user->teacher->id;
        }

        // Admins can delete all notes
        return $user->isAdmin();
    }

    /**
     * Check if teacher has teaching relationship with student.
     */
    private function hasTeachingRelationship(User $user, $studentId)
    {
        if (!$user->teacher) {
            return false;
        }

        // Check if teacher has any bookings with this student
        return \App\Models\Booking::where('teacher_id', $user->teacher->id)
            ->where('student_id', $studentId)
            ->exists();
    }
}
