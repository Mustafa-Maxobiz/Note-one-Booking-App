@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-guitar me-3"></i>Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="dashboard-subtitle">Welcome back! Track your learning progress and manage your sessions.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="dashboard-date">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </div>
</div>

<!-- Main Action Section -->
<div class="action-section mb-5">
    <div class="row">
        <div class="col-12">
            <div class="action-card">
                <div class="action-content">
                    <div class="action-icon enhanced-action-icon">
                    <a href="{{ route('student.booking.create') }}" style="text-decoration: none; color: inherit;"><i class="fas fa-calendar-plus"></i></a>
                    </div>
                    <div class="action-text">
                        <h3 class="action-title p-0"><a href="{{ route('student.booking.create') }}" style="text-decoration: none; color: inherit;">Book Your Next Lesson</a></h3>
                        <p class="action-description">Schedule a session with our qualified teachers and continue your musical journey</p>
                    </div>
                    <div class="action-button">
                        <a href="{{ route('student.booking.create') }}" class="btn btn-primary btn-lg book-now-btn">
                            <i class="fas fa-plus me-2"></i>Book Now
                            <span class="btn-shine"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Friendly Summary Card -->
@if($upcoming_bookings->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="summary-card">
            <div class="summary-content">
                <div class="summary-icon">
                    <i class="fas fa-music"></i>
                </div>
                <div class="summary-text">
                    <h5 class="summary-title p-0">You have {{ $upcoming_bookings->count() }} upcoming lesson{{ $upcoming_bookings->count() !== 1 ? 's' : '' }}</h5>
                    @if($upcoming_bookings->count() > 0)
                        <p class="summary-details">
                            Next: {{ $upcoming_bookings->first()->teacher->user->name }} 
                            at {{ $upcoming_bookings->first()->start_time->format('g:i A') }} 
                            on {{ $upcoming_bookings->first()->start_time->format('M j, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <p class="dashboard-card-subtitle">Manage your learning journey</p>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('student.booking.create') }}" class="quick-action-btn quick-action-primary">
                            <div class="quick-action-icon">
                                <i class="fas fa-guitar"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Book a Lesson</h6>
                                <p>Schedule your next session</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('lesson-notes.index', ['student_id' => auth()->user()->student->id]) }}" class="quick-action-btn quick-action-info">
                            <div class="quick-action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Lesson Log</h6>
                                <p>Review your learning journey</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('student.session-recordings.index') }}" class="quick-action-btn quick-action-warning">
                            <div class="quick-action-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Session Recordings</h6>
                                <p>Review recorded lessons</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('student.profile.index') }}" class="quick-action-btn quick-action-secondary">
                            <div class="quick-action-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>My Profile</h6>
                                <p>Update your information</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Upcoming Bookings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-calendar-plus me-2"></i>Upcoming Sessions
                </h5>
                <p class="dashboard-card-subtitle">Your scheduled learning sessions</p>
            </div>
            <div class="dashboard-card-body">
                @if($upcoming_bookings->count() > 0)
                    <div class="upcoming-sessions">
                                @foreach($upcoming_bookings as $booking)
                            <div class="session-card">
                                <div class="session-teacher">
                                                <img src="{{ $booking->teacher->user->small_profile_picture_url }}"
                                                     alt="{{ $booking->teacher->user->name }}"
                                         class="teacher-avatar">
                                    <div class="teacher-info">
                                        <h6 class="teacher-name">{{ $booking->teacher->user->name }}</h6>
                                        <p class="teacher-qualifications">{{ $booking->teacher->qualifications }}</p>
                                    </div>
                                </div>
                                <div class="session-details">
                                    <div class="session-time">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $booking->start_time->format('M d, Y') }}
                                    </div>
                                    <div class="session-duration">
                                        <i class="fas fa-clock me-2"></i>
                                        {{ $booking->start_time->format('g:i A') }} ({{ $booking->duration_minutes }} min)
                                                </div>
                                            </div>
                                <div class="session-status">
                                            @if($booking->status === 'confirmed')
                                        <span class="status-badge status-confirmed">
                                            <i class="fas fa-check-circle me-1"></i>Confirmed
                                        </span>
                                            @elseif($booking->status === 'pending')
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                            @endif
                                </div>
                                <div class="session-actions">
                                                @if($booking->zoom_join_url && $booking->status === 'confirmed')
                                                    @if($booking->start_time <= now() && $booking->end_time > now())
                                                        <a href="{{ route('meeting.join', $booking) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-video me-1"></i>Join
                                                        </a>
                                                    @elseif($booking->start_time > now())
                                                        <span class="btn btn-outline-secondary btn-sm disabled">
                                                            <i class="fas fa-clock me-1"></i>Not Started
                                                        </span>
                                                    @else
                                                        <span class="btn btn-outline-secondary btn-sm disabled">
                                                            <i class="fas fa-check-circle me-1"></i>Ended
                                                        </span>
                                                    @endif
                                                @endif
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('student.bookings.show', $booking) }}"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('student.booking.create') }}"><i class="fas fa-plus me-2"></i>Book New Session</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="{{ route('student.bookings.cancel', $booking) }}" onclick="return confirm('Are you sure you want to cancel this booking?')"><i class="fas fa-times me-2"></i>Cancel</a></li>
                                                </ul>
                                            </div>
                                </div>
                            </div>
                                @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No upcoming sessions</h6>
                        <p class="text-muted">You don't have any confirmed bookings scheduled.</p>
                        <a href="{{ route('student.booking.create') }}" class="btn btn-primary">
                            <i class="fas fa-guitar me-2"></i>Book a Lesson
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Notice -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Important:</strong> Lessons cannot be cancelled within 24 hours of the scheduled time.
        </div>
    </div>
