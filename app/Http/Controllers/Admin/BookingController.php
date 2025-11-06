<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\Booking;

use App\Models\Teacher;

use App\Models\Student;

use Illuminate\Http\Request;

use Carbon\Carbon;

class BookingController extends Controller

{

    public function index()

    {

        // Get current time for sorting
        $now = \Carbon\Carbon::now();

        $bookings = Booking::with(['teacher.user', 'student.user'])

            ->whereHas('teacher')

            ->whereHas('student')

            ->orderBy('start_time', 'desc')

            ->orderBy('id', 'desc')

            ->paginate(15);



        return view('admin.bookings.index', compact('bookings'));

    }



    public function create()

    {

        $teachers = Teacher::with('user')->whereHas('user')->get();

        $students = Student::with('user')->whereHas('user')->get();

        

        return view('admin.bookings.create', compact('teachers', 'students'));

    }



    public function store(Request $request)

    {
        \Log::info('Admin booking store method called', [
            'request_data' => $request->all(),
            'user' => auth()->user() ? auth()->user()->email : 'not authenticated'
        ]);

        try {
            $validated = $request->validate([

                'teacher_id' => 'required|exists:teachers,id',

                'student_id' => 'required|exists:students,id',

                'start_date' => 'required|date|after_or_equal:today',

                'start_time' => 'required|date_format:H:i',

                'duration_minutes' => 'required|integer|min:30|max:240',

                'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',

                'notes' => 'nullable|string|max:500',

            ]);

            \Log::info('Admin booking validation passed', ['validated_data' => $validated]);

        // Combine date and time
        $startDateTime = \Carbon\Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);

        $endTime = $startDateTime->copy()->addMinutes((int) $validated['duration_minutes']);



            $booking = Booking::create([

                'teacher_id' => $validated['teacher_id'],

                'student_id' => $validated['student_id'],

                'start_time' => $startDateTime,

                'end_time' => $endTime,

                'duration_minutes' => $validated['duration_minutes'],

                'status' => $validated['status'],

                'price' => 0,

                'notes' => $validated['notes'],

            ]);

            \Log::info('Admin booking created successfully', [
                'booking_id' => $booking->id,
                'teacher_id' => $booking->teacher_id,
                'student_id' => $booking->student_id,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time
            ]);

            return redirect()->route('admin.bookings.index')

                ->with('success', 'Booking created successfully.');

        } catch (\Exception $e) {
            \Log::error('Admin booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create booking: ' . $e->getMessage());
        }

    }



    public function show(Booking $booking)

    {

        $booking->load(['teacher.user', 'student.user', 'sessionRecordings']);

        

        return view('admin.bookings.show', compact('booking'));

    }



    public function edit(Booking $booking)

    {

        $teachers = Teacher::with('user')->whereHas('user')->get();

        $students = Student::with('user')->whereHas('user')->get();

        

        return view('admin.bookings.edit', compact('booking', 'teachers', 'students'));

    }



    public function update(Request $request, Booking $booking)

    {

        $validated = $request->validate([

            'teacher_id' => 'required|exists:teachers,id',

            'student_id' => 'required|exists:students,id',

            'start_time' => 'required|date',

            'duration_minutes' => 'required|integer|min:30|max:240',

            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',

            'notes' => 'nullable|string|max:500',

        ]);

        // Store original values for comparison
        $originalStartTime = $booking->start_time;
        $originalEndTime = $booking->end_time;
        $originalTeacherId = $booking->teacher_id;
        $originalStudentId = $booking->student_id;

        $startTime = \Carbon\Carbon::parse($validated['start_time']);

        $endTime = $startTime->copy()->addMinutes((int) $validated['duration_minutes']);

        $booking->update([

            'teacher_id' => $validated['teacher_id'],

            'student_id' => $validated['student_id'],

            'start_time' => $startTime,

            'end_time' => $endTime,

            'duration_minutes' => $validated['duration_minutes'],

            'status' => $validated['status'],

            'price' => 0,

            'notes' => $validated['notes'],

        ]);

        // Check if date/time or participants changed
        $dateTimeChanged = $originalStartTime->ne($startTime) || $originalEndTime->ne($endTime);
        $participantsChanged = $originalTeacherId != $validated['teacher_id'] || $originalStudentId != $validated['student_id'];

        // Send email notifications if date/time or participants changed
        if ($dateTimeChanged || $participantsChanged) {
            try {
                // Load the updated relationships
                $booking->load(['teacher.user', 'student.user']);
                
                // Send email to student
                \App\Services\EmailService::sendBookingUpdateToStudent($booking, $originalStartTime, $originalEndTime);
                
                // Send email to teacher
                \App\Services\EmailService::sendBookingUpdateToTeacher($booking, $originalStartTime, $originalEndTime);
                
                \Log::info('Booking update emails sent successfully', [
                    'booking_id' => $booking->id,
                    'date_time_changed' => $dateTimeChanged,
                    'participants_changed' => $participantsChanged
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send booking update emails', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('admin.bookings.index')

            ->with('success', 'Booking updated successfully.');

    }



    public function destroy(Booking $booking)

    {

        $booking->delete();



        return redirect()->route('admin.bookings.index')

            ->with('success', 'Booking deleted successfully.');

    }



    public function reassign(Request $request, Booking $booking)

    {

        $validated = $request->validate([

            'new_teacher_id' => 'required|exists:teachers,id',

            'new_student_id' => 'required|exists:students,id',

        ]);



        $booking->update([

            'teacher_id' => $validated['new_teacher_id'],

            'student_id' => $validated['new_student_id'],

        ]);



        return redirect()->route('admin.bookings.show', $booking)

            ->with('success', 'Booking reassigned successfully.');

    }



    public function bulkActions(Request $request)

    {

        $request->validate([

            'action' => 'required|in:delete,confirm,cancel,complete',

            'booking_ids' => 'required|array|min:1',

            'booking_ids.*' => 'exists:bookings,id'

        ]);



        $bookingIds = $request->input('booking_ids');

        $action = $request->input('action');

        $updatedCount = 0;



        try {

            switch ($action) {

                case 'delete':

                    // Check if bookings have related records before deleting

                    $bookingsToDelete = Booking::whereIn('id', $bookingIds)->get();

                    $deletedCount = 0;

                    $errors = [];

                    

                    foreach ($bookingsToDelete as $booking) {

                        try {

                            // Check for related records

                            if ($booking->sessionRecordings()->count() > 0) {

                                $errors[] = "Booking #{$booking->id} has session recordings and cannot be deleted.";

                                continue;

                            }

                            

                            // Check for other related records if they exist

                            if (method_exists($booking, 'feedback') && $booking->feedback()->count() > 0) {

                                $errors[] = "Booking #{$booking->id} has feedback and cannot be deleted.";

                                continue;

                            }

                            

                            // Check for notifications that reference this booking

                            $notificationsWithBooking = \App\Models\Notification::where('data->booking_id', $booking->id)->get();

                            if ($notificationsWithBooking->count() > 0) {

                                // Delete notifications that reference this booking

                                $notificationsWithBooking->each(function($notification) {

                                    $notification->delete();

                                });

                            }

                            

                            // Safe to delete

                            $booking->delete();

                            $deletedCount++;

                            

                        } catch (\Exception $e) {

                            $errors[] = "Error deleting booking #{$booking->id}: " . $e->getMessage();

                        }

                    }

                    

                    if ($deletedCount > 0) {

                        $message = "{$deletedCount} booking(s) deleted successfully.";

                        if (count($errors) > 0) {

                            $message .= " Some bookings could not be deleted due to related records.";

                        }

                    } else {

                        $message = "No bookings were deleted. " . implode(' ', $errors);

                    }

                    

                    $updatedCount = $deletedCount;

                    break;



                case 'confirm':

                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->where('status', 'pending')

                        ->update(['status' => 'confirmed']);

                    $message = "{$updatedCount} booking(s) confirmed successfully.";

                    break;



                case 'cancel':

                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->whereIn('status', ['pending', 'confirmed'])

                        ->update(['status' => 'cancelled']);

                    $message = "{$updatedCount} booking(s) cancelled successfully.";

                    break;



                case 'complete':

                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->where('status', 'confirmed')

                        ->update(['status' => 'completed']);

                    $message = "{$updatedCount} booking(s) marked as completed successfully.";

                    break;



                default:

                    return response()->json([

                        'success' => false,

                        'message' => 'Invalid action specified.'

                    ], 400);

            }



            return response()->json([

                'success' => true,

                'message' => $message,

                'updated_count' => $updatedCount

            ]);



        } catch (\Exception $e) {

            \Log::error('Bulk action error: ' . $e->getMessage(), [

                'action' => $action,

                'booking_ids' => $bookingIds,

                'error' => $e->getMessage(),

                'trace' => $e->getTraceAsString()

            ]);

            

            return response()->json([

                'success' => false,

                'message' => 'An error occurred while performing the bulk action: ' . $e->getMessage()

            ], 500);

        }

    }

    public function cleanupOrphaned()
    {
        try {
            // Find bookings with missing teacher or student relationships
            $orphanedBookings = Booking::whereDoesntHave('teacher')
                ->orWhereDoesntHave('student')
                ->get();

            $deletedCount = 0;
            $errors = [];

            foreach ($orphanedBookings as $booking) {
                try {
                    // Check for related records before deletion
                    if ($booking->sessionRecordings()->count() > 0) {
                        $errors[] = "Booking #{$booking->id} has session recordings and cannot be deleted.";
                        continue;
                    }

                    // Delete notifications that reference this booking
                    $notificationsWithBooking = \App\Models\Notification::where('data->booking_id', $booking->id)->get();
                    if ($notificationsWithBooking->count() > 0) {
                        $notificationsWithBooking->each(function($notification) {
                            $notification->delete();
                        });
                    }

                    $booking->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error deleting orphaned booking #{$booking->id}: " . $e->getMessage();
                }
            }

            $message = "Cleanup completed. {$deletedCount} orphaned booking(s) deleted.";
            if (count($errors) > 0) {
                $message .= " Some bookings could not be deleted: " . implode(' ', $errors);
            }

            return redirect()->route('admin.bookings.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Orphaned bookings cleanup error: ' . $e->getMessage());
            
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Error during cleanup: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a booking with confirmation
     */
    public function delete(Request $request)
    {
        $booking = Booking::findOrFail($request->id);
        
        // Check if booking can be deleted
        if (!$booking->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => $booking->getDeletionBlockReason()
            ]);
        }

        return $booking->softDeleteWithConfirmation($request);
    }

    /**
     * Restore a soft deleted booking
     */
    public function restore(Request $request)
    {
        $booking = Booking::withTrashed()->findOrFail($request->id);
        return $booking->restoreWithConfirmation($request);
    }

    /**
     * Show trashed bookings
     */
    public function trashed()
    {
        $bookings = Booking::onlyTrashed()
            ->with(['teacher.user', 'student.user'])
            ->whereHas('teacher')
            ->whereHas('student')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.trashed', compact('bookings'));
    }

    public function getAvailableTimeSlots(Request $request)

    {   
        try {

            $validated = $request->validate([

                'date' => 'required|date|after_or_equal:' . now()->toDateString(),

                'format' => 'nullable|in:12,24'

            ]);

            

            $selectedDate = Carbon::parse($validated['date']);

            $dayOfWeek = strtolower($selectedDate->format('l'));

            $timeFormat = $validated['format'] ?? '12';

            

            // Get all available teachers for this day

            $availableTeachers = Teacher::with(['user', 'availabilities'])

                ->where('is_available', true)

                ->where('is_verified', true)

                ->get()

                ->filter(function($teacher) use ($dayOfWeek) {

                    return $teacher->availabilities()

                        ->where('day_of_week', $dayOfWeek)

                        ->where('is_available', true)

                        ->exists();

                });

            

            // Generate 30-minute time slots from 8 AM to 11:30 PM (23:30)

            $timeSlots = [];

            // For today, start from current time (rounded to next 30-minute slot)
            $startHour = 8;
            if ($selectedDate->isToday()) {
                $currentTime = now();
                $currentHour = $currentTime->format('H');
                $currentMinute = $currentTime->format('i');

                // Round up to next 30-minute slot
                if ($currentMinute > 30) {
                    $currentHour = (int)$currentHour + 1;
                    $currentMinute = '00';
                } else {
                    $currentMinute = '30';
                }

                $startHour = max(8, (int)$currentHour);
            }

            for ($hour = $startHour; $hour < 24; $hour++) {

                for ($minute = 0; $minute < 60; $minute += 30) {

                    $time = sprintf('%02d:%02d', $hour, $minute);

                    $timeCarbon = Carbon::parse($time);

                    $endTime = $timeCarbon->copy()->addMinutes(30)->format('H:i');
                    
                    // Skip time slots that would cross midnight (e.g., 11:30 PM - 12:00 AM)
                    if ($endTime === '00:00') {
                        continue;
                    }

                    // For today, skip past time slots
                    if ($selectedDate->isToday()) {
                        $currentTime = now();
                        $currentTimeStr = $currentTime->format('H:i');
                        if ($time <= $currentTimeStr) {
                            continue;
                        }
                    }

                    

                    // Check which teachers are available for this time slot

                    $availableTeachersForSlot = $availableTeachers->filter(function($teacher) use ($dayOfWeek, $time, $endTime, $selectedDate) {

                        // Check if teacher has availability for this time slot

                        $hasAvailability = $teacher->availabilities()

                            ->where('day_of_week', $dayOfWeek)

                            ->where('is_available', true)

                            ->where('start_time', '<=', $time)

                            ->where('end_time', '>=', $endTime)

                            ->exists();

                        if (!$hasAvailability) {

                            return false;

                        }

                        

                        // Check if teacher has any conflicts (existing bookings)

                        $hasConflict = Booking::where('teacher_id', $teacher->id)

                            ->whereDate('start_time', $selectedDate)

                            ->whereNotIn('status', ['cancelled', 'no_show'])

                            ->where(function($query) use ($time, $endTime, $selectedDate) {

                                $query->whereBetween('start_time', [

                                    $selectedDate->copy()->setTimeFromTimeString($time),

                                    $selectedDate->copy()->setTimeFromTimeString($endTime)->subMinute()

                                ])

                                ->orWhereBetween('end_time', [

                                    $selectedDate->copy()->setTimeFromTimeString($time)->addMinute(),

                                    $selectedDate->copy()->setTimeFromTimeString($endTime)

                                ])

                                ->orWhere(function($q) use ($time, $endTime, $selectedDate) {

                                    $q->where('start_time', '<=', $selectedDate->copy()->setTimeFromTimeString($time))

                                      ->where('end_time', '>=', $selectedDate->copy()->setTimeFromTimeString($endTime));

                                });

                            })

                            ->exists();

                        

                        return !$hasConflict;

                    });

                    

                    // Only add time slots where teachers are actually available

                    if ($availableTeachersForSlot->count() > 0) {

                        // Format time display based on format preference

                        $displayTime = $timeFormat === '24' ? $time : $timeCarbon->format('g:i A');

                        

                        $timeSlots[] = [

                            'time' => $time,

                            'displayTime' => $displayTime,

                            'available' => true,

                            'teachers' => $availableTeachersForSlot->map(function($teacher) {

                                return [

                                    'id' => $teacher->id,

                                    'name' => $teacher->user->name,

                                    'email' => $teacher->user->email,

                                    'qualifications' => $teacher->qualifications,

                                    'experience_years' => $teacher->experience_years

                                ];

                            })->values()->toArray()

                        ];

                    }

                }

            }

            

            return response()->json([

                'success' => true,

                'timeSlots' => $timeSlots,

                'date' => $selectedDate->format('Y-m-d'),

                'dayOfWeek' => $dayOfWeek

            ]);

            

        } catch (\Exception $e) {

            \Log::error('Error getting available time slots', [

                'error' => $e->getMessage(),

                'request' => $request->all()

            ]);

            

            return response()->json([

                'success' => false,

                'message' => 'Failed to load available time slots',

                'error' => $e->getMessage()

            ], 500);

        }

    }


}

