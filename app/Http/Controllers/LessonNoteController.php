<?php

namespace App\Http\Controllers;

use App\Models\LessonNote;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LessonNoteController extends Controller
{
    /**
     * Display a listing of lesson notes for a student.
     */
    public function index(Request $request)
    {
        $studentId = $request->get('student_id');
        $teacherId = $request->get('teacher_id');
        
        // Optimize query by loading only essential relationships
        $query = LessonNote::with(['student.user', 'teacher.user'])
            ->orderBy('lesson_date', 'desc');

        // Apply role-based filtering
        if (Auth::user()->isStudent()) {
            // Students can only see their own lesson notes
            $query->forStudent(Auth::user()->student->id)
                  ->visibleToStudent();
        } else {
            // Teachers and admins can filter by student if specified
            if ($studentId) {
                $query->forStudent($studentId);
            }

            // Filter by teacher if specified
            if ($teacherId) {
                $query->byTeacher($teacherId);
            }
        }
        
        // Apply filtering for teachers - only show notes for students they teach
        if (Auth::user()->isTeacher() && Auth::user()->teacher) {
            $teacherId = Auth::user()->teacher->id;
            
            // Optimize: Use a single query with subquery instead of separate queries
            $query->where(function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId) // Teacher's own notes
                  ->orWhereExists(function($subQuery) use ($teacherId) {
                      $subQuery->select(\DB::raw(1))
                               ->from('bookings')
                               ->whereColumn('bookings.student_id', 'lesson_notes.student_id')
                               ->where('bookings.teacher_id', $teacherId)
                               ->where('bookings.status', 'confirmed');
                  });
            });
        }

        $lessonNotes = $query->paginate(15);

        return view('lesson-notes.index', compact('lessonNotes', 'studentId', 'teacherId'));
    }

    /**
     * Show the form for creating a new lesson note.
     */
    public function create(Request $request)
    {
        $studentId = $request->get('student_id');
        $bookingId = $request->get('booking_id');
        
        $student = null;
        $booking = null;
        
        if ($studentId) {
            $student = Student::with('user')->findOrFail($studentId);
        }
        
        if ($bookingId) {
            $booking = Booking::with(['student.user', 'teacher.user'])->findOrFail($bookingId);
            $student = $booking->student;
        }

        // Load students based on user role and relationship
        if ($student) {
            // If specific student is provided, only show that student
            $students = collect([$student]);
        } elseif (Auth::user()->isTeacher() && Auth::user()->teacher) {
            // For teachers, only show students they have confirmed bookings with
            $teacherId = Auth::user()->teacher->id;
            $studentIds = \App\Models\Booking::where('teacher_id', $teacherId)
                ->where('status', 'confirmed')
                ->distinct()
                ->pluck('student_id')
                ->toArray();
            
            $students = Student::with('user')->whereIn('id', $studentIds)->get();
        } else {
            // For admins, show all students
            $students = Student::with('user')->get();
        }

        return view('lesson-notes.create', compact('student', 'booking', 'students'));
    }

    /**
     * Store a newly created lesson note.
     */
    public function store(Request $request)
    {
        // Handle both single student and multiple students
        $studentIds = $request->student_id ? [$request->student_id] : $request->student_ids;
        
        $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'lesson_date' => 'required|date',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'visibility' => 'required|in:student_and_teacher,teacher_only',
            'attachments' => 'nullable|array',
        ]);

        // Ensure at least one student is selected
        if (empty($studentIds)) {
            return back()->withErrors(['student_ids' => 'Please select at least one student.']);
        }
        
        // Remove any empty values
        $studentIds = array_filter($studentIds);
        
        if (empty($studentIds)) {
            return back()->withErrors(['student_ids' => 'Please select at least one student.']);
        }

        // Ensure booking belongs to one of the selected students if provided
        if ($request->booking_id) {
            $booking = Booking::findOrFail($request->booking_id);
            if (!in_array($booking->student_id, $studentIds)) {
                return back()->withErrors(['booking_id' => 'The selected booking does not belong to any of the selected students.']);
            }
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            \Log::info('File upload detected', ['files' => $request->file('attachments')]);
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                \Log::info('Attempting to store file', ['filename' => $filename, 'original_name' => $file->getClientOriginalName()]);
                
                // Use Storage facade for better error handling
                $path = \Storage::disk('public')->putFileAs('attachments', $file, $filename);
                \Log::info('File stored successfully', ['path' => $path, 'filename' => $filename]);
                
                $attachments[] = $filename;
            }
        } else {
            \Log::info('No files detected in request');
        }

        // Create a single lesson note for all selected students
        $lessonNote = LessonNote::create([
            'student_id' => $studentIds[0], // Primary student (first selected)
            'teacher_id' => Auth::user()->teacher->id,
            'booking_id' => $request->booking_id,
            'lesson_date' => $request->lesson_date,
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'attachments' => $attachments,
            'additional_students' => count($studentIds) > 1 ? array_slice($studentIds, 1) : null, // Store additional students
        ]);

        $message = count($studentIds) > 1 
            ? 'Lesson note created successfully for ' . count($studentIds) . ' students.'
            : 'Lesson note created successfully.';

        return redirect()->route('lesson-notes.index')
            ->with('success', $message);
    }

    /**
     * Display the specified lesson note.
     */
    public function show(LessonNote $lessonNote)
    {
        Gate::authorize('view', $lessonNote);
        
        $lessonNote->load(['student.user', 'teacher.user', 'booking']);
        
        return view('lesson-notes.show', compact('lessonNote'));
    }

    /**
     * Show the form for editing the specified lesson note.
     */
    public function edit(LessonNote $lessonNote)
    {
        Gate::authorize('update', $lessonNote);
        
        // Load only essential relationships to improve performance
        $lessonNote->load(['student.user', 'teacher.user']);
        
        // Only load booking if it exists
        if ($lessonNote->booking_id) {
            $lessonNote->load('booking');
        }
        
        return view('lesson-notes.edit', compact('lessonNote'));
    }

    /**
     * Update the specified lesson note.
     */
    public function update(Request $request, LessonNote $lessonNote)
    {
        Gate::authorize('update', $lessonNote);

        $request->validate([
            'lesson_date' => 'required|date',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'visibility' => 'required|in:student_and_teacher,teacher_only',
            'attachments' => 'nullable|array',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Handle student changes
        $studentIds = $request->student_ids;
        $primaryStudentId = $studentIds[0]; // First selected student becomes primary
        $additionalStudentIds = count($studentIds) > 1 ? array_slice($studentIds, 1) : null;

        // Handle file uploads and removals
        $attachments = $lessonNote->attachments ?? []; // Keep existing attachments
        
        // Debug: Log the removed_attachments value
        \Log::info('removed_attachments value: ' . $request->removed_attachments);
        \Log::info('Current attachments before removal: ', $attachments);
        
        // Remove deleted attachments
        if ($request->removed_attachments) {
            $removedFiles = explode(',', $request->removed_attachments);
            \Log::info('Files to remove: ', $removedFiles);
            
            $attachments = array_filter($attachments, function($attachment) use ($removedFiles) {
                // Handle both string and array attachments
                if (is_string($attachment)) {
                    return !in_array($attachment, $removedFiles);
                } elseif (is_array($attachment) && isset($attachment['name'])) {
                    return !in_array($attachment['name'], $removedFiles);
                }
                return true; // Keep attachment if it doesn't match removal criteria
            });
            
            \Log::info('Attachments after removal: ', $attachments);
            
            // Delete files from storage using Storage facade
            foreach ($removedFiles as $filename) {
                if (\Storage::disk('public')->exists('attachments/' . $filename)) {
                    \Storage::disk('public')->delete('attachments/' . $filename);
                    \Log::info('Deleted file via Storage facade: attachments/' . $filename);
                }
            }
        }
        
        // Add new attachments
        if ($request->hasFile('attachments')) {
            \Log::info('File upload detected in update', ['files' => $request->file('attachments')]);
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                \Log::info('Attempting to store file in update', ['filename' => $filename, 'original_name' => $file->getClientOriginalName()]);
                
                // Use Storage facade for better error handling
                $path = \Storage::disk('public')->putFileAs('attachments', $file, $filename);
                \Log::info('File stored successfully in update', ['path' => $path, 'filename' => $filename]);
                
                $attachments[] = $filename;
            }
        }

        \Log::info('Updating lesson note with attachments: ', $attachments);
        
        $lessonNote->update([
            'student_id' => $primaryStudentId,
            'lesson_date' => $request->lesson_date,
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'attachments' => $attachments,
            'additional_students' => $additionalStudentIds,
        ]);
        
        \Log::info('Lesson note updated successfully');

        return redirect()->route('lesson-notes.index', ['student_id' => $lessonNote->student_id])
            ->with('success', 'Lesson note updated successfully.');
    }

    /**
     * Remove the specified lesson note.
     */
    public function destroy(LessonNote $lessonNote)
    {
        Gate::authorize('delete', $lessonNote);
        
        $studentId = $lessonNote->student_id;
        $lessonNote->delete();

        return redirect()->route('lesson-notes.index', ['student_id' => $studentId])
            ->with('success', 'Lesson note deleted successfully.');
    }

    /**
     * Get lesson notes for a specific student (API endpoint).
     */
    public function getForStudent(Student $student)
    {
        Gate::authorize('viewAny', LessonNote::class);
        
        $lessonNotes = LessonNote::forStudent($student->id)
            ->visibleToStudent()
            ->with(['teacher.user', 'booking'])
            ->orderBy('lesson_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lessonNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->display_title,
                    'date' => $note->formatted_date,
                    'teacher' => $note->teacher->user->name,
                    'content' => $note->content,
                ];
            })
        ]);
    }

    /**
     * Remove attachment via AJAX
     */
    public function removeAttachment(Request $request, LessonNote $lessonNote)
    {
        Gate::authorize('update', $lessonNote);

        $filename = $request->input('filename');
        
        if (!$filename) {
            return response()->json([
                'success' => false,
                'message' => 'Filename is required'
            ], 400);
        }

        $attachments = $lessonNote->attachments ?? [];
        
        // Find and remove the attachment
        $originalCount = count($attachments);
        $attachments = array_filter($attachments, function($attachment) use ($filename) {
            // Handle both string and array attachments
            if (is_string($attachment)) {
                return $attachment !== $filename;
            } elseif (is_array($attachment) && isset($attachment['name'])) {
                return $attachment['name'] !== $filename;
            }
            return true; // Keep attachment if it doesn't match removal criteria
        });

        // Check if anything was actually removed
        if (count($attachments) === $originalCount) {
            return response()->json([
                'success' => false,
                'message' => 'Attachment not found'
            ], 404);
        }

        // Update the lesson note
        $lessonNote->update(['attachments' => array_values($attachments)]);

        // Delete the physical file using Storage facade
        if (\Storage::disk('public')->exists('attachments/' . $filename)) {
            \Storage::disk('public')->delete('attachments/' . $filename);
            \Log::info('Deleted file via Storage facade: attachments/' . $filename);
        }

        \Log::info('Attachment removed via AJAX:', [
            'lesson_note_id' => $lessonNote->id,
            'filename' => $filename,
            'remaining_attachments' => $attachments
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attachment removed successfully'
        ]);
    }
}