</div>

                <!-- Recent Bookings -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-history me-2"></i>Recent Sessions
                </h5>
                <p class="dashboard-card-subtitle">Your learning history</p>
            </div>
            <div class="dashboard-card-body">
                @if($recent_bookings->count() > 0)
                    <div class="recent-sessions">
                                @foreach($recent_bookings as $booking)
                            <div class="recent-session-item">
                                <div class="session-avatar">
                                    <img src="{{ $booking->teacher->user->small_profile_picture_url }}" 
                                         alt="{{ $booking->teacher->user->name }}" 
                                         class="teacher-avatar-small">
                                </div>
                                <div class="session-content">
                                    <div class="session-header">
                                        <h6 class="session-title">{{ $booking->teacher->user->name }}</h6>
                                        <span class="session-status session-status-{{ $booking->status }}">
                                            @if($booking->status === 'completed')
                                                <i class="fas fa-check-double me-1"></i>Completed
                                            @elseif($booking->status === 'cancelled')
                                                <i class="fas fa-times-circle me-1"></i>Cancelled
                                            @elseif($booking->status === 'no_show')
                                                <i class="fas fa-user-times me-1"></i>No Show
                                            @else
                                                <i class="fas fa-clock me-1"></i>{{ ucfirst($booking->status) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="session-meta">
                                        <span class="session-date">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $booking->start_time->format('M d, Y') }}
                                        </span>
                                        <span class="session-time">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $booking->start_time->format('g:i A') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="session-actions">
                                    <a href="{{ route('student.bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm">
                                                     <i class="fas fa-eye"></i>
                                                 </a>
                                                 @if($booking->status === 'completed')
                                        <a href="{{ route('feedback.create.booking', $booking) }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-star"></i>
                                                     </a>
                                                 @endif
                                             </div>
                            </div>
                                @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent sessions</h6>
                        <p class="text-muted">Your completed sessions will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section (Moved to Bottom) -->
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card stat-card-success">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Completed Sessions</div>
                        <div class="stat-value">{{ $stats['completed_bookings'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stat-card stat-card-info">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Upcoming Sessions</div>
                        <div class="stat-value">{{ $stats['upcoming_bookings'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Dashboard Header */
    .dashboard-header {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }
    
    .dashboard-date {
        font-size: 1rem;
        opacity: 0.9;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        backdrop-filter: blur(10px);
    }
    
    /* Action Section */
    .action-section {
        margin: 2rem 0;
    }
    
    .action-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ef473e, #fdb838);
    }
    
    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .action-content {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    
    .action-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
    }
    
    .enhanced-action-icon {
        animation: iconPulse 2s ease-in-out infinite, iconRotate 4s linear infinite;
        box-shadow: 0 6px 20px rgba(239, 71, 62, 0.4);
        position: relative;
    }
    
    .enhanced-action-icon::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        border-radius: 20px;
        opacity: 0.3;
        animation: iconGlow 2s ease-in-out infinite;
        z-index: -1;
    }
    
    .enhanced-action-icon i {
        animation: musicNoteFloat 2s ease-in-out infinite;
        font-size: 2.2rem;
    }
    
    @keyframes iconPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    @keyframes iconRotate {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(2deg); }
        75% { transform: rotate(-2deg); }
        100% { transform: rotate(0deg); }
    }
    
    @keyframes iconGlow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.1); }
    }
    
    @keyframes iconBounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-5px); }
        60% { transform: translateY(-3px); }
    }
    
    @keyframes musicNoteFloat {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
        25% { transform: translateY(-3px) rotate(2deg) scale(1.05); }
        50% { transform: translateY(-1px) rotate(0deg) scale(1.02); }
        75% { transform: translateY(-3px) rotate(-2deg) scale(1.05); }
    }
    
    .action-text {
        flex: 1;
    }
    
    .action-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 0.5rem 0;
    }
    
    .action-description {
        font-size: 1rem;
        color: #6c757d;
        margin: 0;
        line-height: 1.5;
    }
    
    .action-button {
        flex-shrink: 0;
    }
    
    .action-button .btn {
        padding: 0.875rem 2rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .action-button .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Enhanced Book Now Button */
    .book-now-btn {
        position: relative;
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 50%, #ef473e 100%);
        background-size: 200% 200%;
        border: none;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
        padding: 1.2rem 3rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(239, 71, 62, 0.5);
        overflow: hidden;
        animation: gradientShift 3s ease-in-out infinite, pulseGlow 2s ease-in-out infinite, musicVibe 4s ease-in-out infinite;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 0.8px;
        text-transform: uppercase;
        font-family: 'Arial', sans-serif;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .book-now-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(239, 71, 62, 0.6);
        animation-play-state: paused;
    }
    
    .book-now-btn:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    .book-now-btn i {
        animation: guitarStrum 1.5s ease-in-out infinite;
        font-size: 1.3rem;
    }
    
    .btn-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
    }
    
    .book-now-btn:hover .btn-shine {
        left: 100%;
    }
    
    /* Keyframe Animations */
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes pulseGlow {
        0% { box-shadow: 0 8px 25px rgba(239, 71, 62, 0.4); }
        50% { box-shadow: 0 8px 35px rgba(239, 71, 62, 0.6), 0 0 20px rgba(239, 71, 62, 0.3); }
        100% { box-shadow: 0 8px 25px rgba(239, 71, 62, 0.4); }
    }
    
    @keyframes bounceIcon {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-3px); }
        60% { transform: translateY(-2px); }
    }
    
    @keyframes musicVibe {
        0%, 100% { transform: scale(1) rotate(0deg); }
        25% { transform: scale(1.02) rotate(1deg); }
        50% { transform: scale(1.01) rotate(0deg); }
        75% { transform: scale(1.02) rotate(-1deg); }
    }
    
    @keyframes guitarStrum {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(-2px) rotate(2deg); }
        50% { transform: translateY(-1px) rotate(0deg); }
        75% { transform: translateY(-2px) rotate(-2deg); }
    }
    
    /* Enhanced Action Card for Book Now */
    .action-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        animation: cardFloat 4s ease-in-out infinite;
    }
    
    @keyframes cardFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-2px); }
    }
    
    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ef473e, #fdb838);
        animation: borderGlow 2s ease-in-out infinite;
    }
    
    @keyframes borderGlow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* Summary Card */
    .summary-card {
        background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
        border: 2px solid #d4edda;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .summary-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .summary-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }
    
    .summary-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #155724;
        margin: 0 0 0.5rem 0;
    }
    
    .summary-details {
        font-size: 1rem;
        color: #155724;
        margin: 0;
        opacity: 0.8;
    }
    
    /* Statistics Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--brand-gradient);
    }
    
    .stat-card-primary::before { background: linear-gradient(90deg, #ef473e, #fdb838); }
    .stat-card-success::before { background: linear-gradient(90deg, #28a745, #20c997); }
    .stat-card-info::before { background: linear-gradient(90deg, #17a2b8, #6f42c1); }
    .stat-card-warning::before { background: linear-gradient(90deg, #ffc107, #fd7e14); }
    
    .stat-card-body {
        padding: 1.5rem;
    }
    
    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-card-primary .stat-icon { background: linear-gradient(135deg, #ef473e, #fdb838); }
    .stat-card-success .stat-icon { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-card-info .stat-icon { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
    .stat-card-warning .stat-icon { background: linear-gradient(135deg, #ffc107, #fd7e14); }
    
    .stat-footer {
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }
    
    /* Dashboard Cards */
    .dashboard-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }
    
    .dashboard-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .dashboard-card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .dashboard-card-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0.25rem 0 0 0;
    }
    
    .dashboard-card-body {
        padding: 1.5rem;
    }
    
    /* Quick Action Buttons */
    .quick-action-btn {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        text-decoration: none;
    }
    
    .quick-action-primary:hover { border-color: #ef473e; }
    .quick-action-success:hover { border-color: #28a745; }
    .quick-action-info:hover { border-color: #17a2b8; }
    .quick-action-warning:hover { border-color: #ffc107; }
    .quick-action-secondary:hover { border-color: #6c757d; }
    
    .quick-action-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        margin-right: 1rem;
    }
    
    .quick-action-primary .quick-action-icon { background: linear-gradient(135deg, #ef473e, #fdb838); }
    .quick-action-success .quick-action-icon { background: linear-gradient(135deg, #28a745, #20c997); }
    .quick-action-info .quick-action-icon { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
    .quick-action-warning .quick-action-icon { background: linear-gradient(135deg, #ffc107, #fd7e14); }
    .quick-action-secondary .quick-action-icon { background: linear-gradient(135deg, #6c757d, #495057); }
    
    .quick-action-content h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }
    
    .quick-action-content p {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }
    
    /* Session Cards */
    .upcoming-sessions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .session-card {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .session-card:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }
    
    .session-teacher {
        display: flex;
        align-items: center;
        margin-right: 1.5rem;
        min-width: 200px;
    }
    
    .teacher-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .teacher-info h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }
    
    .teacher-info p {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 0;
    }
    
    .session-details {
        flex: 1;
        margin-right: 1rem;
    }
    
    .session-time, .session-duration {
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .session-time i, .session-duration i {
        color: #6c757d;
    }
    
    .session-status {
        margin-right: 1rem;
    }
    
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 20px;
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
    
    .session-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    /* Recent Sessions */
    .recent-sessions {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .recent-session-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .recent-session-item:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }
    
    .teacher-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-right: 1rem;
    }
    
    .session-content {
        flex: 1;
    }
    
    .session-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .session-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .session-status {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .session-status-completed { background: #d4edda; color: #155724; }
    .session-status-cancelled { background: #f8d7da; color: #721c24; }
    .session-status-no_show { background: #e2e3e5; color: #383d41; }
    .session-status-pending { background: #fff3cd; color: #856404; }
    
    .session-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .session-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 2rem;
        }
        
        .dashboard-subtitle {
            font-size: 1rem;
        }
        
        .action-content {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .action-title {
            font-size: 1.3rem;
        }
        
        .action-description {
            font-size: 0.9rem;
        }
        
        .action-button .btn {
            width: 100%;
            justify-content: center;
        }
        
        .summary-content {
            flex-direction: column;
            text-align: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
        
        .quick-action-btn {
            padding: 1rem;
        }
        
        .session-card {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .session-teacher {
            margin-right: 0;
            margin-bottom: 1rem;
        }
        
        .session-details {
            margin-right: 0;
            margin-bottom: 1rem;
        }
        
        .session-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .session-meta {
            flex-direction: column;
            gap: 0.25rem;
        }
    }
    
    @media (max-width: 576px) {
        .welcome-section {
            padding: 1rem 0;
        }
        
        .action-card {
            padding: 1.5rem;
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
        
        .action-title {
            font-size: 1.2rem;
        }
        
        .action-button .btn {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection
