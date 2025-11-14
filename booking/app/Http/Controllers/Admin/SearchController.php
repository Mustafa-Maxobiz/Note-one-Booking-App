<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\Booking;

use App\Models\User;

use App\Models\Teacher;

use App\Models\Student;

use App\Models\Payment;

use App\Models\Feedback;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;



class SearchController extends Controller

{

    public function index()

    {

        return view('admin.search.index');

    }



    public function search(Request $request)

    {

        $query = $request->get('query');

        $type = $request->get('type', 'all');

        $dateFrom = $request->get('date_from');

        $dateTo = $request->get('date_to');



        if (empty($query) && empty($dateFrom) && empty($dateTo)) {

            return view('admin.search.index')->with('error', 'Please enter a search term or date range.');

        }



        $results = [];



        if ($type === 'all' || $type === 'bookings') {

            $results['bookings'] = $this->searchBookings($query, $dateFrom, $dateTo);

        }



        if ($type === 'all' || $type === 'users') {

            $results['users'] = $this->searchUsers($query);

        }



        if ($type === 'all' || $type === 'payments') {

            $results['payments'] = $this->searchPayments($query, $dateFrom, $dateTo);

        }



        if ($type === 'all' || $type === 'feedback') {

            $results['feedback'] = $this->searchFeedback($query);

        }



        return view('admin.search.results', compact('results', 'query', 'type'));

    }



    private function searchBookings($query, $dateFrom, $dateTo)

