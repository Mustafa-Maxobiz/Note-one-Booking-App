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

use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;



class ReportController extends Controller

{

    public function index()

    {

        // Cache key for dashboard data

        $cacheKey = 'admin_dashboard_data_' . date('Y-m-d-H');

        

        // Try to get cached data first

        $cachedData = Cache::get($cacheKey);

        

        if ($cachedData) {

            return view('admin.reports.index', $cachedData);

        }



        // Dashboard Overview - Use single query for basic counts

        $basicStats = $this->getBasicStats();



        // Monthly Statistics

        $monthlyStats = $this->getMonthlyStats();

        

        // Weekly Statistics

        $weeklyStats = $this->getWeeklyStats();

        

        // Daily Statistics

        $dailyStats = $this->getDailyStats();

        
        
        // Top Performing Teachers

        $topTeachers = $this->getTopTeachers();

        

        // Teacher Utilization Rates

        $teacherUtilization = $this->getTeacherUtilizationRates();

        

        // Teacher Acceptance Rates

        $teacherAcceptance = $this->getTeacherAcceptanceRates();

        
        
        // Popular Subjects

        $popularSubjects = $this->getPopularSubjects();

        
        
        // Revenue Trends

        $revenueTrends = $this->getRevenueTrends();

        
        
        // Booking Status Distribution

        $bookingStatusDistribution = $this->getBookingStatusDistribution();



        $data = compact(

            'basicStats',

            'monthlyStats',

            'weeklyStats',

            'dailyStats',

            'topTeachers',

            'teacherUtilization',

            'teacherAcceptance',

            'popularSubjects',

            'revenueTrends',

            'bookingStatusDistribution'

        );



        // Cache the data for 1 hour

        Cache::put($cacheKey, $data, 3600);



        return view('admin.reports.index', $data);

    }



    public function revenue()

    {

        $revenueData = $this->getDetailedRevenueData();

        $monthlyRevenue = $this->getMonthlyRevenue();

        $teacherRevenue = $this->getTeacherRevenue();

        
        
        return view('admin.reports.revenue', compact('revenueData', 'monthlyRevenue', 'teacherRevenue'));

    }



    public function teachers()

    {

        $teacherPerformance = $this->getTeacherPerformance();

        $teacherRatings = $this->getTeacherRatings();

        $teacherAvailability = $this->getTeacherAvailability();
        
        $teacherUtilization = $this->getTeacherUtilizationRates();

        $teacherAcceptance = $this->getTeacherAcceptanceRates();

        $teacherResponseTimes = $this->getTeacherResponseTimes();

        

        return view('admin.reports.teachers', compact(

            'teacherPerformance', 

            'teacherRatings', 

            'teacherAvailability',

            'teacherUtilization',

            'teacherAcceptance',

            'teacherResponseTimes'

        ));

    }



    public function users()

    {

        $userGrowth = $this->getUserGrowth();

        $userActivity = $this->getUserActivity();

        $userEngagement = $this->getUserEngagement();

        
        
        return view('admin.reports.users', compact('userGrowth', 'userActivity', 'userEngagement'));

    }



    public function students()

    {

        $studentActivity = $this->getStudentActivity();

        $studentProgress = $this->getStudentProgress();

        $studentEngagement = $this->getStudentEngagement();

        
        
        return view('admin.reports.students', compact('studentActivity', 'studentProgress', 'studentEngagement'));

    }



    public function bookings()

    {

        $bookingTrends = $this->getBookingTrends();

        $bookingCompletion = $this->getBookingCompletion();

        $bookingFeedback = $this->getBookingFeedback();
        
        $dailyStats = $this->getDailyStats();

        $weeklyStats = $this->getWeeklyStats();

        

        return view('admin.reports.bookings', compact(

            'bookingTrends', 

            'bookingCompletion', 

            'bookingFeedback',

            'dailyStats',

            'weeklyStats'

        ));

    }



    public function export(Request $request)

