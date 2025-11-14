<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\SoftHardDelete;

use Carbon\Carbon;



class Booking extends Model

{

    use HasFactory, SoftDeletes, Auditable, SoftHardDelete;



    protected $fillable = [

        'teacher_id',

        'student_id',

        'scheduled_at',

        'start_time',

        'end_time',

        'duration_minutes',

        'status',

        'zoom_meeting_id',

        'zoom_join_url',

        'zoom_start_url',

        'zoom_password',

        'notes',

        'price',

        'booking_time_limit',

        'notification_sent_24h',

        'notification_sent_1h'

    ];



    protected $casts = [

        'scheduled_at' => 'datetime',

        'start_time' => 'datetime',

        'end_time' => 'datetime',

        'booking_time_limit' => 'datetime',

        'notification_sent_24h' => 'boolean',

        'notification_sent_1h' => 'boolean'

    ];



    // Relationships

    public function student()

    {

        return $this->belongsTo(Student::class);

    }



    public function teacher()

    {

        return $this->belongsTo(Teacher::class);

    }



    public function sessionRecordings()

    {

        return $this->hasMany(SessionRecording::class);

    }



    public function feedback()

    {

        return $this->hasMany(Feedback::class);

    }



    public function payments()

    {

        return $this->hasMany(Payment::class);

    }

    /**
     * Check if booking can be deleted
     */
    public function canBeDeleted()
    {
        // Cannot delete if has session recordings
        if ($this->sessionRecordings()->count() > 0) {
            return false;
        }
        
        // Cannot delete if has payments
        if ($this->payments()->count() > 0) {
            return false;
        }
        
        // Cannot delete if has feedback
        if ($this->feedback()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Get reason why booking cannot be deleted
     */
    public function getDeletionBlockReason()
    {
        $reasons = [];
        
        if ($this->sessionRecordings()->count() > 0) {
            $reasons[] = 'has session recordings';
        }
        
        if ($this->payments()->count() > 0) {
            $reasons[] = 'has payment records';
        }
        
        if ($this->feedback()->count() > 0) {
            $reasons[] = 'has feedback records';
        }

        if (!empty($reasons)) {
            return 'This booking ' . implode(', ', $reasons) . '. Please handle these records first.';
        }

        return null;
    }

    /**
     * Get redirect URL after delete
     */
    protected function getRedirectAfterDelete()
    {
        return route('admin.bookings.index');
    }

    /**
     * Get redirect URL after restore
     */
    protected function getRedirectAfterRestore()
    {
        return route('admin.bookings.index');
    }



    // 24-hour booking limit validation

    public static function canBookSession($startTime, $studentId)

    {

        $existingBooking = self::where('start_time', $startTime)

            ->where('student_id', $studentId)

            ->first();



        if ($existingBooking) {

            return false; // Already booked

        }



        // Check if session is within 24 hours

        $sessionTime = Carbon::parse($startTime);

        $now = Carbon::now();

        

        if ($sessionTime->diffInHours($now) < 24) {

            return false; // Too close to session time

        }



        return true;

    }



    // Set booking time limit (24 hours from now)

    public function setBookingTimeLimit()

    {

        $this->booking_time_limit = Carbon::now()->addHours(24);

        $this->save();

    }



    // Check if booking time limit expired

    public function isBookingTimeExpired()

    {

        if (!$this->booking_time_limit) {

            return false;

        }

        return Carbon::now()->isAfter($this->booking_time_limit);

    }



    // Send dynamic notifications

    public function sendDynamicNotifications()

    {

        $sessionTime = Carbon::parse($this->start_time);

        $now = Carbon::now();

        

        // 24-hour notification

        if (!$this->notification_sent_24h && $sessionTime->diffInHours($now) <= 24) {

            $this->send24HourNotification();

            $this->sendTeacher24HourNotification();

            $this->notification_sent_24h = true;

            $this->save();

        }

        

        // 1-hour notification

        if (!$this->notification_sent_1h && $sessionTime->diffInHours($now) <= 1) {

            $this->send1HourNotification();

            $this->sendTeacher1HourNotification();

            $this->notification_sent_1h = true;

            $this->save();

        }

    }



    private function send24HourNotification()

    {

        // Send 24-hour reminder email

        $student = $this->student;

        $teacher = $this->teacher;

        

        // Check if student and teacher have user records

        if (!$student || !$student->user || !$teacher || !$teacher->user) {

            \Log::warning('Cannot send 24h reminder - missing user records', [

                'booking_id' => $this->id,

                'student_exists' => $student ? true : false,

                'teacher_exists' => $teacher ? true : false

            ]);

            return;

        }

        

        \Mail::send('emails.session_reminder_24h', [

            'student' => $student,

            'teacher' => $teacher,

            'session' => $this,

            'booking' => $this

        ], function($message) use ($student) {

            $message->to($student->user->email)

                    ->subject('Reminder: Your lesson starts in 24 hours');

        });

    }



    private function send1HourNotification()

    {

        // Send 1-hour reminder email

        $student = $this->student;

        $teacher = $this->teacher;

        

        // Check if student and teacher have user records

        if (!$student || !$student->user || !$teacher || !$teacher->user) {

            \Log::warning('Cannot send 1h reminder - missing user records', [

                'booking_id' => $this->id,

                'student_exists' => $student ? true : false,

                'teacher_exists' => $teacher ? true : false

            ]);

            return;

        }

        

        \Mail::send('emails.session_reminder_1h', [

            'student' => $student,

            'teacher' => $teacher,

            'session' => $this,

            'booking' => $this

        ], function($message) use ($student) {

            $message->to($student->user->email)

                    ->subject('Reminder: Your lesson starts in 1 hour');

        });

    }

    private function sendTeacher24HourNotification()

    {

        // Send 24-hour reminder email to teacher

        $student = $this->student;

        $teacher = $this->teacher;

        

        // Check if student and teacher have user records

        if (!$student || !$student->user || !$teacher || !$teacher->user) {

            \Log::warning('Cannot send teacher 24h reminder - missing user records', [

                'booking_id' => $this->id,

                'student_exists' => $student ? true : false,

                'teacher_exists' => $teacher ? true : false

            ]);

            return;

        }

        

        \Mail::send('emails.teacher_reminder_24h', [

            'student' => $student,

            'teacher' => $teacher,

            'session' => $this,

            'booking' => $this

        ], function($message) use ($teacher) {

            $message->to($teacher->user->email)

                    ->subject('Reminder: Your teaching session starts in 24 hours');

        });

    }

    private function sendTeacher1HourNotification()

    {

        // Send 1-hour reminder email to teacher

        $student = $this->student;

        $teacher = $this->teacher;

        

        // Check if student and teacher have user records

        if (!$student || !$student->user || !$teacher || !$teacher->user) {

            \Log::warning('Cannot send teacher 1h reminder - missing user records', [

                'booking_id' => $this->id,

                'student_exists' => $student ? true : false,

                'teacher_exists' => $teacher ? true : false

            ]);

            return;

        }

        

        \Mail::send('emails.teacher_reminder_1h', [

            'student' => $student,

            'teacher' => $teacher,

            'session' => $this,

            'booking' => $this

        ], function($message) use ($teacher) {

            $message->to($teacher->user->email)

                    ->subject('URGENT: Your teaching session starts in 1 hour');

        });

    }

}

