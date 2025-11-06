@extends('layouts.app')

@section('title', 'Bookings Reports')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-3"></i>Bookings Reports
            </h1>
            <p class="page-subtitle">Comprehensive analysis of booking patterns and performance metrics</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Daily Statistics -->
<div class="modern-card mb-4">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-calendar-day me-2"></i>Daily Booking Statistics (Last 30 Days)
        </h5>
        <p class="modern-card-subtitle">Day-by-day breakdown of booking activity and performance</p>
            </div>
    <div class="modern-card-body">
        <div class="modern-table-container">
            <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Bookings</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Confirmed</th>
                        <th>Avg Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyStats as $day)
                                <tr>
                            <td>
                                <div class="date-info">
                                    <div class="date-main">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</div>
                                    <div class="date-day">{{ \Carbon\Carbon::parse($day->date)->format('l') }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="total-count">{{ $day->total_bookings }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-completed">{{ $day->completed_bookings }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-cancelled">{{ $day->cancelled_bookings }}</span>
                                    </td>
                                    <td>
                                <span class="status-badge status-confirmed">{{ $day->confirmed_bookings }}</span>
                                    </td>
                                    <td>
                                <div class="duration-display">
                                    <i class="fas fa-clock me-1"></i>{{ number_format($day->avg_duration_minutes, 1) }}m
                                </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
        </div>
    </div>
</div>

<!-- Weekly Statistics -->
<div class="modern-card mb-4">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-calendar-week me-2"></i>Weekly Booking Statistics (Current Year)
        </h5>
        <p class="modern-card-subtitle">Weekly trends and patterns in booking activity</p>
            </div>
    <div class="modern-card-body">
        <div class="modern-table-container">
            <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>Total Bookings</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Confirmed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklyStats as $week)
                                <tr>
                            <td>
                                <div class="week-info">
                                    <div class="week-main">Week {{ $week->week }}</div>
                                    <div class="week-year">{{ $week->year }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="total-count">{{ $week->total_bookings }}</span>
                            </td>
                            <td>
                                <span class="status-badge status-completed">{{ $week->completed_bookings }}</span>
                                    </td>
                                    <td>
                                <span class="status-badge status-cancelled">{{ $week->cancelled_bookings }}</span>
                                    </td>
                                    <td>
                                <span class="status-badge status-confirmed">{{ $week->confirmed_bookings }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
        </div>
    </div>
</div>

<!-- Booking Trends Chart -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-line me-2"></i>Booking Trends (Last 30 Days)
                </h5>
                <p class="modern-card-subtitle">Visual representation of booking patterns over time</p>
            </div>
            <div class="modern-card-body">
                <canvas id="bookingTrendsChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>

    <!-- Booking Status Distribution -->
    <div class="col-xl-4 col-lg-5">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-pie me-2"></i>Booking Status Distribution
                </h5>
                <p class="modern-card-subtitle">Current status breakdown of all bookings</p>
            </div>
            <div class="modern-card-body">
                <canvas id="bookingStatusChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Booking Completion Analysis -->
<div class="modern-card mb-4">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-analytics me-2"></i>Booking Completion Analysis
        </h5>
        <p class="modern-card-subtitle">Detailed breakdown of booking status distribution and performance</p>
            </div>
    <div class="modern-card-body">
        <div class="modern-table-container">
            <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                        <th>Average Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalBookings = $bookingCompletion->sum('count');
                            @endphp
                            @foreach($bookingCompletion as $status)
                                @php
                                    $percentage = $totalBookings > 0 ? round(($status->count / $totalBookings) * 100, 2) : 0;
                                @endphp
                                <tr>
                                    <td>
                                <div class="status-display">
                                    <span class="status-indicator 
                                        @if($status->status === 'completed') status-completed
                                        @elseif($status->status === 'cancelled') status-cancelled
                                        @elseif($status->status === 'confirmed') status-confirmed
                                        @elseif($status->status === 'pending') status-pending
                                        @else status-other
                                            @endif">
                                        </span>
                                    <span class="status-name">{{ ucfirst($status->status) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="count-display">{{ $status->count }}</span>
                                    </td>
                                    <td>
                                <div class="percentage-display">
                                    <div class="modern-progress">
                                            <div class="progress-bar 
                                                @if($status->status === 'completed') bg-success
                                                @elseif($status->status === 'cancelled') bg-danger
                                                @elseif($status->status === 'confirmed') bg-primary
                                                @elseif($status->status === 'pending') bg-warning
                                                @else bg-secondary
                                                @endif" 
                                                role="progressbar" style="width: {{ $percentage }}%">
                                            </div>
                                    </div>
                                    <span class="percentage-value">{{ $percentage }}%</span>
                                </div>
                            </td>
                            <td>
                                <div class="duration-display">
                                    <i class="fas fa-clock me-1"></i>{{ number_format($status->avg_duration, 1) }}m
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
        </div>
    </div>
</div>

<!-- Booking Feedback Summary -->
<div class="modern-card mb-4">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-star me-2"></i>Booking Feedback Summary
        </h5>
        <p class="modern-card-subtitle">Student feedback and ratings for completed bookings</p>
            </div>
    <div class="modern-card-body">
        <div class="modern-table-container">
            <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Average Rating</th>
                                <th>Feedback Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookingFeedback as $feedback)
                                <tr>
                            <td>
                                <div class="booking-id">
                                    <i class="fas fa-hashtag me-1"></i>{{ $feedback->booking_id }}
                                </div>
                            </td>
                            <td>
                                <div class="rating-display">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->avg_rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="rating-badge 
                                        @if($feedback->avg_rating >= 4.5) rating-excellent
                                        @elseif($feedback->avg_rating >= 3.5) rating-good
                                        @else rating-poor
                                            @endif">
                                            {{ number_format($feedback->avg_rating, 1) }}/5
                                        </span>
                                </div>
                            </td>
                            <td>
                                <span class="feedback-count">{{ $feedback->feedback_count }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    
    .modern-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Date Info */
    .date-info {
        display: flex;
        flex-direction: column;
    }
    
    .date-main {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    .date-day {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.125rem;
    }
    
    /* Week Info */
    .week-info {
        display: flex;
        flex-direction: column;
    }
    
    .week-main {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    .week-year {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.125rem;
    }
    
    /* Total Count */
    .total-count {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    
    /* Status Badges */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .status-confirmed {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .status-other {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
    }
    
    /* Duration Display */
    .duration-display {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .duration-display i {
        color: #007bff;
    }
    
    /* Status Display */
    .status-display {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .status-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    /* Count Display */
    .count-display {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    /* Percentage Display */
    .percentage-display {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 120px;
    }
    
    .modern-progress {
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        overflow: hidden;
    }
    
    .modern-progress .progress-bar {
        border-radius: 4px;
        transition: width 0.6s ease;
    }
    
    .percentage-value {
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
        text-align: center;
    }
    
    /* Booking ID */
    .booking-id {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    .booking-id i {
        color: #007bff;
    }
    
    /* Rating Display */
    .rating-display {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .rating-stars {
        display: flex;
        gap: 0.125rem;
    }
    
    .rating-stars i {
        font-size: 0.875rem;
    }
    
    .rating-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
    }
    
    .rating-excellent {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .rating-good {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .rating-poor {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    /* Feedback Count */
    .feedback-count {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
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
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .percentage-display {
            min-width: 100px;
        }
        
        .rating-display {
            align-items: flex-start;
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
        
        .modern-table th,
        .modern-table td {
            padding: 0.5rem 0.25rem;
        }
        
        .status-display {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
        
        .rating-display {
            gap: 0.25rem;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Booking Trends Chart
const trendsCtx = document.getElementById('bookingTrendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: @json($bookingTrends->pluck('date')),
        datasets: [{
            label: 'Total Bookings',
            data: @json($bookingTrends->pluck('total_bookings')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Completed',
            data: @json($bookingTrends->pluck('completed')),
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            tension: 0.1
        }, {
            label: 'Cancelled',
            data: @json($bookingTrends->pluck('cancelled')),
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Booking Status Distribution Chart
const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($bookingCompletion->pluck('status')),
        datasets: [{
            data: @json($bookingCompletion->pluck('count')),
            backgroundColor: [
                '#28a745', // completed
                '#ffc107', // pending
                '#dc3545', // cancelled
                '#17a2b8', // confirmed
                '#6c757d'  // no_show
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection