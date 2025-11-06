<?php



namespace App\Http\Controllers\Student;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Teacher;

use App\Models\TeacherAvailability;

use App\Models\Booking;

use App\Services\NotificationService;

use App\Services\EmailService;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;



class BookingController extends Controller

{

    public function index()

    {

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        

        $bookings = Booking::where('student_id', $student->id)

            ->with(['teacher.user'])

            ->orderBy('id', 'desc')

            ->paginate(10);



        return view('student.bookings.index', compact('bookings'));

    }



    public function create()

    {

        // Redirect to calendar view (new default)
        return redirect()->route('student.booking.calendar');

    }

    
    /**
     * Show the legacy search-based booking form
     */
    public function createSearch()

    {

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        

        return view('student.booking.create');

    }

    

    /**

     * Show the new calendar-based booking interface

     */

    public function calendar()

    {

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        

        return view('student.booking.calendar');

    }



    public function search(Request $request)

    {

        try {

            $validated = $request->validate([

                'date' => 'required|date|after_or_equal:today',

                'time' => 'required|date_format:H:i',

                'duration_minutes' => 'required|integer|min:30|max:240',

            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            if ($request->ajax()) {

                return response()->json([

                    'success' => false,

                    'message' => 'Validation failed: ' . implode(', ', $e->errors()['date'] ?? []) . 

                                implode(', ', $e->errors()['time'] ?? []) . 

                                implode(', ', $e->errors()['duration_minutes'] ?? [])

                ], 422);

            }

            throw $e;

        }



        $selectedDate = Carbon::parse($validated['date']);

        $dayOfWeek = strtolower($selectedDate->format('l'));

        $startTime = $validated['time'];

        $duration = (int) $validated['duration_minutes'];

        
        // If booking is for today, check if the time is in the future
        if ($selectedDate->isToday()) {
            $requestedDateTime = $selectedDate->copy()->setTimeFromTimeString($startTime);
            if ($requestedDateTime <= now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot book a session in the past. Please select a future time for today or choose a future date.'
                ], 422);
            }
        }

        

        // Calculate end time

        $endTime = Carbon::parse($startTime)->addMinutes($duration)->format('H:i');
        
        // Create DateTime objects for conflict checking
        $startDateTime = Carbon::parse($validated['date'] . ' ' . $startTime);
        $endDateTime = Carbon::parse($validated['date'] . ' ' . $endTime);



        // Find teachers available at this time using the new helper methods

        $availableTeachers = Teacher::with(['user', 'availabilities'])

            ->where('is_available', true)

            ->where('is_verified', true)

            ->get()

            ->filter(function($teacher) use ($dayOfWeek, $startTime, $endTime, $selectedDate, $startDateTime, $endDateTime) {

                // Check if teacher is available at this time

                $isAvailable = $teacher->isAvailableOn($dayOfWeek, $startTime, $endTime);

                

                if (!$isAvailable) {

                    \Log::info('Teacher not available for search', [

                        'teacher_id' => $teacher->id,

                        'teacher_name' => $teacher->user->name,

                        'day_of_week' => $dayOfWeek,

                        'start_time' => $startTime,

                        'end_time' => $endTime

                    ]);

                    return false;

                }

                

                // Check if teacher has any conflicts at this time

                // $conflict = Booking::where('teacher_id', $teacher->id)

                //     ->whereDate('start_time', $selectedDate)

                //     ->whereNotIn('status', ['cancelled', 'no_show'])

                //     ->where(function($query) use ($startTime, $endTime) {

                //         $query->whereBetween('start_time', [$startTime, $endTime])

                //             ->orWhereBetween('end_time', [$startTime, $endTime])

                //             ->orWhere(function($q) use ($startTime, $endTime) {

                //                 $q->where('start_time', '<=', $startTime)

                //                   ->where('end_time', '>=', $endTime);

                //             });

                //     })->first();
                
                $conflict = Booking::where('teacher_id', $teacher->id)
                ->whereDate('start_time', $selectedDate)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->where(function($query) use ($startDateTime, $endDateTime) {
                    $query->where('start_time', '<', $endDateTime)   // existing start < new end
                        ->where('end_time', '>', $startDateTime);  // existing end > new start
                })
                ->first();

                

                if ($conflict) {

                    \Log::info('Teacher has conflict for search', [

                        'teacher_id' => $teacher->id,

                        'teacher_name' => $teacher->user->name,

                        'date' => $selectedDate->format('Y-m-d'),

                        'start_time' => $startTime,

                        'end_time' => $endTime

                    ]);

                }

                

                return !$conflict;

            });



        // If this is an AJAX request, return JSON

        if ($request->ajax()) {

            try {

                return response()->json([

                    'success' => true,

                    'teachers' => $availableTeachers->map(function($teacher) {

                        return [

                            'id' => $teacher->id,

                            'name' => $teacher->user->name,

                            'email' => $teacher->user->email,

                            'phone' => $teacher->user->phone ?? '',

                            'qualifications' => $teacher->qualifications ?? 'Qualified Teacher',

                            'experience_years' => $teacher->experience_years ?? 0,

                            'bio' => $teacher->bio ?? '',

                            'available_days' => $teacher->availabilities->pluck('day_of_week')->unique()->sort()->values()

                        ];

                    })->values(), // This ensures we get a proper array with sequential keys

                    'count' => $availableTeachers->count(),

                    'search_criteria' => [

                        'date' => $selectedDate->format('Y-m-d'),

                        'time' => $startTime,

                        'duration' => $duration

                    ]

                ]);

            } catch (\Exception $e) {

                \Log::error('Error in AJAX search response', [

                    'error' => $e->getMessage(),

                    'trace' => $e->getTraceAsString()

                ]);

                

                return response()->json([

                    'success' => false,

                    'message' => 'An error occurred while processing the search. Please try again.'

                ], 500);

            }

        }



        return view('student.booking.search_results', compact('availableTeachers', 'selectedDate', 'startTime', 'duration'));

    }



    public function getTeacherAvailability(Request $request)

    {

        $teacherId = $request->teacher_id;

        $date = $request->date;

        

        $teacher = Teacher::findOrFail($teacherId);

        $selectedDate = Carbon::parse($date);

        $dayOfWeek = strtolower($selectedDate->format('l'));

        

        $availabilities = TeacherAvailability::where('teacher_id', $teacherId)

            ->where('day_of_week', $dayOfWeek)

            ->get();

        

        // Get existing bookings for this teacher on this date

        $existingBookings = Booking::where('teacher_id', $teacherId)

            ->whereDate('start_time', $selectedDate)

            ->whereNotIn('status', ['cancelled', 'no_show'])

            ->pluck('start_time')

            ->map(function($time) {

                return Carbon::parse($time)->format('H:i');

            });

        

        return response()->json([

            'availabilities' => $availabilities,

            'existing_bookings' => $existingBookings,

            'day_of_week' => $dayOfWeek

        ]);

    }

    

    /**

     * Get available time slots for a specific date

     */

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

            

            // Generate 30-minute time slots based on actual teacher availability

            $timeSlots = [];
            
            // Get all available time ranges from teachers for this day
            $allTimeRanges = [];
            foreach ($availableTeachers as $teacher) {
                $teacherAvailabilities = $teacher->availabilities()
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true)
                    ->get();
                
