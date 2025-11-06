<?php



namespace App\Http\Controllers\Student;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\SessionRecording;

use App\Models\Booking;

use Illuminate\Support\Facades\Auth;



class RecordingController extends Controller

{

    public function index()

    {

        $student = Auth::user()->student;

        

        // Get all recordings for sessions where this student was enrolled

        $recordings = SessionRecording::whereHas('booking', function($query) use ($student) {

            $query->where('student_id', $student->id);

        })->with(['session.teacher.user'])

        ->orderBy('id', 'desc')

        ->paginate(10);

        

        return view('student.recordings.index', compact('recordings'));

    }

    

    public function show($id)

    {

        $student = Auth::user()->student;

        

        $recording = SessionRecording::whereHas('booking', function($query) use ($student) {

            $query->where('student_id', $student->id);

        })->with(['session.teacher.user'])

        ->findOrFail($id);

        

        return view('student.recordings.show', compact('recording'));

    }

    

    public function download($id)

    {

        $student = Auth::user()->student;

        

        $recording = SessionRecording::whereHas('booking', function($query) use ($student) {

            $query->where('student_id', $student->id);

        })->findOrFail($id);

        

        if ($recording->download_url) {

            return redirect($recording->download_url);

        }

        

        return back()->with('error', 'Download link not available for this recording.');

    }

    

    public function play($id)

    {

        $student = Auth::user()->student;

        

        $recording = SessionRecording::whereHas('booking', function($query) use ($student) {

            $query->where('student_id', $student->id);

        })->findOrFail($id);

        

        if ($recording->play_url) {

            return redirect($recording->play_url);

        }

        

        return back()->with('error', 'Play link not available for this recording.');

    }

}

