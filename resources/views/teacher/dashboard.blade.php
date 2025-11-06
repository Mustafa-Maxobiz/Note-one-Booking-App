@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-chalkboard-teacher me-3"></i>Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="dashboard-subtitle">Welcome back! Manage your teaching schedule and track your progress.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="dashboard-date">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Sessions</div>
                        <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <i class="fas fa-arrow-up text-success me-1"></i>
                    <span class="text-success">+18%</span>
                    <span class="text-muted ms-2">this month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Pending Requests</div>
                        <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                    <span class="text-warning">Needs Action</span>
                    <span class="text-muted ms-2">awaiting approval</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
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
                <div class="stat-footer">
                    <i class="fas fa-arrow-up text-success me-1"></i>
                    <span class="text-success">+25%</span>
                    <span class="text-muted ms-2">this month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 d-none">
        <div class="stat-card stat-card-info">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Earnings</div>
                        <div class="stat-value">${{ number_format($stats['total_earnings'], 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <i class="fas fa-chart-line text-info me-1"></i>
                    <span class="text-info">Revenue</span>
                    <span class="text-muted ms-2">total earnings</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <p class="dashboard-card-subtitle">Manage your teaching activities</p>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('teacher.bookings.index') }}" class="quick-action-btn quick-action-primary">
                            <div class="quick-action-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Manage Bookings</h6>
                                <p>View and manage sessions</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('lesson-notes.index') }}" class="quick-action-btn quick-action-info">
                            <div class="quick-action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Lesson Notes</h6>
                                <p>Document student progress</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('teacher.session-recordings.index') }}" class="quick-action-btn quick-action-secondary">
                            <div class="quick-action-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Session Recordings</h6>
                                <p>Access recorded lessons</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('teacher.availability.index') }}" class="quick-action-btn quick-action-success">
                            <div class="quick-action-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Set Availability</h6>
                                <p>Update your schedule</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('teacher.profile.index') }}" class="quick-action-btn quick-action-warning">
                            <div class="quick-action-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Edit Profile</h6>
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
                <p class="dashboard-card-subtitle">Your scheduled teaching sessions</p>
            </div>
            <div class="dashboard-card-body">
                @if($upcoming_bookings->count() > 0)
                    <div class="upcoming-sessions">
                                @foreach($upcoming_bookings as $booking)
                            <div class="session-card">
                                <div class="session-student">
                                                <img src="{{ $booking->student->user->small_profile_picture_url }}"
                                                     alt="{{ $booking->student->user->name }}"
                                         class="student-avatar">
                                    <div class="student-info">
                                        <h6 class="student-name">{{ $booking->student->user->name }}</h6>
                                        <p class="student-level">{{ $booking->student->level }} level</p>
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
                                                @if($booking->zoom_start_url && $booking->status === 'confirmed')
                                        <a href="{{ route('meeting.start', $booking) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-play me-1"></i>Start
                                                    </a>
                                                @endif
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                                                                 <ul class="dropdown-menu">
                                                     <li><a class="dropdown-item" href="{{ route('teacher.bookings.show', $booking) }}"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                                     <li><a class="dropdown-item" href="{{ route('teacher.bookings.edit', $booking) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                     <li><hr class="dropdown-divider"></li>
                                                     <li><a class="dropdown-item text-danger" href="{{ route('teacher.bookings.destroy', $booking) }}" onclick="return confirm('Are you sure you want to cancel this booking?')"><i class="fas fa-times me-2"></i>Cancel</a></li>
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
                    </div>
                @endif
            </div>
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
                <p class="dashboard-card-subtitle">Your teaching history</p>
            </div>
            <div class="dashboard-card-body">
                @if($recent_bookings->count() > 0)
                    <div class="recent-sessions">
                                @foreach($recent_bookings as $booking)
                            <div class="recent-session-item">
                                <div class="session-avatar">
                                    <img src="{{ $booking->student->user->small_profile_picture_url }}" 
                                         alt="{{ $booking->student->user->name }}" 
                                         class="student-avatar-small">
                                </div>
                                <div class="session-content">
                                    <div class="session-header">
                                        <h6 class="session-title">{{ $booking->student->user->name }}</h6>
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
                                    <a href="{{ route('teacher.bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm">
                                                     <i class="fas fa-eye"></i>
                                                 </a>
                                                 @if($booking->status === 'completed')
                                        <a href="{{ route('teacher.bookings.show', $booking) }}" class="btn btn-outline-success btn-sm">
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
    
    .session-student {
        display: flex;
        align-items: center;
        margin-right: 1.5rem;
        min-width: 200px;
    }
    
    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .student-info h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }
    
    .student-info p {
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
        display: inline-flex;
        align-items: center;
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
    
    .student-avatar-small {
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
        
        .session-student {
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
</style>
@endsection
