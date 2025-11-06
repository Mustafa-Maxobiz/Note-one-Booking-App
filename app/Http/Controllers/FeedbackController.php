<?php



namespace App\Http\Controllers;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Booking;

use App\Models\Feedback;

use Illuminate\Support\Facades\Auth;



class FeedbackController extends Controller

{

    public function index()

    {

        $user = Auth::user();

        

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

            }

            $feedbacks = Feedback::with(['booking.teacher.user'])

                ->where('student_id', $user->student->id)

                ->orderBy('id', 'desc')

                ->paginate(10);

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            $feedbacks = Feedback::with(['booking.student.user'])

                ->where('teacher_id', $user->teacher->id)

                ->orderBy('id', 'desc')

                ->paginate(10);

        } else {

            $feedbacks = Feedback::with(['booking.teacher.user', 'booking.student.user'])

                ->orderBy('id', 'desc')

                ->paginate(10);

        }



        return view('feedback.index', compact('feedbacks'));

    }



    public function create(Request $request, Booking $booking = null)

    {

        $user = Auth::user();

        

        // If no booking is provided via route parameter, try to get it from query parameter

        if (!$booking && $request->has('booking_id')) {

            $booking = Booking::find($request->booking_id);

        }

        

        // If still no booking, try to get it from the first query parameter (for URLs like ?32)

        if (!$booking) {

            $queryParams = $request->query();

            if (!empty($queryParams)) {

                // Check if any of the query parameter keys are numeric (for URLs like ?32)

                foreach ($queryParams as $key => $value) {

                    if (is_numeric($key)) {

                        $booking = Booking::find($key);

                        if ($booking) break;

                    }

                }

                

                // If still no booking, check the values

                if (!$booking) {

                    $firstParam = reset($queryParams);

                    if (is_numeric($firstParam)) {

                        $booking = Booking::find($firstParam);

                    }

                }

            }

        }

        

        if (!$booking) {
            if ($user->isStudent()) {
                return redirect()->route('student.bookings.index')
                    ->with('error', 'Booking not found. The booking you are looking for does not exist.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.bookings.index')
                    ->with('error', 'Booking not found. The booking you are looking for does not exist.');
            } else {
                return redirect()->route('admin.bookings.index')
                    ->with('error', 'Booking not found. The booking you are looking for does not exist.');
            }
        }

        

        // Check if user can provide feedback for this booking

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

            }

            if ($booking->student_id != $user->student->id) {

                return redirect()->route('student.bookings.index')
                    ->with('error', 'You do not have permission to provide feedback for this booking.');

            }

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            if ($booking->teacher_id != $user->teacher->id) {

                return redirect()->route('teacher.bookings.index')
                    ->with('error', 'You do not have permission to provide feedback for this booking.');

            }

        }



        // Check if booking is completed

        if ($booking->status != 'completed') {

            return back()->withErrors(['feedback' => 'Only completed bookings can be rated.']);

        }



        // Check if feedback already exists

        $existingFeedback = Feedback::where('booking_id', $booking->id)

            ->where('type', $user->isStudent() ? 'student_to_teacher' : 'teacher_to_student')

            ->first();



        if ($existingFeedback) {

            return back()->withErrors(['feedback' => 'You have already provided feedback for this booking.']);

        }



        return view('feedback.create', compact('booking'));

    }



    public function store(Request $request)

    {
        // This is for the resource route POST /feedback
        return redirect()->route('feedback.index')->with('error', 'Please use the booking-specific feedback form.');
    }

    public function storeForBooking(Request $request, $booking)

    {
        // This is for the specific route POST /bookings/{booking}/feedback
        // Manually resolve the booking since route model binding is not working
        $booking = Booking::find($booking);
        
        if (!$booking) {
            return redirect()->route('feedback.index')->with('error', 'Booking not found.');
        }

        $user = Auth::user();

        // Check authorization

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

            }

            if ($booking->student_id != $user->student->id) {

                return redirect()->route('student.bookings.index')
                    ->with('error', 'You do not have permission to submit feedback for this booking.');

            }

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            if ($booking->teacher_id != $user->teacher->id) {

                return redirect()->route('teacher.bookings.index')
                    ->with('error', 'You do not have permission to submit feedback for this booking.');

            }

        }



        // Check if booking is completed

        if ($booking->status != 'completed') {

            return back()->withErrors(['feedback' => 'Only completed bookings can be rated.']);

        }



        $validated = $request->validate([

            'rating' => 'required|integer|min:1|max:5',

            'comment' => 'nullable|string|max:500',

            'is_public' => 'boolean',

        ]);



        // Check if feedback already exists

        $existingFeedback = Feedback::where('booking_id', $booking->id)

            ->where('type', $user->isStudent() ? 'student_to_teacher' : 'teacher_to_student')

            ->first();



        if ($existingFeedback) {

            return back()->withErrors(['feedback' => 'You have already provided feedback for this booking.']);

        }



        // Create feedback with proper authorization
        $feedbackData = [
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'type' => $user->isStudent() ? 'student_to_teacher' : 'teacher_to_student',
            'is_public' => $validated['is_public'] ?? false,
        ];

        // Only set the ID of the user creating the feedback
        if ($user->isStudent()) {
            $feedbackData['student_id'] = $user->student->id;
            // Don't include teacher_id for student feedback
        } elseif ($user->isTeacher()) {
            $feedbackData['teacher_id'] = $user->teacher->id;
            // Don't include student_id for teacher feedback
        }

        Feedback::create($feedbackData);



        return redirect()->route('feedback.index')->with('success', 'Feedback submitted successfully!');

    }



    public function show($id)

    {

        $user = Auth::user();

        
        // Find the feedback and check if it exists
        $feedback = \App\Models\Feedback::find($id);
        
        if (!$feedback) {
            if ($user->isStudent()) {
                return redirect()->route('student.feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } else {
                return redirect()->route('admin.feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            }
        }

        // Check authorization

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');

            }

            if ($feedback->student_id != $user->student->id) {

                return redirect()->route('student.feedback.index')
                    ->with('error', 'You do not have permission to view this feedback.');

            }

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            if ($feedback->teacher_id != $user->teacher->id) {

                return redirect()->route('teacher.feedback.index')
                    ->with('error', 'You do not have permission to view this feedback.');

            }

        }



        return view('feedback.show', compact('feedback'));

    }



    public function edit($id)

    {

        $user = Auth::user();

        
        // Find the feedback and check if it exists
        $feedback = \App\Models\Feedback::find($id);
        if (!$feedback) {
            if ($user->isStudent()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } else {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            }
        }

        // Check authorization - users can only edit feedback they created

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('profile.index')->with('error', 'Please complete your student profile first.');

            }

            // Students can only edit feedback they created (where student_id matches)
            if ($feedback->student_id != $user->student->id || $feedback->type != 'student_to_teacher') {

                return redirect()->route('feedback.index')
                    ->with('error', 'You can only edit feedback you created.');

            }

        } elseif ($user->isTeacher() && $feedback->type != 'teacher_to_student') {

            if (!$user->teacher) {

                return redirect()->route('profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            // Teachers can only edit feedback they created (where teacher_id matches)
            if ($feedback->teacher_id != $user->teacher->id || $feedback->type != 'teacher_to_student') {

                return redirect()->route('feedback.index')
                    ->with('error', 'You can only edit feedback you created.');

            }

        }



        return view('feedback.edit', compact('feedback'));

    }



    public function update(Request $request, $id)

    {

        $user = Auth::user();

        
        // Find the feedback and check if it exists
        $feedback = \App\Models\Feedback::find($id);
        
        if (!$feedback) {
            if ($user->isStudent()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } else {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            }
        }

        // Check authorization - users can only update feedback they created

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('profile.index')->with('error', 'Please complete your student profile first.');

            }

            // Students can only update feedback they created (where student_id matches)
            if ($feedback->student_id != $user->student->id || $feedback->type !== 'student_to_teacher') {

                return redirect()->route('feedback.index')
                    ->with('error', 'You can only update feedback you created.');

            }

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            // Teachers can only update feedback they created (where teacher_id matches)
            if ($feedback->teacher_id != $user->teacher->id || $feedback->type !== 'teacher_to_student') {

                return redirect()->route('teacher.feedback.index')
                    ->with('error', 'You can only update feedback you created.');

            }

        }



        $validated = $request->validate([

            'rating' => 'required|integer|min:1|max:5',

            'comment' => 'nullable|string|max:500',

            'is_public' => 'boolean',

        ]);



        $feedback->update($validated);



        return redirect()->route('feedback.index')->with('success', 'Feedback updated successfully!');

    }



    public function destroy($id)

    {

        $user = Auth::user();

        
        // Find the feedback and check if it exists
        $feedback = \App\Models\Feedback::find($id);
        
        if (!$feedback) {
            if ($user->isStudent()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            } else {
                return redirect()->route('feedback.index')
                    ->with('error', 'Feedback not found. The feedback you are looking for does not exist.');
            }
        }

        // Check authorization - users can only delete feedback they created

        if ($user->isStudent()) {

            if (!$user->student) {

                return redirect()->route('profile.index')->with('error', 'Please complete your student profile first.');

            }

            // Students can only delete feedback they created (where student_id matches)
            if ($feedback->student_id == $user->student->id && $feedback->type != 'student_to_teacher') {

                return redirect()->route('feedback.index')
                    ->with('error', 'You can only delete feedback you created.');

            }

        } elseif ($user->isTeacher()) {

            if (!$user->teacher) {

                return redirect()->route('profile.index')->with('error', 'Please complete your teacher profile first.');

            }

            // Teachers can only delete feedback they created (where teacher_id matches)
            if ($feedback->teacher_id == $user->teacher->id && $feedback->type != 'teacher_to_student') {

                return redirect()->route('feedback.index')
                    ->with('error', 'You can only delete feedback you created.');

            }

        }



        $feedback->delete();



        return redirect()->route('feedback.index')->with('success', 'Feedback deleted successfully!');

    }

}

