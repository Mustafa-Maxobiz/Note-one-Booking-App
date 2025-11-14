<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        // Check if student profile exists
        if (!$student) {
            // Redirect to profile setup or show error
            return redirect()->route('student.profile.index')->with('error', 'Please complete your student profile first.');
        }
        
        $stats = [
            'total_bookings' => Booking::where('student_id', $student->id)->count(),
            'completed_bookings' => Booking::where('student_id', $student->id)->where('status', 'completed')->count(),
            'upcoming_bookings' => Booking::where('student_id', $student->id)
                ->where('start_time', '>', now())
                ->where('status', 'confirmed')
                ->count(),
            'total_spent' => Payment::where('student_id', $student->id)->where('status', 'completed')->sum('amount'),
        ];

        $upcoming_bookings = Booking::with(['teacher.user'])
            ->where('student_id', $student->id)
            ->where('start_time', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $recent_bookings = Booking::with(['teacher.user'])
            ->where('student_id', $student->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('stats', 'upcoming_bookings', 'recent_bookings'));
    }
}