    {

        $type = $request->get('type', 'revenue');

        $format = $request->get('format', 'pdf');

        
        
        switch ($type) {

            case 'revenue':

                $data = $this->getDetailedRevenueData();

                break;

            case 'teachers':

                $data = $this->getTeacherPerformance();

                break;

            case 'students':

                $data = $this->getStudentActivity();

                break;

            case 'bookings':

                $data = $this->getBookingTrends();

                break;

            default:

                $data = [];

        }

        
        
        if ($format === 'excel') {

            return $this->exportToExcel($data, $type);

        } else {

            return $this->exportToPdf($data, $type);

        }

    }



    /**

     * Clear all report caches

     */

    public function clearCache()

    {

        $cacheKeys = [

            'admin_dashboard_data_' . date('Y-m-d-H'),

            'basic_stats_' . date('Y-m-d-H'),

            'monthly_stats_' . date('Y-m'),

            'weekly_stats_' . date('Y'),

            'daily_stats_' . date('Y-m-d'),

            'top_teachers_' . date('Y-m-d'),

            'teacher_utilization_' . date('Y-m-d'),

            'teacher_acceptance_' . date('Y-m-d'),

            'teacher_response_times_' . date('Y-m-d'),

        ];



        foreach ($cacheKeys as $key) {

            Cache::forget($key);

        }



        return response()->json(['message' => 'Report caches cleared successfully']);

    }



    private function getBasicStats()

