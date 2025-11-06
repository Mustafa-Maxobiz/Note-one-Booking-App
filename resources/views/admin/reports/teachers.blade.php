@extends('layouts.app')

@section('title', 'Teachers Reports')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chalkboard-teacher me-3"></i>Teachers Reports
            </h1>
            <p class="page-subtitle">Comprehensive analytics and insights about teacher performance</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Utilization Rates -->
<div class="row mb-4">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-line me-2"></i>Teacher Utilization Rates
                </h5>
                <p class="modern-card-subtitle">Track how efficiently teachers are utilizing their available time</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Available Hours</th>
                                <th>Booked Hours</th>
                                <th>Utilization Rate</th>
                                <th>Total Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherUtilization as $teacher)
                                <tr>
                                    <td>{{ $teacher->user?->name ?? 'Unknown Teacher' }}</td>
                                    <td>{{ $teacher->total_available_hours }}h</td>
                                    <td>{{ $teacher->total_booked_hours }}h</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $teacher->utilization_rate >= 80 ? 'bg-success' : ($teacher->utilization_rate >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" style="width: {{ $teacher->utilization_rate }}%">
                                                {{ $teacher->utilization_rate }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $teacher->total_bookings }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Acceptance Rates -->
<div class="row mb-4">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-check-circle me-2"></i>Teacher Acceptance Rates
                </h5>
                <p class="modern-card-subtitle">Track how teachers respond to booking requests</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Total Requests</th>
                                <th>Accepted</th>
                                <th>Declined</th>
                                <th>Acceptance Rate</th>
                                <th>Decline Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherAcceptance as $teacher)
                                <tr>
                                    <td>{{ $teacher->user?->name ?? 'Unknown Teacher' }}</td>
                                    <td>{{ $teacher->total_requests }}</td>
                                    <td>{{ $teacher->accepted_bookings }}</td>
                                    <td>{{ $teacher->declined_bookings }}</td>
                                    <td>
                                        <span class="badge {{ $teacher->acceptance_rate >= 80 ? 'bg-success' : ($teacher->acceptance_rate >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $teacher->acceptance_rate }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $teacher->decline_rate <= 20 ? 'bg-success' : ($teacher->decline_rate <= 40 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $teacher->decline_rate }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Response Times -->
<div class="row mb-4">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-clock me-2"></i>Teacher Response Times
                </h5>
                <p class="modern-card-subtitle">Average time taken by teachers to respond to booking requests</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Average Response Time</th>
                                <th>Response Time (Hours)</th>
                                <th>Total Responses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherResponseTimes as $teacher)
                                <tr>
                                    <td>{{ $teacher->name }}</td>
                                    <td>{{ $teacher->avg_response_time_minutes }} minutes</td>
                                    <td>
                                        <span class="badge {{ $teacher->avg_response_time_hours <= 2 ? 'bg-success' : ($teacher->avg_response_time_hours <= 24 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $teacher->avg_response_time_hours }}h
                                        </span>
                                    </td>
                                    <td>{{ $teacher->total_responses }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Performance -->
<div class="row mb-4">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-star me-2"></i>Teacher Performance Summary
                </h5>
                <p class="modern-card-subtitle">Overall performance metrics and earnings for each teacher</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Total Bookings</th>
                                <th>Completed</th>
                                <th>Completion Rate</th>
                                <th>Average Rating</th>
                                <th>Total Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherPerformance as $teacher)
                                <tr>
                                    <td>{{ $teacher->user?->name ?? 'Unknown Teacher' }}</td>
                                    <td>{{ $teacher->total_bookings }}</td>
                                    <td>{{ $teacher->completed_bookings }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $teacher->completion_rate >= 80 ? 'bg-success' : ($teacher->completion_rate >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" style="width: {{ $teacher->completion_rate }}%">
                                                {{ $teacher->completion_rate }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($teacher->feedback_avg_rating)
                                            <span class="badge bg-primary">{{ number_format($teacher->feedback_avg_rating, 1) }}/5</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($teacher->payments_sum_amount ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        margin: 0;
        font-size: 0.95rem;
    }
    
    .modern-table thead {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }
    
    .modern-table thead th {
        border: none;
        font-weight: 600;
        color: white;
        padding: 1.25rem 1rem;
        text-align: left;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
    }
    
    .modern-table thead th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
    }
    
    .modern-table tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
        color: #495057;
    }
    
    .modern-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .modern-table tbody tr:nth-child(even) {
        background: #fafbfc;
    }
    
    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Teacher Info */
    .teacher-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .teacher-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    
    .teacher-details h6 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .teacher-details small {
        color: #6c757d;
    }
    
    /* Progress Bar */
    .progress {
        height: 12px;
        border-radius: 20px;
        background: #f1f3f4;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .progress-bar {
        height: 100%;
        border-radius: 20px;
        transition: width 0.6s ease;
        position: relative;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0%, transparent 50%, rgba(255, 255, 255, 0.2) 100%);
        border-radius: 20px;
    }
    
    .progress-bar.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .progress-bar.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: #212529;
        text-shadow: none;
    }
    
    .progress-bar.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    }
    
    .progress-bar.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    }
    
    /* Badge Styling */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .badge:hover::before {
        left: 100%;
    }
    
    .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%) !important;
        color: white !important;
    }
    
    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        color: white !important;
    }
    
    .badge.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
        color: #212529 !important;
    }
    
    .badge.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
        color: white !important;
    }
    
    .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
        color: white !important;
    }
    
    /* Button Styling */
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-light {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .btn-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
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
        
        .teacher-info {
            flex-direction: column;
            text-align: center;
        }
        
        .teacher-avatar {
            margin: 0 auto;
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
        
        .table thead th,
        .table tbody td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endsection