    {

        $bookings = Booking::with(['teacher.user', 'student.user']);



        if ($query) {

            $bookings->where(function ($q) use ($query) {

                $q->where('id', 'LIKE', "%{$query}%")

                  ->orWhere('status', 'LIKE', "%{$query}%")

                  ->orWhereHas('teacher.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%")

                         ->orWhere('email', 'LIKE', "%{$query}%");

                  })

                  ->orWhereHas('student.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%")

                         ->orWhere('email', 'LIKE', "%{$query}%");

                  });

            });

        }



        if ($dateFrom) {

            $bookings->whereDate('start_time', '>=', $dateFrom);

        }



        if ($dateTo) {

            $bookings->whereDate('start_time', '<=', $dateTo);

        }



        return $bookings->orderBy('id', 'desc')->paginate(10);

    }



    private function searchUsers($query)

    {

        $users = User::with(['teacher', 'student']);



        if ($query) {

            $users->where(function ($q) use ($query) {

                $q->where('name', 'LIKE', "%{$query}%")

                  ->orWhere('email', 'LIKE', "%{$query}%")

                  ->orWhere('role', 'LIKE', "%{$query}%")

                  ->orWhere('phone', 'LIKE', "%{$query}%")

                  // Search teacher-specific fields

                  ->orWhereHas('teacher', function ($q2) use ($query) {

                      $q2->where('bio', 'LIKE', "%{$query}%")

                         ->orWhere('qualifications', 'LIKE', "%{$query}%")

                         ->orWhere('timezone', 'LIKE', "%{$query}%");

                  })

                  // Search for verification status

                  ->orWhereHas('teacher', function ($q2) use ($query) {

                      if (strtolower($query) === 'verified' || strtolower($query) === 'unverified') {

                          $q2->where('is_verified', strtolower($query) === 'verified');

                      }

                  })

                  // Search for availability status

                  ->orWhereHas('teacher', function ($q2) use ($query) {

                      if (strtolower($query) === 'available' || strtolower($query) === 'unavailable') {

                          $q2->where('is_available', strtolower($query) === 'available');

                      }

                  });

            });

        }



        return $users->latest()->paginate(10);

    }



    private function searchPayments($query, $dateFrom, $dateTo)

    {

        $payments = Payment::with(['student.user', 'teacher.user']);



        if ($query) {

            $payments->where(function ($q) use ($query) {

                $q->where('id', 'LIKE', "%{$query}%")

                  ->orWhere('status', 'LIKE', "%{$query}%")

                  ->orWhere('payment_method', 'LIKE', "%{$query}%")

                  ->orWhere('transaction_id', 'LIKE', "%{$query}%")

                  ->orWhereHas('student.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%")

                         ->orWhere('email', 'LIKE', "%{$query}%");

                  })

                  ->orWhereHas('teacher.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%")

                         ->orWhere('email', 'LIKE', "%{$query}%");

                  });

            });

        }



        if ($dateFrom) {

            $payments->whereDate('created_at', '>=', $dateFrom);

        }



        if ($dateTo) {

            $payments->whereDate('created_at', '<=', $dateTo);

        }



        return $payments->latest()->paginate(10);

    }



    private function searchFeedback($query)

    {

        $feedback = Feedback::with(['booking.teacher.user', 'booking.student.user']);



        if ($query) {

            $feedback->where(function ($q) use ($query) {

                $q->where('comment', 'LIKE', "%{$query}%")

                  ->orWhere('type', 'LIKE', "%{$query}%")

                  ->orWhereHas('booking.teacher.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%");

                  })

                  ->orWhereHas('booking.student.user', function ($q2) use ($query) {

                      $q2->where('name', 'LIKE', "%{$query}%");

                  });

            });

        }



        return $feedback->latest()->paginate(10);

    }



    public function advancedSearch(Request $request)

    {

        $filters = $request->all();

        

        $results = [];

        

        // Advanced booking search

        if (isset($filters['search_bookings'])) {

            $results['bookings'] = $this->advancedBookingSearch($filters);

        }

        

        // Advanced user search

        if (isset($filters['search_users'])) {

            $results['users'] = $this->advancedUserSearch($filters);

        }

        

        // Advanced payment search

        if (isset($filters['search_payments'])) {

            $results['payments'] = $this->advancedPaymentSearch($filters);

        }

        

        return view('admin.search.advanced', compact('results', 'filters'));

    }



    private function advancedBookingSearch($filters)

    {

        $query = Booking::with(['teacher.user', 'student.user']);



        if (!empty($filters['booking_status'])) {

            $query->where('status', $filters['booking_status']);

        }



        if (!empty($filters['booking_date_from'])) {

            $query->whereDate('start_time', '>=', $filters['booking_date_from']);

        }



        if (!empty($filters['booking_date_to'])) {

            $query->whereDate('start_time', '<=', $filters['booking_date_to']);

        }





        if (!empty($filters['teacher_name'])) {

            $query->whereHas('teacher.user', function ($q) use ($filters) {

                $q->where('name', 'LIKE', "%{$filters['teacher_name']}%");

            });

        }



        if (!empty($filters['student_name'])) {

            $query->whereHas('student.user', function ($q) use ($filters) {

                $q->where('name', 'LIKE', "%{$filters['student_name']}%");

            });

        }



        return $query->latest()->paginate(15);

    }



    private function advancedUserSearch($filters)

    {

        $query = User::with(['teacher', 'student']);



        if (!empty($filters['user_role'])) {

            $query->where('role', $filters['user_role']);

        }



        if (!empty($filters['user_status'])) {

            if ($filters['user_status'] === 'verified') {

                $query->whereNotNull('email_verified_at');

            } else {

                $query->whereNull('email_verified_at');

            }

        }



        if (!empty($filters['user_date_from'])) {

            $query->whereDate('created_at', '>=', $filters['user_date_from']);

        }



        if (!empty($filters['user_date_to'])) {

            $query->whereDate('created_at', '<=', $filters['user_date_to']);

        }



        if (!empty($filters['user_name'])) {

            $query->where('name', 'LIKE', "%{$filters['user_name']}%");

        }



        if (!empty($filters['user_email'])) {

            $query->where('email', 'LIKE', "%{$filters['user_email']}%");

        }



        // Teacher-specific filters

        if (!empty($filters['teacher_verification'])) {

            $query->whereHas('teacher', function ($q) use ($filters) {

                $q->where('is_verified', $filters['teacher_verification'] === 'verified');

            });

        }



        if (!empty($filters['teacher_availability'])) {

            $query->whereHas('teacher', function ($q) use ($filters) {

                $q->where('is_available', $filters['teacher_availability'] === 'available');

            });

        }





        if (!empty($filters['teacher_qualifications'])) {

            $query->whereHas('teacher', function ($q) use ($filters) {

                $q->where('qualifications', 'LIKE', "%{$filters['teacher_qualifications']}%");

            });

        }



        return $query->latest()->paginate(15);

    }



    private function advancedPaymentSearch($filters)

    {

        $query = Payment::with(['student.user', 'teacher.user']);



        if (!empty($filters['payment_status'])) {

            $query->where('status', $filters['payment_status']);

        }



        if (!empty($filters['payment_method'])) {

            $query->where('payment_method', $filters['payment_method']);

        }



        if (!empty($filters['payment_amount_min'])) {

            $query->where('amount', '>=', $filters['payment_amount_min']);

        }



        if (!empty($filters['payment_amount_max'])) {

            $query->where('amount', '<=', $filters['payment_amount_max']);

        }



        if (!empty($filters['payment_date_from'])) {

            $query->whereDate('created_at', '>=', $filters['payment_date_from']);

        }



        if (!empty($filters['payment_date_to'])) {

            $query->whereDate('created_at', '<=', $filters['payment_date_to']);

        }



        return $query->latest()->paginate(15);

    }

}

