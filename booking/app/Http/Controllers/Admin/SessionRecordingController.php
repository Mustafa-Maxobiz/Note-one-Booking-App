<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionRecording;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SessionRecordingController extends Controller
{
    public function index(Request $request)
    {
        // Get all recordings grouped by meeting
        $allRecordings = SessionRecording::with(['booking.teacher.user', 'booking.student.user'])
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
                    'formatted_file_size' => $recording->formatted_file_size,
                    'play_url' => $recording->play_url,
                    'download_url' => $recording->download_url,
                ];
            })->toArray();
            
            $primaryRecordings->push($primaryRecording);
        }

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $primaryRecordings = $primaryRecordings->filter(function($recording) use ($search) {
                return str_contains(strtolower($recording->file_name), strtolower($search)) ||
                       str_contains(strtolower($recording->recording_type), strtolower($search)) ||
                       str_contains(strtolower($recording->zoom_meeting_id), strtolower($search)) ||
                       ($recording->booking && $recording->booking->teacher && str_contains(strtolower($recording->booking->teacher->user->name), strtolower($search))) ||
                       ($recording->booking && $recording->booking->student && str_contains(strtolower($recording->booking->student->user->name), strtolower($search)));
            });
        }

        // Filter by recording type
        if ($request->filled('type')) {
            $primaryRecordings = $primaryRecordings->where('recording_type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $primaryRecordings = $primaryRecordings->filter(function($recording) use ($request) {
                return $recording->recording_start && $recording->recording_start->format('Y-m-d') >= $request->date_from;
            });
        }
        if ($request->filled('date_to')) {
            $primaryRecordings = $primaryRecordings->filter(function($recording) use ($request) {
                return $recording->recording_start && $recording->recording_start->format('Y-m-d') <= $request->date_to;
            });
        }

        // Sort by ID descending and paginate
        $recordings = $primaryRecordings->sortByDesc('id')->values();
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedRecordings = $recordings->slice($offset, $perPage);
        
        // Create paginator manually
        $recordings = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedRecordings,
            $recordings->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        return view('admin.session-recordings.index', compact('recordings'));
    }

    public function show(SessionRecording $session_recording)
    {
        // Load relationships with null checks
        $session_recording->load(['session.teacher.user', 'session.student.user']);
        
        // Check if session exists, if not, redirect with error
        if (!$session_recording->session) {
            return redirect()->route('admin.session-recordings.index')
                ->with('error', 'Session recording found but associated session is missing.');
        }
        
        return view('admin.session-recordings.show', compact('session_recording'));
    }

    public function edit(SessionRecording $session_recording)
    {
        $session_recording->load(['session.teacher.user', 'session.student.user']);
        return view('admin.session-recordings.edit', compact('session_recording'));
    }

    public function update(Request $request, SessionRecording $session_recording)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'recording_type' => 'required|in:video,audio,chat,transcript',
            'duration_seconds' => 'required|integer|min:0',
            'file_size_bytes' => 'required|integer|min:0',
            'play_url' => 'required|url',
            'download_url' => 'required|url',
            'zoom_meeting_id' => 'nullable|string|max:255',
            'zoom_recording_id' => 'nullable|string|max:255',
        ]);

        $session_recording->update($request->all());

        return redirect()->route('admin.session-recordings.show', $session_recording)
            ->with('success', 'Recording updated successfully.');
    }

    public function destroy(SessionRecording $session_recording)
    {
        $session_recording->delete();
        return redirect()->route('admin.session-recordings.index')
            ->with('success', 'Recording deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'recording_ids' => 'required|array|min:1',
            'recording_ids.*' => 'exists:session_recordings,id'
        ]);

        $deletedCount = SessionRecording::whereIn('id', $request->recording_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} recording(s) deleted successfully.",
            'deleted_count' => $deletedCount
        ]);
    }

    public function fetchRecordings(Request $request)
    {
        try {
            Log::info('Manual recording fetch initiated via admin interface');
            
            // Run the zoom:fetch-recordings command
            $exitCode = Artisan::call('zoom:fetch-recordings', ['--force' => true]);
            
            if ($exitCode === 0) {
                $output = Artisan::output();
                Log::info('Recording fetch completed successfully', ['output' => $output]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Recordings fetched successfully!',
                    'output' => $output
                ]);
            } else {
                Log::error('Recording fetch failed', ['exit_code' => $exitCode]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch recordings. Please check the logs.',
                    'exit_code' => $exitCode
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Error in manual recording fetch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching recordings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMeetingFiles($meetingId)
    {
        try {
            // Log the request
            \Log::info('Getting meeting files for: ' . $meetingId);
            
            $files = SessionRecording::where('zoom_meeting_id', $meetingId)
                ->orderBy('recording_type')
                ->get()
                ->map(function($recording) {
                    return [
                        'id' => $recording->id,
                        'recording_type' => $recording->recording_type,
                        'file_name' => $recording->file_name,
                        'formatted_file_size' => $recording->formatted_file_size,
                        'play_url' => $recording->play_url,
                        'download_url' => $recording->download_url,
                    ];
                });

            \Log::info('Found ' . $files->count() . ' files for meeting ' . $meetingId);

            return response()->json([
                'success' => true,
                'files' => $files
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getMeetingFiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading files: ' . $e->getMessage()
            ], 500);
        }
    }
}
