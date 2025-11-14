<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAvailability;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        $availabilities = TeacherAvailability::where('teacher_id', $teacher->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('teacher.availability.index', compact('availabilities'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        return view('teacher.availability.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        // Check for conflicts
        $conflict = TeacherAvailability::where('teacher_id', $teacher->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                          ->where('end_time', '>=', $validated['end_time']);
                    });
            })->first();

        if ($conflict) {
            return back()->withErrors(['time' => 'This time slot conflicts with existing availability.']);
        }

        TeacherAvailability::create([
            'teacher_id' => $teacher->id,
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('teacher.availability.index')->with('success', 'Availability added successfully!');
    }

    public function destroy(TeacherAvailability $availability)
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        if ($availability->teacher_id != $teacher->id) {
            abort(403, 'Unauthorized access.');
        }

        $availability->delete();
        return redirect()->route('teacher.availability.index')->with('success', 'Availability removed successfully!');
    }
}
