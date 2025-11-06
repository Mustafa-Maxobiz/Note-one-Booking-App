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

                <a href="{{ route('student.bookings.index') }}" class="btn btn-outline-light me-2">

                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings

                </a>

                @if($booking->status === 'confirmed' && $booking->zoom_join_url)

                    @if($booking->start_time <= now() && $booking->end_time > now())

                        <a href="{{ $booking->zoom_join_url }}" target="_blank" class="btn btn-light btn-lg">

                            <i class="fas fa-video me-2"></i>Join Session

                        </a>

                    @elseif($booking->start_time > now())

                        <div class="alert alert-info">

                            <i class="fas fa-clock me-2"></i>

                            <strong>Session not started yet.</strong><br>

                            <small>Join link will be available at {{ $booking->start_time->format('M d, Y g:i A') }}</small>

                        </div>

                    @else

                        <div class="alert alert-warning">

                            <i class="fas fa-check-circle me-2"></i>

                            <strong>Session has ended.</strong><br>

                            <small>This session ended at {{ $booking->end_time->format('M d, Y g:i A') }}</small>

                        </div>

                    @endif

                @endif

            </div>

        </div>

    </div>

</div>



<div class="row">

    <div class="col-lg-8">

        <!-- Booking Information -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-info-circle me-2"></i>Session Information

                </h5>

                <p class="modern-card-subtitle">Details about your upcoming session</p>

            </div>

            <div class="modern-card-body">

                <div class="session-details-grid">

                    <div class="detail-item">

                        <div class="detail-icon">

                            <i class="fas fa-calendar-day"></i>

                        </div>

                        <div class="detail-content">

                            <div class="detail-label">Date</div>

                            <div class="detail-value">{{ $booking->start_time->format('l, F d, Y') }}</div>

                        </div>

                    </div>

                    

                    <div class="detail-item">

                        <div class="detail-icon">

                            <i class="fas fa-clock"></i>

                        </div>

                        <div class="detail-content">

                            <div class="detail-label">Time</div>

                            <div class="detail-value">

                                {{ $booking->start_time->format('g:i A') }} - {{ $booking->start_time->addMinutes((int) $booking->duration_minutes)->format('g:i A') }}

                            </div>

                        </div>

                    </div>

                    

                    <div class="detail-item">

                        <div class="detail-icon">

                            <i class="fas fa-hourglass-half"></i>

                        </div>

                        <div class="detail-content">

                            <div class="detail-label">Duration</div>

                            <div class="detail-value">{{ $booking->duration_minutes }} minutes</div>

                        </div>

                    </div>

                    

                    <div class="detail-item">

                        <div class="detail-icon">

                            <i class="fas fa-flag"></i>

                        </div>

                        <div class="detail-content">

                            <div class="detail-label">Status</div>

                            <div class="detail-value">

                                @if($booking->status === 'confirmed')

                                    <span class="status-badge status-confirmed">

                                        <i class="fas fa-check-circle me-1"></i>Confirmed

                                    </span>

                                @elseif($booking->status === 'pending')

                                    <span class="status-badge status-pending">

                                        <i class="fas fa-clock me-1"></i>Pending

                                    </span>

                                @elseif($booking->status === 'completed')

                                    <span class="status-badge status-completed">

                                        <i class="fas fa-check-double me-1"></i>Completed

                                    </span>

                                @elseif($booking->status === 'cancelled')

                                    <span class="status-badge status-cancelled">

                                        <i class="fas fa-times-circle me-1"></i>Cancelled

                                    </span>

                                @elseif($booking->status === 'declined')

                                    <span class="status-badge status-declined">

                                        <i class="fas fa-ban me-1"></i>Declined

                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>



                @if($booking->notes)

                    <div class="session-notes">

                        <h6 class="notes-title">

                            <i class="fas fa-sticky-note me-2"></i>Session Notes

                        </h6>

                        <div class="notes-content">{{ $booking->notes }}</div>

                    </div>

                @endif



                @if($booking->zoom_join_url && $booking->status === 'confirmed')

                    <div class="zoom-meeting">

                        <h6 class="zoom-title">

                            <i class="fas fa-video me-2"></i>Zoom Meeting

                        </h6>

                        <div class="zoom-actions">

                            @if($booking->start_time <= now() && $booking->end_time > now())

                                <a href="{{ route('meeting.join', $booking) }}" class="btn btn-primary btn-lg" target="_blank">

                                    <i class="fas fa-video me-2"></i>Join Meeting

                                </a>

                                <small class="zoom-note">Click to join your scheduled session</small>

                            @elseif($booking->start_time > now())

                                <div class="alert alert-info">

                                    <i class="fas fa-clock me-2"></i>

                                    <strong>Meeting not started yet.</strong><br>

                                    <small>Join link will be available at {{ $booking->start_time->format('M d, Y g:i A') }}</small>

                                </div>

                            @else

                                <div class="alert alert-warning">

                                    <i class="fas fa-check-circle me-2"></i>

                                    <strong>Meeting has ended.</strong><br>

                                    <small>This meeting ended at {{ $booking->end_time->format('M d, Y g:i A') }}</small>

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

            </div>

        </div>

        <!-- Lesson Log Link -->
        <div class="modern-card mb-4">
            <div class="modern-card-body text-center">
                <h6 class="card-title mb-3">
                    <i class="fas fa-book me-2"></i>Your Learning Journey
                </h6>
                <p class="text-muted mb-3">Review what you've learned in your lessons</p>
                <a href="{{ route('lesson-notes.index', ['student_id' => auth()->user()->student->id]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-book me-2"></i>View Lesson Log
                </a>
            </div>
        </div>

        <!-- Teacher Information -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-chalkboard-teacher me-2"></i>Teacher Information

                </h5>

                <p class="modern-card-subtitle">Your session instructor</p>

            </div>

            <div class="modern-card-body">

                <div class="teacher-profile">

                    <div class="teacher-avatar">

                        <img src="{{ $booking->teacher->user->profile_picture_url }}" 

                             alt="{{ $booking->teacher->user->name }}"

                             class="avatar-img">

                    </div>

                    <div class="teacher-info">

                        <h5 class="teacher-name">{{ $booking->teacher->user->name }}</h5>

                        <p class="teacher-qualifications">{{ $booking->teacher->qualifications }}</p>

                        <p class="teacher-email">{{ $booking->teacher->user->email }}</p>

                    </div>

                </div>



                @if($booking->teacher->bio)

                    <div class="teacher-bio">

                        <h6 class="bio-title">

                            <i class="fas fa-user-circle me-2"></i>About

                        </h6>

                        <p class="bio-content">{{ $booking->teacher->bio }}</p>

                    </div>

                @endif



                @if($booking->teacher->experience_years)

                    <div class="teacher-experience">

                        <div class="experience-item">

                            <i class="fas fa-graduation-cap me-2"></i>

                            <span class="experience-label">Experience:</span>

                            <span class="experience-value">{{ $booking->teacher->experience_years }} years</span>

                        </div>

                    </div>

                @endif



                @if($booking->sessionRecordings->count() > 0)

                    <div class="session-recordings">

                        <h6 class="recordings-title">

                            <i class="fas fa-video me-2"></i>Session Recordings

                        </h6>

                        <div class="recordings-grid">

                            @foreach($booking->sessionRecordings as $recording)

                                <div class="recording-item">

                                    <div class="recording-icon">

                                        @if($recording->recording_type === 'video')

                                            <i class="fas fa-video"></i>

                                        @elseif($recording->recording_type === 'audio')

                                            <i class="fas fa-microphone"></i>

                                        @elseif($recording->recording_type === 'chat')

                                            <i class="fas fa-comments"></i>

                                        @else

                                            <i class="fas fa-file"></i>

                                        @endif

                                    </div>

                                    <div class="recording-info">

                                        <div class="recording-name">{{ $recording->file_name }}</div>

                                        <div class="recording-duration">{{ $recording->formatted_duration }}</div>

                                    </div>

                                    <div class="recording-actions">

                                        <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Play">

                                            <i class="fas fa-play"></i>

                                        </a>

                                        <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-sm btn-outline-success" title="Download">

                                            <i class="fas fa-download"></i>

                                        </a>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>

        <!-- Feedback Section -->
        @if($booking->status === 'completed')
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-star me-2"></i>Session Feedback
                </h5>
                <p class="modern-card-subtitle">Rate your experience with this session</p>
            </div>
            <div class="modern-card-body">
                @if($booking->feedback->where('student_id', auth()->user()->student->id)->count() > 0)
                    @php $feedback = $booking->feedback->where('student_id', auth()->user()->student->id)->first(); @endphp
                    <div class="feedback-display">
                        <div class="rating-display mb-3">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="rating-text ms-2">{{ $feedback->rating }}/5</span>
                            </div>
                        </div>
                        @if($feedback->comment)
                            <div class="feedback-comment">
                                <p class="comment-text">{{ $feedback->comment }}</p>
                            </div>
                        @endif
                        <div class="feedback-meta">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Submitted on {{ $feedback->created_at->format('M d, Y g:i A') }}
                            </small>
                        </div>
                    </div>
                @else
                    <div class="feedback-prompt">
                        <p class="mb-3">How was your session? Your feedback helps us improve our services.</p>
                        <a href="{{ route('feedback.create.booking', $booking) }}" class="btn btn-success">
                            <i class="fas fa-star me-2"></i>Add Rating & Feedback
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

    </div>



    <div class="col-lg-4">

        <!-- Actions -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-tools me-2"></i>Actions

                </h5>

                <p class="modern-card-subtitle">Manage your session</p>

            </div>

            <div class="modern-card-body">

                <div class="action-buttons">

                    @if($booking->status === 'confirmed' && $booking->zoom_join_url)

                        <a href="{{ $booking->zoom_join_url }}" target="_blank" class="btn btn-primary btn-lg action-btn d-none">

                            <i class="fas fa-video me-2"></i>Join Session

                        </a>

                    @endif

                    

                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        @if($booking->end_time > now())
                        <form method="POST" action="{{ route('student.bookings.cancel', $booking) }}" class="d-inline w-100">

                            @csrf

                            <button type="submit" class="btn btn-danger btn-lg action-btn w-100" onclick="return confirm('Are you sure you want to cancel this booking?')">

                                <i class="fas fa-times me-2"></i>Cancel Booking

                            </button>

                        </form>
                        @endif

                    @endif

                    @if($booking->status === 'completed' && $booking->feedback->where('student_id', auth()->user()->student->id)->where('type', 'student_to_teacher')->count() == 0)

                        <a href="{{ route('feedback.create.booking', $booking) }}" class="btn btn-info w-100 mb-2">

                            <i class="fas fa-star me-2"></i>Rate Teacher

                        </a>

                    @endif



                    <a href="{{ route('student.bookings.index') }}" class="btn btn-outline-secondary action-btn">

                        <i class="fas fa-arrow-left me-2"></i>Back to Bookings

                    </a>

                </div>

            </div>

        </div>



        <!-- Booking Summary -->

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-clipboard-list me-2"></i>Booking Summary

                </h5>

                <p class="modern-card-subtitle">Session details</p>

            </div>

            <div class="modern-card-body">

                <div class="summary-items">

                    <div class="summary-item">

                        <div class="summary-label">

                            <i class="fas fa-hashtag me-2"></i>Booking ID

                        </div>

                        <div class="summary-value">#{{ $booking->id }}</div>

                    </div>

                    

                    <div class="summary-item">

                        <div class="summary-label">

                            <i class="fas fa-calendar-plus me-2"></i>Created

                        </div>

                        <div class="summary-value">{{ $booking->created_at->format('M d, Y g:i A') }}</div>

                    </div>

                    

                    <div class="summary-item">

                        <div class="summary-label">

                            <i class="fas fa-edit me-2"></i>Last Updated

                        </div>

                        <div class="summary-value">{{ $booking->updated_at->format('M d, Y g:i A') }}</div>

                    </div>

                    

                    @if($booking->completed_at)

                        <div class="summary-item">

                            <div class="summary-label">

                                <i class="fas fa-check-circle me-2"></i>Completed

                            </div>

                            <div class="summary-value">{{ $booking->completed_at->format('M d, Y g:i A') }}</div>

                        </div>

                    @endif

                </div>

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

    

    /* Session Details Grid */

    .session-details-grid {

        display: grid;

        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));

        gap: 1.5rem;

        margin-bottom: 2rem;

    }

    

    .detail-item {

        display: flex;

        align-items: center;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 12px;

        border-left: 4px solid #ef473e;

    }

    

    .detail-icon {

        width: 50px;

        height: 50px;

        border-radius: 50%;

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 1.25rem;

    }

    

    .detail-content {

        flex: 1;

    }

    

    .detail-label {

        font-size: 0.875rem;

        color: #6c757d;

        font-weight: 500;

        margin-bottom: 0.25rem;

    }

    

    .detail-value {

        font-size: 1rem;

        font-weight: 600;

        color: #2c3e50;

    }

    

    /* Status Badges */

    .status-badge {

        display: inline-flex;

        align-items: center;

        padding: 0.375rem 0.75rem;

        border-radius: 15px;

        font-size: 0.7rem;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.5px;

    }

    

    .status-confirmed {

        background: #d4edda;

        color: #155724;

    }

    

    .status-pending {

        background: #fff3cd;

        color: #856404;

    }

    

    .status-completed {

        background: #d1ecf1;

        color: #0c5460;

    }

    

    .status-cancelled {

        background: #f8d7da;

        color: #721c24;

    }

    

    .status-declined {

        background: #e2e3e5;

        color: #383d41;

    }

    

    /* Session Notes */

    .session-notes {

        background: #f8f9fa;

        padding: 1.5rem;

        border-radius: 12px;

        border-left: 4px solid #ef473e;

        margin-bottom: 2rem;

    }

    

    .notes-title {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 1rem;

    }

    

    .notes-content {

        color: #6c757d;

        line-height: 1.6;

    }

    

    /* Zoom Meeting */

    .zoom-meeting {

        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);

        padding: 1.5rem;

        border-radius: 12px;

        text-align: center;

    }

    

    .zoom-title {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 1rem;

    }

    

    .zoom-actions {

        display: flex;

        flex-direction: column;

        align-items: center;

        gap: 0.5rem;

    }

    

    .zoom-note {

        color: #6c757d;

        font-size: 0.875rem;

    }

    

    /* Teacher Profile */

    .teacher-profile {

        display: flex;

        align-items: center;

        gap: 1.5rem;

        margin-bottom: 2rem;

        padding: 1.5rem;

        background: #f8f9fa;

        border-radius: 12px;

    }

    

    .teacher-avatar {

        width: 80px;

        height: 80px;

        border-radius: 50%;

        overflow: hidden;

        border: 4px solid white;

        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);

    }

    

    .avatar-img {

        width: 100%;

        height: 100%;

        object-fit: cover;

    }

    

    .teacher-info {

        flex: 1;

    }

    

    .teacher-name {

        font-size: 1.5rem;

        font-weight: 700;

        color: #2c3e50;

        margin-bottom: 0.5rem;

    }

    

    .teacher-qualifications {

        color: #ef473e;

        font-weight: 600;

        margin-bottom: 0.25rem;

    }

    

    .teacher-email {

        color: #6c757d;

        font-size: 0.875rem;

    }

    

    /* Teacher Bio */

    .teacher-bio {

        margin-bottom: 1.5rem;

    }

    

    .bio-title {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 1rem;

    }

    

    .bio-content {

        color: #6c757d;

        line-height: 1.6;

    }

    

    /* Teacher Experience */

    .teacher-experience {

        margin-bottom: 2rem;

    }

    

    .experience-item {

        display: flex;

        align-items: center;

        gap: 0.5rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 8px;

    }

    

    .experience-label {

        font-weight: 600;

        color: #2c3e50;

    }

    

    .experience-value {

        color: #ef473e;

        font-weight: 600;

    }

    

    /* Session Recordings */

    .session-recordings {

        margin-top: 2rem;

    }

    

    .recordings-title {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 1rem;

    }

    

    .recordings-grid {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .recording-item {

        display: flex;

        align-items: center;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 12px;

        border: 1px solid #e9ecef;

    }

    

    .recording-icon {

        width: 40px;

        height: 40px;

        border-radius: 50%;

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 1rem;

    }

    

    .recording-info {

        flex: 1;

    }

    

    .recording-name {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.25rem;

    }

    

    .recording-duration {

        font-size: 0.875rem;

        color: #6c757d;

    }

    

    .recording-actions {

        display: flex;

        gap: 0.5rem;

    }

    

    /* Action Buttons */

    .action-buttons {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .action-btn {

        border-radius: 12px;

        font-weight: 600;

        transition: all 0.3s ease;

        padding: 0.875rem 1.5rem;

    }

    

    .action-btn:hover {

        transform: translateY(-2px);

        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);

    }

    

    .btn-primary {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        border: none;

        color: white;

    }

    

    .btn-primary:hover {

        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);

        color: white;

    }

    

    /* Summary Items */

    .summary-items {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .summary-item {

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding: 0.75rem 0;

        border-bottom: 1px solid #f1f3f4;

    }

    

    .summary-item:last-child {

        border-bottom: none;

    }

    

    .summary-label {

        font-weight: 600;

        color: #2c3e50;

        display: flex;

        align-items: center;

    }

    

    .summary-value {

        font-weight: 500;

        color: #6c757d;

        text-align: right;

    }

    

    /* Responsive */

    @media (max-width: 768px) {

        .page-title {

            font-size: 2rem;

        }

        

        .header-actions {

            flex-direction: column;

            align-items: stretch;

        }

        

        .session-details-grid {

            grid-template-columns: 1fr;

        }

        

        .teacher-profile {

            flex-direction: column;

            text-align: center;

        }

        

        .recording-item {

            flex-direction: column;

            align-items: stretch;

            text-align: center;

        }

        

        .recording-actions {

            justify-content: center;

        }

    }

</style>

@endsection