                foreach ($teacherAvailabilities as $availability) {
                    $allTimeRanges[] = [
                        'start' => $availability->start_time,
                        'end' => $availability->end_time
                    ];
                }
            }
            
            // Find the earliest start time and latest end time
            $earliestStart = '23:59';
            $latestEnd = '00:00';
            
            foreach ($allTimeRanges as $range) {
                if ($range['start'] < $earliestStart) {
                    $earliestStart = $range['start'];
                }
                if ($range['end'] > $latestEnd) {
                    $latestEnd = $range['end'];
                }
            }
            
            // If no availability found, use default range
            if ($earliestStart === '23:59' && $latestEnd === '00:00') {
                $earliestStart = '06:00';
                $latestEnd = '23:30';
            }
            
            // For today, start from current time (rounded to next 30-minute slot)
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
                
                $earliestStart = sprintf('%02d:%02d', $currentHour, $currentMinute);
            }
            
            // Convert to Carbon for easier manipulation
            $startTime = Carbon::parse($earliestStart);
            $endTime = Carbon::parse($latestEnd);
            
            // Generate time slots from earliest available time to latest available time
            $currentTime = $startTime->copy();
            while ($currentTime->lte($endTime)) {
                $time = $currentTime->format('H:i');
                $timeCarbon = $currentTime->copy();
                $slotEndTime = $currentTime->copy()->addMinutes(30)->format('H:i');
                
                // Skip if slot would go past midnight (unless teacher is available past midnight)
                if ($slotEndTime === '00:00' && $latestEnd < '23:59') {
                    $currentTime->addMinutes(30);
                    continue;
                }

                    

                // Check which teachers are available for this time slot
                $availableTeachersForSlot = $availableTeachers->filter(function($teacher) use ($dayOfWeek, $time, $slotEndTime, $selectedDate) {

                    // Check if teacher has availability for this time slot
                    $hasAvailability = $teacher->availabilities()
                        ->where('day_of_week', $dayOfWeek)
                        ->where('is_available', true)
                        ->where('start_time', '<=', $time)
                        ->where('end_time', '>=', $slotEndTime)
                        ->exists();

                    if (!$hasAvailability) {
                        return false;
                    }

                    // Check if teacher has any conflicts (existing bookings)
                    $hasConflict = Booking::where('teacher_id', $teacher->id)
                        ->whereDate('start_time', $selectedDate)
                        ->whereNotIn('status', ['cancelled', 'no_show'])
                        ->where(function($query) use ($time, $slotEndTime, $selectedDate) {
                            $query->whereBetween('start_time', [
                                $selectedDate->copy()->setTimeFromTimeString($time),
                                $selectedDate->copy()->setTimeFromTimeString($slotEndTime)->subMinute()
                            ])
                            ->orWhereBetween('end_time', [
                                $selectedDate->copy()->setTimeFromTimeString($time)->addMinute(),
                                $selectedDate->copy()->setTimeFromTimeString($slotEndTime)
                            ])
                            ->orWhere(function($q) use ($time, $slotEndTime, $selectedDate) {
                                $q->where('start_time', '<=', $selectedDate->copy()->setTimeFromTimeString($time))
                                  ->where('end_time', '>=', $selectedDate->copy()->setTimeFromTimeString($slotEndTime));
                            });
                        })
                        ->exists();

                    return !$hasConflict;
                });

                // Only add time slots where teachers are actually available
                if ($availableTeachersForSlot->count() > 0) {
                    // Skip past time slots for today
                    if ($selectedDate->isToday()) {
                        $now = now();
                        $slotDateTime = $selectedDate->copy()->setTimeFromTimeString($time);
                        
                        // Skip if this time slot is in the past
                        if ($slotDateTime <= $now) {
                            $currentTime->addMinutes(30);
                            continue;
                        }
                    }
                    
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
                
                // Move to next 30-minute slot
                $currentTime->addMinutes(30);
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



    public function store(Request $request)

    {

        // Log the request data for debugging

        \Log::info('Student booking request received', $request->all());

        \Log::info('Request method', ['method' => $request->method()]);

        \Log::info('Request URL', ['url' => $request->url()]);

        

        try {

            $validated = $request->validate([

                'teacher_id' => 'required|exists:teachers,id',

                'date' => 'required|date|after_or_equal:today',

                'time' => 'required|date_format:H:i',

                'duration_minutes' => 'required|integer|min:30|max:240',

                'notes' => 'nullable|string|max:1000',

            ]);

            \Log::info('Validation passed', $validated);

        } catch (\Illuminate\Validation\ValidationException $e) {

            \Log::error('Validation failed', $e->errors());

            throw $e;

        }



        $teacher = Teacher::findOrFail($validated['teacher_id']);

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        $selectedDate = Carbon::parse($validated['date']);

        $startDateTime = $selectedDate->copy()->setTimeFromTimeString($validated['time']);

        $endDateTime = $startDateTime->copy()->addMinutes((int) $validated['duration_minutes']);

        
        // If booking is for today, check if the time is in the future
        if ($selectedDate->isToday() && $startDateTime <= now()) {
            return back()->withErrors(['time' => 'Cannot book a session in the past. Please select a future time for today or choose a future date.']);
        }

        

        // Check if teacher is available at this time using the new helper method

        $dayOfWeek = strtolower($selectedDate->format('l'));

        \Log::info('Checking availability', [

            'day' => $dayOfWeek,

            'start_time' => $validated['time'],

            'end_time' => $endDateTime->format('H:i')

        ]);

        

        $isAvailable = $teacher->isAvailableOn($dayOfWeek, $validated['time'], $endDateTime->format('H:i'));

        

        if (!$isAvailable) {

            $availableTimes = TeacherAvailability::where('teacher_id', $teacher->id)->where('day_of_week', $dayOfWeek)->get()->pluck('start_time', 'end_time');

            \Log::warning('Teacher not available at this time', [

                'teacher_id' => $teacher->id,

                'day_of_week' => $dayOfWeek,

                'requested_time' => $validated['time'],

                'available_times' => $availableTimes

            ]);

            return back()->withErrors(['time' => 'Teacher is not available at this time. Please check teacher availability.']);

        }

        

        // Check for conflicts (exclude cancelled bookings)

        $conflict = Booking::where('teacher_id', $teacher->id)

            ->whereDate('start_time', $selectedDate)

            ->whereNotIn('status', ['cancelled', 'no_show']) // Exclude cancelled and no-show bookings

            ->where(function($query) use ($startDateTime, $endDateTime) {

                $query->where(function($q) use ($startDateTime, $endDateTime) {
                    // New booking starts during existing booking
                    $q->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
                });

            })->first();

        

        if ($conflict) {

            \Log::warning('Time slot conflict found', [

                'teacher_id' => $teacher->id,

                'date' => $selectedDate->format('Y-m-d'),

                'start_time' => $startDateTime->format('Y-m-d H:i:s'),

                'end_time' => $endDateTime->format('Y-m-d H:i:s')

            ]);

            return back()
                ->withErrors(['time' => 'This time slot is already booked.'])
                ->with('error', 'Time slot conflict: This time slot is already booked by another student. Please choose a different time.');

        }

        

        // Set default price (since hourly_rate is removed)

        $price = 0; // Free sessions

        

        // Create booking

        $booking = Booking::create([

            'teacher_id' => $teacher->id,

            'student_id' => $student->id,

            'start_time' => $startDateTime,

            'end_time' => $endDateTime,

            'duration_minutes' => $validated['duration_minutes'],

            'status' => 'pending',

            'price' => $price,

            'notes' => $validated['notes'] ?? null,

        ]);



        \Log::info('Booking created successfully', [

            'booking_id' => $booking->id,

            'teacher_id' => $teacher->id,

            'student_id' => $student->id,

            'start_time' => $startDateTime->format('Y-m-d H:i:s'),

            'end_time' => $endDateTime->format('Y-m-d H:i:s')

        ]);



        // Send notifications

        NotificationService::lessonBooked($booking);

        

        // Send email notification to teacher

        EmailService::sendBookingRequestToTeacher($booking);

        

        \Log::info('Redirecting to bookings index after successful booking');

        return redirect()->route('student.bookings.index')->with('success', 'Booking created successfully! Please wait for teacher confirmation.');

    }



    public function show($id)

    {

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('student.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this student
        if ($booking->student_id != $student->id) {

            return redirect()->route('student.bookings.index')
                ->with('error', 'You do not have permission to view this booking.');

        }



        $booking->load(['teacher.user', 'student.user', 'feedback', 'sessionRecordings']);

        

        return view('student.bookings.show', compact('booking'));

    }



    public function cancel($id)

    {

        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

        }

        
        // Find the booking and check if it exists
        $booking = Booking::find($id);
        
        if (!$booking) {
            return redirect()->route('student.bookings.index')
                ->with('error', 'Booking not found. The booking you are looking for does not exist.');
        }

        // Ensure the booking belongs to this student
        if ($booking->student_id != $student->id) {

            return redirect()->route('student.bookings.index')
                ->with('error', 'You do not have permission to cancel this booking.');

        }



        // Only allow cancellation if booking is still pending or confirmed

        if (!in_array($booking->status, ['pending', 'confirmed'])) {

            return back()->withErrors(['booking' => 'This booking cannot be cancelled.']);

        }



        $booking->update(['status' => 'cancelled']);

        

        // Send notification to teacher

        NotificationService::bookingCancelled($booking);

        

        return redirect()->route('student.bookings.index')

            ->with('success', 'Booking cancelled successfully.');

    }



    public function bulkActions(Request $request)

    {

        $request->validate([

            'action' => 'required|in:cancel',

            'booking_ids' => 'required|array|min:1',

            'booking_ids.*' => 'exists:bookings,id'

        ]);



        $student = Auth::user()->student;

        

        // Check if student profile exists

        if (!$student) {

            return response()->json([

                'success' => false,

                'message' => 'Please complete your student profile first.'

            ], 403);

        }

        $bookingIds = $request->input('booking_ids');

        $action = $request->input('action');

        $updatedCount = 0;



        try {

            // Ensure all bookings belong to this student

            $bookings = Booking::whereIn('id', $bookingIds)

                ->where('student_id', $student->id)

                ->get();



            if ($bookings->count() !== count($bookingIds)) {

                return response()->json([

                    'success' => false,

                    'message' => 'Some bookings do not belong to you or do not exist.'

                ], 403);

            }



            switch ($action) {

                case 'cancel':

                    $updatedCount = Booking::whereIn('id', $bookingIds)

                        ->where('student_id', $student->id)

                        ->whereIn('status', ['pending', 'confirmed'])

                        ->update(['status' => 'cancelled']);

                    

                    // Send notifications for cancelled bookings

                    $cancelledBookings = Booking::whereIn('id', $bookingIds)

                        ->where('student_id', $student->id)

                        ->where('status', 'cancelled')

                        ->get();

                    

                    foreach ($cancelledBookings as $booking) {

                        NotificationService::bookingCancelled($booking);

                    }

                    

                    $message = "{$updatedCount} booking(s) cancelled successfully.";

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

            return response()->json([

                'success' => false,

                'message' => 'An error occurred while performing the bulk action: ' . $e->getMessage()

            ], 500);

        }

    }

}

