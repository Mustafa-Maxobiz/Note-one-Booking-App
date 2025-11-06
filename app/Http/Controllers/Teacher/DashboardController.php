<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            // Redirect to profile setup or show error
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        $stats = [
            'total_bookings' => Booking::where('teacher_id', $teacher->id)->count(),
            'pending_bookings' => Booking::where('teacher_id', $teacher->id)->where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('teacher_id', $teacher->id)->where('status', 'completed')->count(),
            'total_earnings' => Payment::where('teacher_id', $teacher->id)->where('status', 'completed')->sum('amount'),
            'upcoming_bookings' => Booking::where('teacher_id', $teacher->id)
                ->where('start_time', '>', now())
                ->where('status', 'confirmed')
                ->count(),
        ];

        $upcoming_bookings = Booking::with(['student.user'])
            ->where('teacher_id', $teacher->id)
            ->where('start_time', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $recent_bookings = Booking::with(['student.user'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'upcoming_bookings', 'recent_bookings'));
    }
}