    {

        $cacheKey = 'basic_stats_' . date('Y-m-d-H');

        

        return Cache::remember($cacheKey, 3600, function () {

            // Single query to get all basic statistics

            $userStats = DB::select("

                SELECT 

                    (SELECT COUNT(*) FROM users) as total_users,

                    (SELECT COUNT(*) FROM teachers) as total_teachers,

                    (SELECT COUNT(*) FROM students) as total_students,

                    (SELECT COUNT(*) FROM bookings) as total_bookings,

                    (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed') as total_revenue,

                    (SELECT COALESCE(AVG(rating), 0) FROM feedback) as average_rating

            ")[0];

            

            return (object) [

                'totalUsers' => $userStats->total_users,

                'totalTeachers' => $userStats->total_teachers,

                'totalStudents' => $userStats->total_students,

                'totalBookings' => $userStats->total_bookings,

                'totalRevenue' => $userStats->total_revenue,

                'averageRating' => $userStats->average_rating

            ];

        });

    }



    private function getMonthlyStats()

    {

        $cacheKey = 'monthly_stats_' . date('Y-m');

        

        return Cache::remember($cacheKey, 3600, function () {

        return Booking::selectRaw('

            MONTH(created_at) as month,

            YEAR(created_at) as year,

            COUNT(*) as total_bookings,

            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings,

            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings

        ')

        ->whereYear('created_at', date('Y'))

        ->groupBy('year', 'month')

        ->orderBy('year')

        ->orderBy('month')

        ->get();

        });

    }



    private function getTopTeachers()

    {

        $cacheKey = 'top_teachers_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

        return Teacher::with('user')

            ->withCount(['sessions as total_bookings' => function($query) {

                $query->where('status', 'completed');

            }])

            ->withAvg('feedback', 'rating')

            ->orderByDesc('total_bookings')

            ->limit(10)

            ->get();

        });

    }



    private function getPopularSubjects()

    {

        return DB::table('bookings')

            ->selectRaw('"General Session" as name, COUNT(*) as booking_count')

            ->groupBy('name')

            ->orderByDesc('booking_count')

            ->limit(10)

            ->get();

    }



    private function getRevenueTrends()

    {

        return Payment::selectRaw('

            DATE(created_at) as date,

            SUM(amount) as daily_revenue,

            COUNT(*) as transaction_count

        ')

        ->where('status', 'completed')

        ->whereDate('created_at', '>=', now()->subDays(30))

        ->groupBy('date')

        ->orderBy('date')

        ->get();

    }



    private function getBookingStatusDistribution()

    {

        return Booking::selectRaw('status, COUNT(*) as count')

            ->groupBy('status')

            ->get();

    }



    private function getDetailedRevenueData()

    {

        return Payment::with(['booking.teacher.user', 'booking.student.user'])

            ->where('status', 'completed')

            ->orderBy('id', 'desc')

            ->paginate(20);

    }



    private function getMonthlyRevenue()

    {

        return Payment::selectRaw('

            YEAR(created_at) as year,

            MONTH(created_at) as month,

            SUM(amount) as revenue,

            COUNT(*) as transactions

        ')

        ->where('status', 'completed')

        ->groupBy('year', 'month')

        ->orderBy('year', 'desc')

        ->orderBy('month', 'desc')

        ->get();

    }



    private function getTeacherRevenue()

    {

        return Teacher::with('user')

            ->withSum(['payments as total_revenue' => function($query) {

                $query->where('status', 'completed');

            }], 'amount')

            ->withCount(['sessions as total_bookings' => function($query) {

                $query->where('status', 'completed');

            }])

            ->orderByDesc('total_revenue')

            ->get();

    }



    private function getTeacherPerformance()

    {

        return Teacher::with('user')

            ->withCount(['sessions as total_bookings'])

            ->withCount(['sessions as completed_bookings' => function($query) {

                $query->where('status', 'completed');

            }])

            ->withAvg('feedback', 'rating')

            ->withSum('payments', 'amount')

            ->get()

            ->map(function($teacher) {

                $teacher->completion_rate = $teacher->total_bookings > 0 

                    ? round(($teacher->completed_bookings / $teacher->total_bookings) * 100, 2)

                    : 0;

                return $teacher;

            });

    }



    private function getTeacherRatings()

    {

        return Feedback::with(['booking.teacher.user'])

            ->selectRaw('teacher_id, AVG(rating) as avg_rating, COUNT(*) as total_feedback')

            ->groupBy('teacher_id')

            ->orderByDesc('avg_rating')

            ->get();

    }



    private function getTeacherAvailability()

    {

        return DB::table('teacher_availabilities')

            ->join('teachers', 'teacher_availabilities.teacher_id', '=', 'teachers.id')

            ->join('users', 'teachers.user_id', '=', 'users.id')

            ->selectRaw('users.name, COUNT(*) as available_slots')

            ->where('teacher_availabilities.is_available', true)

            ->groupBy('teachers.id', 'users.name')

            ->orderByDesc('available_slots')

            ->get();

    }



    private function getTeacherUtilizationRates()

    {

        $cacheKey = 'teacher_utilization_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

            // Optimized query with joins to reduce database calls

            $teachers = DB::table('teachers')

                ->join('users', 'teachers.user_id', '=', 'users.id')

                ->leftJoin('bookings', function($join) {

                    $join->on('teachers.id', '=', 'bookings.teacher_id')

                         ->where('bookings.status', '=', 'completed');

                })

                ->leftJoin('teacher_availabilities', function($join) {

                    $join->on('teachers.id', '=', 'teacher_availabilities.teacher_id')

                         ->where('teacher_availabilities.is_available', '=', true);

                })

                ->selectRaw('

                    teachers.id,

                    users.name,

                    COUNT(DISTINCT bookings.id) as total_bookings,

                    COALESCE(SUM(bookings.duration_minutes), 0) as total_booked_minutes,

                    COALESCE(SUM(TIMESTAMPDIFF(MINUTE, teacher_availabilities.start_time, teacher_availabilities.end_time)), 0) as total_available_minutes

                ')

                ->groupBy('teachers.id', 'users.name')

                ->get()

                ->map(function($teacher) {

                    $totalBookedHours = $teacher->total_booked_minutes / 60;

                    $totalAvailableHours = $teacher->total_available_minutes / 60;

                    

                    $teacher->total_available_hours = round($totalAvailableHours, 2);

                    $teacher->total_booked_hours = round($totalBookedHours, 2);

                    $teacher->utilization_rate = $totalAvailableHours > 0 

                        ? round(($totalBookedHours / $totalAvailableHours) * 100, 2)

                        : 0;

                    

                    return $teacher;

                })

                ->sortByDesc('utilization_rate');

            

            // Convert to collection of Teacher models for consistency

            return $teachers->map(function($teacherData) {

                $teacher = Teacher::find($teacherData->id);

                $teacher->user = (object) ['name' => $teacherData->name];

                $teacher->total_bookings = $teacherData->total_bookings;

                $teacher->total_available_hours = $teacherData->total_available_hours;

                $teacher->total_booked_hours = $teacherData->total_booked_hours;

                $teacher->utilization_rate = $teacherData->utilization_rate;

                return $teacher;

            });

        });

    }



    private function getTeacherAcceptanceRates()

    {

        $cacheKey = 'teacher_acceptance_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

            // Optimized query with single database call

            $teachers = DB::table('teachers')

                ->join('users', 'teachers.user_id', '=', 'users.id')

                ->leftJoin('bookings', 'teachers.id', '=', 'bookings.teacher_id')

                ->selectRaw('

                    teachers.id,

                    users.name,

                    COUNT(bookings.id) as total_requests,

                    SUM(CASE WHEN bookings.status IN ("confirmed", "completed") THEN 1 ELSE 0 END) as accepted_bookings,

                    SUM(CASE WHEN bookings.status = "cancelled" THEN 1 ELSE 0 END) as declined_bookings

                ')

                ->groupBy('teachers.id', 'users.name')

                ->get()

                ->map(function($teacher) {

                    $totalRequests = $teacher->total_requests;

                    $accepted = $teacher->accepted_bookings;

                    $declined = $teacher->declined_bookings;

                    

                    $teacher->acceptance_rate = $totalRequests > 0 

                        ? round(($accepted / $totalRequests) * 100, 2)

                        : 0;

                    

                    $teacher->decline_rate = $totalRequests > 0 

                        ? round(($declined / $totalRequests) * 100, 2)

                        : 0;

                    

                    return $teacher;

                })

                ->sortByDesc('acceptance_rate');

            

            // Convert to collection of Teacher models for consistency

            return $teachers->map(function($teacherData) {

                $teacher = Teacher::find($teacherData->id);

                $teacher->user = (object) ['name' => $teacherData->name];

                $teacher->total_requests = $teacherData->total_requests;

                $teacher->accepted_bookings = $teacherData->accepted_bookings;

                $teacher->declined_bookings = $teacherData->declined_bookings;

                $teacher->acceptance_rate = $teacherData->acceptance_rate;

                $teacher->decline_rate = $teacherData->decline_rate;

                return $teacher;

            });

        });

    }



    private function getTeacherResponseTimes()

    {

        $cacheKey = 'teacher_response_times_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

            return DB::table('bookings')

                ->join('teachers', 'bookings.teacher_id', '=', 'teachers.id')

                ->join('users', 'teachers.user_id', '=', 'users.id')

                ->selectRaw('

                    teachers.id,

                    users.name,

                    AVG(TIMESTAMPDIFF(MINUTE, bookings.created_at, bookings.updated_at)) as avg_response_time_minutes,

                    COUNT(*) as total_responses

                ')

                ->whereIn('bookings.status', ['confirmed', 'cancelled'])

                ->groupBy('teachers.id', 'users.name')

                ->orderBy('avg_response_time_minutes')

                ->get()

                ->map(function($teacher) {

                    $teacher->avg_response_time_hours = round($teacher->avg_response_time_minutes / 60, 2);

                    return $teacher;

                });

        });

    }



