@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt me-3"></i>Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="dashboard-subtitle">Welcome back! Here's what's happening with your platform today.</p>
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
        <a href="{{ route('admin.users.index') }}" class="stat-card-link">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Total Users</div>
                            <div class="stat-value">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($growth['users'] > 0)
                            <i class="fas fa-arrow-up text-success me-1"></i>
                            <span class="text-success">+{{ $growth['users'] }}%</span>
                        @elseif($growth['users'] < 0)
                            <i class="fas fa-arrow-down text-danger me-1"></i>
                            <span class="text-danger">{{ $growth['users'] }}%</span>
                        @else
                            <i class="fas fa-minus text-muted me-1"></i>
                            <span class="text-muted">0%</span>
                        @endif
                        <span class="text-muted ms-2">from last month</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="stat-card-link">
            <div class="stat-card stat-card-success">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Total Teachers</div>
                            <div class="stat-value">{{ $stats['total_teachers'] }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($growth['teachers'] > 0)
                            <i class="fas fa-arrow-up text-success me-1"></i>
                            <span class="text-success">+{{ $growth['teachers'] }}%</span>
                        @elseif($growth['teachers'] < 0)
                            <i class="fas fa-arrow-down text-danger me-1"></i>
                            <span class="text-danger">{{ $growth['teachers'] }}%</span>
                        @else
                            <i class="fas fa-minus text-muted me-1"></i>
                            <span class="text-muted">0%</span>
                        @endif
                        <span class="text-muted ms-2">from last month</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="stat-card-link">
            <div class="stat-card stat-card-info">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Total Students</div>
                            <div class="stat-value">{{ $stats['total_students'] }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-footer">
                        @if($growth['students'] > 0)
                            <i class="fas fa-arrow-up text-success me-1"></i>
                            <span class="text-success">+{{ $growth['students'] }}%</span>
                        @elseif($growth['students'] < 0)
                            <i class="fas fa-arrow-down text-danger me-1"></i>
                            <span class="text-danger">{{ $growth['students'] }}%</span>
                        @else
                            <i class="fas fa-minus text-muted me-1"></i>
                            <span class="text-muted">0%</span>
                        @endif
                        <span class="text-muted ms-2">from last month</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-warning bg-body-secondary">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if($growth['revenue'] > 0)
                        <i class="fas fa-arrow-up text-success me-1"></i>
                        <span class="text-success">+{{ $growth['revenue'] }}%</span>
                    @elseif($growth['revenue'] < 0)
                        <i class="fas fa-arrow-down text-danger me-1"></i>
                        <span class="text-danger">{{ $growth['revenue'] }}%</span>
                    @else
                        <i class="fas fa-minus text-muted me-1"></i>
                        <span class="text-muted">0%</span>
                    @endif
                    <span class="text-muted ms-2">from last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Session Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-secondary">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Total Sessions</div>
                        <div class="stat-value">{{ $stats['total_sessions'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if($growth['sessions'] > 0)
                        <i class="fas fa-arrow-up text-success me-1"></i>
                        <span class="text-success">+{{ $growth['sessions'] }}%</span>
                    @elseif($growth['sessions'] < 0)
                        <i class="fas fa-arrow-down text-danger me-1"></i>
                        <span class="text-danger">{{ $growth['sessions'] }}%</span>
                    @else
                        <i class="fas fa-minus text-muted me-1"></i>
                        <span class="text-muted">0%</span>
                    @endif
                    <span class="text-muted ms-2">from last month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Pending Sessions</div>
                        <div class="stat-value">{{ $stats['pending_sessions'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if($growth['pending'] > 0)
                        <i class="fas fa-arrow-up text-warning me-1"></i>
                        <span class="text-warning">+{{ $growth['pending'] }}%</span>
                    @elseif($growth['pending'] < 0)
                        <i class="fas fa-arrow-down text-success me-1"></i>
                        <span class="text-success">{{ $growth['pending'] }}%</span>
                    @else
                        <i class="fas fa-minus text-muted me-1"></i>
                        <span class="text-muted">0%</span>
                    @endif
                    <span class="text-muted ms-2">from last month</span>
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
                        <div class="stat-value">{{ $stats['completed_sessions'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if($growth['completed'] > 0)
                        <i class="fas fa-arrow-up text-success me-1"></i>
                        <span class="text-success">+{{ $growth['completed'] }}%</span>
                    @elseif($growth['completed'] < 0)
                        <i class="fas fa-arrow-down text-danger me-1"></i>
                        <span class="text-danger">{{ $growth['completed'] }}%</span>
                    @else
                        <i class="fas fa-minus text-muted me-1"></i>
                        <span class="text-muted">0%</span>
                    @endif
                    <span class="text-muted ms-2">from last month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card stat-card-danger">
            <div class="stat-card-body">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-label">Cancelled Sessions</div>
                        <div class="stat-value">{{ $stats['cancelled_sessions'] }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if($growth['cancelled'] > 0)
                        <i class="fas fa-arrow-up text-danger me-1"></i>
                        <span class="text-danger">+{{ $growth['cancelled'] }}%</span>
                    @elseif($growth['cancelled'] < 0)
                        <i class="fas fa-arrow-down text-success me-1"></i>
                        <span class="text-success">{{ $growth['cancelled'] }}%</span>
                    @else
                        <i class="fas fa-minus text-muted me-1"></i>
                        <span class="text-muted">0%</span>
                    @endif
                    <span class="text-muted ms-2">from last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Confirmed Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['confirmed_sessions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 bg-body-secondary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 bg-body-secondary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">This Month Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['this_month_sessions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['this_month_revenue'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
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
                <p class="dashboard-card-subtitle">Common administrative tasks</p>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.users.create') }}" class="quick-action-btn quick-action-primary">
                            <div class="quick-action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Add User</h6>
                                <p>Create a new user account</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.bookings.index') }}" class="quick-action-btn quick-action-success">
                            <div class="quick-action-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>View Booking</h6>
                                <p>View all bookings</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.reports.index') }}" class="quick-action-btn quick-action-info">
                            <div class="quick-action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Reports</h6>
                                <p>View analytics & reports</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.export.index') }}" class="quick-action-btn quick-action-warning">
                            <div class="quick-action-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Export Data</h6>
                                <p>Download system data</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.search.index') }}" class="quick-action-btn quick-action-secondary">
                            <div class="quick-action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Search</h6>
                                <p>Find users & sessions</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <a href="{{ route('admin.email-settings.index') }}" class="quick-action-btn quick-action-dark">
                            <div class="quick-action-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="quick-action-content">
                                <h6>Email Settings</h6>
                                <p>Configure email templates</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Sessions -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-calendar-alt me-2"></i>Recent Sessions
                </h5>
                <p class="dashboard-card-subtitle">Latest booking activities</p>
            </div>
            <div class="dashboard-card-body">
                @if($recent_sessions->count() > 0)
                    <div class="recent-sessions">
                                @foreach($recent_sessions as $lesson)
                            <div class="recent-session-item">
                                <div class="session-avatars">
                                                <img src="{{ $lesson->teacher->user->small_profile_picture_url }}"
                                                     alt="{{ $lesson->teacher->user->name }}"
                                         class="session-avatar">
                                                <img src="{{ $lesson->student->user->small_profile_picture_url }}"
                                                     alt="{{ $lesson->student->user->name }}"
                                         class="session-avatar">
                                                </div>
                                <div class="session-content">
                                    <div class="session-header">
                                        <h6 class="session-title">{{ $lesson->teacher->user->name }} → {{ $lesson->student->user->name }}</h6>
                                        <span class="session-status session-status-{{ $lesson->status }}">
                                            @if($lesson->status === 'pending')
                                                <i class="fas fa-clock me-1"></i>Pending
                                            @elseif($lesson->status === 'confirmed')
                                                <i class="fas fa-check-circle me-1"></i>Confirmed
                                            @elseif($lesson->status === 'completed')
                                                <i class="fas fa-check-double me-1"></i>Completed
                                            @elseif($lesson->status === 'cancelled')
                                                <i class="fas fa-times-circle me-1"></i>Cancelled
                                            @endif
                                        </span>
                                    </div>
                                    <div class="session-meta">
                                        <span class="session-date">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $lesson->start_time->format('M d, Y') }}
                                        </span>
                                        <span class="session-time">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $lesson->start_time->format('g:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No recent sessions found</h6>
                        <p class="text-muted">New booking activities will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-server me-2"></i>System Status
                </h5>
                <p class="dashboard-card-subtitle">Platform health</p>
            </div>
            <div class="dashboard-card-body">
                <div class="system-status">
                    <!-- Platform Status -->
                    <div class="status-item">
                        <div class="status-indicator status-{{ $systemStatus['platform']['status'] }}"></div>
                        <div class="status-content">
                            <h6>Platform Status</h6>
                            <p class="text-{{ $systemStatus['platform']['status'] === 'online' ? 'success' : ($systemStatus['platform']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                {{ $systemStatus['platform']['message'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Database Status -->
                    <div class="status-item">
                        <div class="status-indicator status-{{ $systemStatus['database']['status'] }}"></div>
                        <div class="status-content">
                            <h6>Database</h6>
                            <p class="text-{{ $systemStatus['database']['status'] === 'online' ? 'success' : ($systemStatus['database']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                {{ $systemStatus['database']['message'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Email Service Status -->
                    <div class="status-item">
                        <div class="status-indicator status-{{ $systemStatus['email']['status'] }}"></div>
                        <div class="status-content">
                            <h6>Email Service</h6>
                            <p class="text-{{ $systemStatus['email']['status'] === 'online' ? 'success' : ($systemStatus['email']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                {{ $systemStatus['email']['message'] }}
                            </p>
                        </div>
                        @if($systemStatus['email']['status'] !== 'online')
                            <div class="status-alert">
                                <i class="fas fa-exclamation-triangle text-{{ $systemStatus['email']['status'] === 'warning' ? 'warning' : 'danger' }}"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Zoom Integration Status -->
                    <div class="status-item">
                        <div class="status-indicator status-{{ $systemStatus['zoom']['status'] }}"></div>
                        <div class="status-content">
                            <h6>Zoom Integration</h6>
                            <p class="text-{{ $systemStatus['zoom']['status'] === 'online' ? 'success' : ($systemStatus['zoom']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                {{ $systemStatus['zoom']['message'] }}
                            </p>
                        </div>
                        @if($systemStatus['zoom']['status'] !== 'online')
                            <div class="status-alert">
                                <i class="fas fa-exclamation-triangle text-{{ $systemStatus['zoom']['status'] === 'warning' ? 'warning' : 'danger' }}"></i>
                            </div>
                        @endif
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
    
    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .stat-card-link:hover {
        text-decoration: none;
        color: inherit;
    }
    
    .stat-card-link:hover .stat-card {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
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
    .stat-card-danger::before { background: linear-gradient(90deg, #dc3545, #e83e8c); }
    .stat-card-secondary::before { background: linear-gradient(90deg, #6c757d, #495057); }
    
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
    .stat-card-danger .stat-icon { background: linear-gradient(135deg, #dc3545, #e83e8c); }
    .stat-card-secondary .stat-icon { background: linear-gradient(135deg, #6c757d, #495057); }
    
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
    .quick-action-dark:hover { border-color: #343a40; }
    
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
    .quick-action-dark .quick-action-icon { background: linear-gradient(135deg, #343a40, #495057); }
    
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
    
    .session-avatars {
        position: relative;
        margin-right: 1rem;
    }
    
    .session-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .session-avatars .session-avatar:last-child {
        position: absolute;
        top: 0;
        left: 20px;
        border: 3px solid white;
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
    
    .session-status-pending { background: #fff3cd; color: #856404; }
    .session-status-confirmed { background: #d1ecf1; color: #0c5460; }
    .session-status-completed { background: #d4edda; color: #155724; }
    .session-status-cancelled { background: #f8d7da; color: #721c24; }
    
    .session-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* System Status */
    .system-status {
        space-y: 1rem;
    }
    
    .status-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
        position: relative;
    }
    
    .status-item:last-child {
        border-bottom: none;
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 1rem;
        animation: pulse 2s infinite;
    }
    
    .status-online {
        background: #28a745;
        box-shadow: 0 0 8px rgba(40, 167, 69, 0.5);
    }
    
    .status-warning {
        background: #ffc107;
        box-shadow: 0 0 8px rgba(255, 193, 7, 0.5);
    }
    
    .status-error {
        background: #dc3545;
        box-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
        animation: pulseDanger 1.5s infinite;
    }
    
    .status-alert {
        margin-left: auto;
        font-size: 1.25rem;
    }
    
    .status-content h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }
    
    .status-content p {
        font-size: 0.8rem;
        margin: 0;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    /* Animations */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    @keyframes pulseDanger {
        0% { 
            opacity: 1; 
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
        }
        50% { 
            opacity: 0.6; 
            box-shadow: 0 0 16px rgba(220, 53, 69, 0.8);
        }
        100% { 
            opacity: 1; 
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
        }
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
