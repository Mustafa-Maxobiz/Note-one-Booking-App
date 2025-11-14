@extends('layouts.app')



@section('title', 'Booking Details')



@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-3"></i>Booking Details
            </h1>
            <p class="page-subtitle">Session #{{ $booking->id }} - {{ $booking->start_time->format('M d, Y') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('teacher.bookings.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>



<div class="row">

    <div class="col-md-8">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-info-circle me-2"></i>Booking Information

                </h5>

                <p class="modern-card-subtitle">Student and session details</p>

            </div>

            <div class="modern-card-body">

                <div class="row">

                    <div class="col-md-6">

                        <h6 class="text-muted">Student Information</h6>

                        <div class="d-flex align-items-center mb-3">

                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">

                                <span class="text-white fw-bold fs-4">{{ substr($booking->student->user->name, 0, 1) }}</span>

                            </div>

                            <div>

                                <h5 class="mb-1">{{ $booking->student->user->name }}</h5>

                                <p class="text-muted mb-0">{{ $booking->student->level }} level</p>

                            </div>

                        </div>

                        <p><strong>Email:</strong> {{ $booking->student->user->email }}</p>

                        @if($booking->student->user->phone)

                            <p><strong>Phone:</strong> {{ $booking->student->user->phone }}</p>

                        @endif

                    </div>

                    <div class="col-md-6">

                        <h6 class="text-muted">Session Details</h6>

                        <p><strong>Date:</strong> {{ $booking->start_time->format('l, F d, Y') }}</p>

                        <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>

                        <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>

                        <p><strong>Status:</strong> 

                            @if($booking->status === 'pending')

                                <span class="status-badge status-pending"><i class="fas fa-clock me-1"></i>Pending</span>

                            @elseif($booking->status === 'confirmed')

                                <span class="status-badge status-confirmed"><i class="fas fa-check-circle me-1"></i>Confirmed</span>

                            @elseif($booking->status === 'completed')

                                <span class="status-badge status-completed"><i class="fas fa-check-double me-1"></i>Completed</span>

                            @elseif($booking->status === 'cancelled')

                                <span class="status-badge status-cancelled"><i class="fas fa-times-circle me-1"></i>Cancelled</span>

                            @elseif($booking->status === 'no_show')

                                <span class="status-badge status-no-show"><i class="fas fa-user-times me-1"></i>No Show</span>

                            @endif

                        </p>

                    </div>

                </div>

                

                @if($booking->notes)

                    <hr>

                    <h6 class="text-muted">Notes</h6>

                    <p>{{ $booking->notes }}</p>

                @endif

                

                @if($booking->zoom_start_url && $booking->status === 'confirmed')
                    @if($booking->end_time > now())
                    <hr>

                    <h6 class="text-muted">Zoom Meeting</h6>

                    <a href="{{ route('meeting.start', $booking) }}" target="_blank" class="btn btn-success">

                        <i class="fas fa-play me-2"></i>Start Meeting

                    </a>
                    @endif
                @endif



                @if($booking->sessionRecordings->count() > 0)

                    <hr>

                    <h6 class="text-muted">Session Recordings</h6>

                    <div class="modern-table-container">

                        <table class="modern-table">

                            <thead>

                                <tr>

                                    <th>Type</th>

                                    <th>File Name</th>

                                    <th>Duration</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($booking->sessionRecordings as $recording)

                                    <tr>

                                        <td>

                                            @if($recording->recording_type === 'video')

                                                <i class="fas fa-video text-primary"></i> Video

                                            @elseif($recording->recording_type === 'audio')

                                                <i class="fas fa-microphone text-success"></i> Audio

                                            @elseif($recording->recording_type === 'chat')

                                                <i class="fas fa-comments text-info"></i> Chat

                                            @else

                                                <i class="fas fa-file text-secondary"></i> {{ ucfirst($recording->recording_type) }}

                                            @endif

                                        </td>

                                        <td>{{ $recording->file_name }}</td>

                                        <td>{{ $recording->formatted_duration }}</td>

                                        <td>

                                            <div class="btn-group btn-group-sm">

                                                <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Play">

                                                    <i class="fas fa-play"></i>

                                                </a>

                                                <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-outline-success btn-sm" title="Download">

                                                    <i class="fas fa-download"></i>

                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif

            </div>

        </div>

        

        {{-- Feedback & Ratings section disabled for now
        @if($booking->feedback->count() > 0)

            <div class="modern-card mt-4">

                <div class="modern-card-header">

                    <h5 class="modern-card-title">

                        <i class="fas fa-comment-dots me-2"></i>Student Feedback

                    </h5>

                    <p class="modern-card-subtitle">Feedback and ratings from the student</p>

                </div>

                <div class="modern-card-body">

                    @foreach($booking->feedback as $feedback)

                        <div class="mb-3">

                            <div class="d-flex align-items-center mb-2">

                                @for($i = 1; $i <= 5; $i++)

                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>

                                @endfor

                                <span class="ms-2">{{ $feedback->rating }}/5</span>

                            </div>

                            <p class="mb-1">{{ $feedback->comment }}</p>

                            <small class="text-muted">Posted on {{ $feedback->created_at->format('M d, Y g:i A') }}</small>

                        </div>

                        @if(!$loop->last)

                            <hr>

                        @endif

                    @endforeach

                </div>

            </div>

        @endif
        --}}

    </div>

    

    <div class="col-md-4">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-cogs me-2"></i>Actions

                </h5>

                <p class="modern-card-subtitle">Manage this booking</p>

            </div>

            <div class="modern-card-body">

                @if($booking->status === 'pending')
                    @if($booking->end_time > now())
                    <form method="POST" action="{{ route('teacher.bookings.accept', $booking) }}" class="mb-2">

                        @csrf

                        <button type="submit" class="btn btn-success w-100">

                            <i class="fas fa-check me-2"></i>Accept Booking

                        </button>

                    </form>

                    <form method="POST" action="{{ route('teacher.bookings.decline', $booking) }}" class="mb-2">

                        @csrf

                        <button type="submit" class="btn btn-danger w-100">

                            <i class="fas fa-times me-2"></i>Decline Booking

                        </button>

                    </form>
                    
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> Cancellations are not allowed within {{ \App\Models\SystemSetting::getValue('cancellation_policy_hours', 24) }} hours of the scheduled time.
                        </div>
                    @endif

                @endif

                

                @if($booking->status === 'confirmed')

                    @if($booking->start_time <= now())

                        <form method="POST" action="{{ route('teacher.bookings.complete', $booking) }}" class="mb-2">

                            @csrf

                            <button type="submit" class="btn btn-primary w-100">

                                <i class="fas fa-check-circle me-2"></i>Mark as Completed

                            </button>

                        </form>

                        <a href="{{ route('lesson-notes.create', ['student_id' => $booking->student_id, 'booking_id' => $booking->id]) }}" class="btn btn-outline-primary w-100 mb-2">

                            <i class="fas fa-book me-2"></i>Add Lesson Note

                        </a>

                    @else

                        <div class="alert alert-info mb-2">

                            <i class="fas fa-clock me-2"></i>

                            <strong>Meeting not started yet.</strong><br>

                            <small>You can mark this session as completed after {{ $booking->start_time->format('M d, Y g:i A') }}</small>

                        </div>

                    @endif

                    @if($booking->end_time > now())
                    <a href="{{ route('teacher.bookings.edit', $booking) }}" class="btn btn-warning w-100 mb-2">

                        <i class="fas fa-edit me-2"></i>Update Status

                    </a>
                    @endif

                @endif

                

                {{-- Feedback button disabled for now
                @if($booking->status === 'completed' && $booking->feedback->where('teacher_id', auth()->user()->teacher->id)->where('type', 'teacher_to_student')->count() == 0)


                    <a href="{{ route('feedback.create.booking', $booking) }}" class="btn btn-info w-100 mb-2">

                        <i class="fas fa-star me-2"></i>Rate Student

                    </a>

                @endif
                --}}

            </div>

        </div>

    </div>

</div>

@endsection

@section('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modern-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .modern-card-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0.25rem 0 0 0;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
    
    /* Modern Table */
    .modern-table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    
    .modern-table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .modern-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e9ecef;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modern-table td {
        padding: 1rem;
        border-bottom: 1px solid #f8f9fa;
        vertical-align: middle;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Form Styling */
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.95rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
    }
    
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
        color: white;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
        color: #856404;
        border: 1px solid #FFA000;
    }
    
    .status-confirmed {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #b1dfbb;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
        border: 1px solid #abdde5;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f1b0b7;
    }
    
    .status-no-show {
        background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
        color: #383d41;
        border: 1px solid #c6c8ca;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .modern-card-header,
        .modern-card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

