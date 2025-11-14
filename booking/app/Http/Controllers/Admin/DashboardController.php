<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Teacher;
use App\Models\Student;
use App\Services\ZoomService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Current month stats
        $stats = [
            'total_users' => User::count(),
            'total_teachers' => Teacher::count(),
            'total_students' => Student::count(),
            'total_sessions' => Booking::count(),
            'pending_sessions' => Booking::where('status', 'pending')->count(),
            'completed_sessions' => Booking::where('status', 'completed')->count(),
            'cancelled_sessions' => Booking::where('status', 'cancelled')->count(),
            'confirmed_sessions' => Booking::where('status', 'confirmed')->count(),
            'total_revenue' => \App\Models\Payment::where('status', 'completed')->sum('amount'),
            'this_month_sessions' => Booking::whereMonth('created_at', now()->month)->count(),
            'this_month_revenue' => \App\Models\Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        // Calculate growth percentages (current month vs last month)
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $currentMonthStart = now()->startOfMonth();

        // Last month stats
        $lastMonth = [
            'users' => User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'teachers' => Teacher::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'students' => Student::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'sessions' => Booking::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'pending' => Booking::where('status', 'pending')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'completed' => Booking::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'cancelled' => Booking::where('status', 'cancelled')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count(),
            'revenue' => \App\Models\Payment::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('amount'),
        ];

        // Current month (so far) stats
        $thisMonth = [
            'users' => User::where('created_at', '>=', $currentMonthStart)->count(),
            'teachers' => Teacher::where('created_at', '>=', $currentMonthStart)->count(),
            'students' => Student::where('created_at', '>=', $currentMonthStart)->count(),
            'sessions' => Booking::where('created_at', '>=', $currentMonthStart)->count(),
            'pending' => Booking::where('status', 'pending')
                ->where('created_at', '>=', $currentMonthStart)->count(),
            'completed' => Booking::where('status', 'completed')
                ->where('created_at', '>=', $currentMonthStart)->count(),
            'cancelled' => Booking::where('status', 'cancelled')
                ->where('created_at', '>=', $currentMonthStart)->count(),
            'revenue' => $stats['this_month_revenue'],
        ];

        // Calculate percentage changes
        $growth = [
            'users' => $this->calculateGrowth($lastMonth['users'], $thisMonth['users']),
            'teachers' => $this->calculateGrowth($lastMonth['teachers'], $thisMonth['teachers']),
            'students' => $this->calculateGrowth($lastMonth['students'], $thisMonth['students']),
            'sessions' => $this->calculateGrowth($lastMonth['sessions'], $thisMonth['sessions']),
            'pending' => $this->calculateGrowth($lastMonth['pending'], $thisMonth['pending']),
            'completed' => $this->calculateGrowth($lastMonth['completed'], $thisMonth['completed']),
            'cancelled' => $this->calculateGrowth($lastMonth['cancelled'], $thisMonth['cancelled']),
            'revenue' => $this->calculateGrowth($lastMonth['revenue'], $thisMonth['revenue']),
        ];

        // System status checks
        $systemStatus = $this->checkSystemStatus();

        // Recent sessions
        $recent_sessions = Booking::with(['teacher.user', 'student.user'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'growth', 'systemStatus', 'recent_sessions'));
    }

    /**
     * Calculate percentage growth
     */
    private function calculateGrowth($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        $change = (($newValue - $oldValue) / $oldValue) * 100;
        return round($change, 1);
    }

    /**
     * Check system status for all integrations
     */
    private function checkSystemStatus()
    {
        $status = [
            'platform' => ['status' => 'online', 'message' => 'Online'],
            'database' => ['status' => 'online', 'message' => 'Connected'],
            'email' => ['status' => 'online', 'message' => 'Active'],
            'zoom' => ['status' => 'online', 'message' => 'Connected'],
        ];

        // Check Database Connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $status['database'] = ['status' => 'error', 'message' => 'Connection Failed'];
            Log::error('Dashboard: Database connection failed', ['error' => $e->getMessage()]);
        }

        // Check Email Service
        try {
            $emailConfigured = config('mail.mailers.smtp.host') !== null;
            if (!$emailConfigured) {
                $status['email'] = ['status' => 'warning', 'message' => 'Not Configured'];
            }
        } catch (\Exception $e) {
            $status['email'] = ['status' => 'error', 'message' => 'Configuration Error'];
            Log::error('Dashboard: Email service check failed', ['error' => $e->getMessage()]);
        }

        // Check Zoom Integration
        try {
            $zoomService = new ZoomService();
            if (!$zoomService->isConfigured()) {
                $status['zoom'] = ['status' => 'warning', 'message' => 'Not Configured'];
            } else {
                // Try to get access token
                $token = $zoomService->getAccessToken();
                if (!$token) {
                    $status['zoom'] = ['status' => 'error', 'message' => 'Authentication Failed'];
                }
            }
        } catch (\Exception $e) {
            $status['zoom'] = ['status' => 'error', 'message' => 'Connection Error'];
            Log::error('Dashboard: Zoom integration check failed', ['error' => $e->getMessage()]);
        }

        return $status;
    }
}