    private function getWeeklyStats()

    {

        $cacheKey = 'weekly_stats_' . date('Y');

        

        return Cache::remember($cacheKey, 3600, function () {

            return Booking::selectRaw('

                YEAR(created_at) as year,

                WEEK(created_at) as week,

                COUNT(*) as total_bookings,

                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings,

                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,

                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings

            ')

            ->whereYear('created_at', date('Y'))

            ->groupBy('year', 'week')

            ->orderBy('year')

            ->orderBy('week')

            ->get();

        });

    }



    private function getDailyStats()

    {

        $cacheKey = 'daily_stats_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 1800, function () { // Cache for 30 minutes

            return Booking::selectRaw('

                DATE(created_at) as date,

                COUNT(*) as total_bookings,

                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings,

                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings,

                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings,

                AVG(duration_minutes) as avg_duration_minutes

            ')

            ->whereDate('created_at', '>=', now()->subDays(30))

            ->groupBy('date')

            ->orderBy('date')

            ->get();

        });

    }



    private function getStudentActivity()

    {

        return Student::with('user')

            ->withCount(['sessions as total_bookings'])

            ->withCount(['sessions as completed_bookings' => function($query) {

                $query->where('status', 'completed');

            }])

            ->withSum('payments', 'amount')

            ->orderByDesc('total_bookings')

            ->get();

    }



    private function getStudentProgress()

    {

        return Student::with('user')

            ->withCount(['sessions as total_bookings' => function($query) {

                $query->where('status', 'completed');

            }])

            ->withAvg(['feedback as avg_rating' => function($query) {

                $query->where('type', 'teacher_to_student');

            }], 'rating')

            ->get();

    }



    private function getStudentEngagement()

    {

        return DB::table('bookings')

            ->join('students', 'bookings.student_id', '=', 'students.id')

            ->join('users', 'students.user_id', '=', 'users.id')

            ->selectRaw('

                users.name,

                COUNT(*) as total_bookings,

                AVG(bookings.duration_minutes) as avg_duration,

                SUM(CASE WHEN bookings.status = "completed" THEN 1 ELSE 0 END) as completed_bookings

            ')

            ->groupBy('students.id', 'users.name')

            ->orderByDesc('total_bookings')

            ->get();

    }



    private function getBookingTrends()

    {

        $cacheKey = 'booking_trends_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 1800, function () {

        return Booking::selectRaw('

            DATE(created_at) as date,

            COUNT(*) as total_bookings,

            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,

            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled

        ')

        ->whereDate('created_at', '>=', now()->subDays(30))

        ->groupBy('date')

        ->orderBy('date')

        ->get();

        });

    }



    private function getBookingCompletion()

    {

        $cacheKey = 'booking_completion_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

        return Booking::selectRaw('

            status,

            COUNT(*) as count,

            AVG(duration_minutes) as avg_duration

        ')

        ->groupBy('status')

        ->get();

        });

    }



    private function getBookingFeedback()

    {

        $cacheKey = 'booking_feedback_' . date('Y-m-d');

        

        return Cache::remember($cacheKey, 3600, function () {

        return Feedback::with(['booking'])

            ->selectRaw('

                booking_id,

                AVG(rating) as avg_rating,

                COUNT(*) as feedback_count

            ')

            ->groupBy('booking_id')

            ->orderByDesc('avg_rating')

            ->limit(20)

            ->get();

        });

    }



    private function exportToExcel($data, $type)

    {

        // Implementation for Excel export

        return response()->json(['message' => 'Excel export not implemented yet']);

    }



    private function exportToPdf($data, $type)

    {

        // Implementation for PDF export

        return response()->json(['message' => 'PDF export not implemented yet']);

    }



    private function getUserGrowth()

    {

        return User::selectRaw('

            DATE(created_at) as date,

            COUNT(*) as new_users,

            SUM(CASE WHEN role = "teacher" THEN 1 ELSE 0 END) as new_teachers,

            SUM(CASE WHEN role = "student" THEN 1 ELSE 0 END) as new_students

        ')

        ->whereDate('created_at', '>=', now()->subDays(30))

        ->groupBy('date')

        ->orderBy('date')

        ->get();

    }



    private function getUserActivity()

    {

        return User::withCount(['sessions as total_sessions'])

            ->withCount(['sessions as recent_sessions' => function($query) {

                $query->where('created_at', '>=', now()->subDays(7));

            }])

            ->orderByDesc('recent_sessions')

            ->limit(20)

            ->get();

    }



    private function getUserEngagement()

    {

        return DB::table('users')

            ->leftJoin('bookings', 'users.id', '=', 'sessions.teacher_id')

            ->leftJoin('sessions as student_sessions', 'users.id', '=', 'student_sessions.student_id')

            ->selectRaw('

                users.name,

                users.role,

                COUNT(DISTINCT sessions.id) as teacher_sessions,

                COUNT(DISTINCT student_sessions.id) as student_sessions,

                users.created_at

            ')

            ->groupBy('users.id', 'users.name', 'users.role', 'users.created_at')

            ->orderByDesc('teacher_sessions')

            ->orderByDesc('student_sessions')

            ->limit(20)

            ->get();

    }

}

