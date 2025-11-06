<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SessionRecording;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionRecordingController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        // Get all recordings grouped by meeting
        $allRecordings = SessionRecording::whereHas('booking', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with(['booking.student.user', 'booking.teacher.user'])
        ->orderBy('zoom_meeting_id')
        ->orderBy('recording_type')
        ->get();

        // Group recordings by meeting ID and select the primary recording for each meeting
        $groupedRecordings = $allRecordings->groupBy('zoom_meeting_id');
        $primaryRecordings = collect();

        foreach ($groupedRecordings as $meetingId => $meetingRecordings) {
            // Priority order: screen/video > audio > transcript > others
            $primaryRecording = $meetingRecordings->sortBy(function($recording) {
                if (str_contains($recording->recording_type, 'screen') || str_contains($recording->recording_type, 'video')) {
                    return 1;
                } elseif (str_contains($recording->recording_type, 'audio') && !str_contains($recording->recording_type, 'transcript')) {
                    return 2;
                } elseif (str_contains($recording->recording_type, 'transcript')) {
                    return 3;
                } else {
                    return 4;
                }
            })->first();

            // Add additional files count and notes
            $primaryRecording->additional_files_count = $meetingRecordings->count() - 1;
            $primaryRecording->all_recordings = $meetingRecordings;
            $primaryRecording->has_transcript = $meetingRecordings->where('recording_type', 'like', '%transcript%')->count() > 0;
            $primaryRecording->has_audio = $meetingRecordings->where('recording_type', 'like', '%audio%')->where('recording_type', 'not like', '%transcript%')->count() > 0;
            
            // Add all recordings data for JavaScript
            $primaryRecording->all_recordings_data = $meetingRecordings->map(function($recording) {
                return [
                    'id' => $recording->id,
                    'recording_type' => $recording->recording_type,
                    'file_name' => $recording->file_name,
                    'play_url' => $recording->play_url,
                    'download_url' => $recording->download_url,
                    'duration' => $recording->formatted_duration,
                    'file_size' => $recording->file_size,
                    'formatted_file_size' => $recording->formatted_file_size,
                    'recording_id' => $recording->recording_id,
                ];
            })->toArray();

            $primaryRecordings->push($primaryRecording);
        }

        // Convert to paginated collection
        $recordings = $primaryRecordings->sortByDesc('id')->values();
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $recordings->slice($offset, $perPage)->values();
        
        $recordings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $recordings->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('teacher.session-recordings.index', compact('recordings'));
    }

    public function show(SessionRecording $recording)
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        // Ensure the recording belongs to a booking by this teacher
        if ($recording->booking->teacher_id != $teacher->id) {
            abort(403, 'Unauthorized access to this recording.');
        }

        $recording->load(['booking.student.user', 'booking.teacher.user']);
        
        return view('teacher.session-recordings.show', compact('recording'));
    }

    public function bySession(Booking $booking)
    {
        $teacher = Auth::user()->teacher;
        
        // Check if teacher profile exists
        if (!$teacher) {
            return redirect()->route('teacher.profile.index')->with('error', 'Please complete your teacher profile first.');
        }
        
        // Ensure the session belongs to this teacher
        if ($booking->teacher_id != $teacher->id) {
            abort(403, 'Unauthorized access to this session.');
        }

        $recordings = $booking->sessionRecordings()->orderBy('id', 'desc')->get();
        $booking->load(['student.user', 'teacher.user']);
        
        return view('teacher.session-recordings.by-session', compact('booking', 'recordings'));
    }
}
