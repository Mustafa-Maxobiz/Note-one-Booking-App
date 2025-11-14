@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chart-bar me-3"></i>Reports & Analytics
            </h1>
            <p class="page-subtitle">Comprehensive insights into your booking platform performance</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
        <div class="btn-group me-2">
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-light">
                        <i class="fas fa-dollar-sign me-2"></i>Revenue
                    </a>
                    <a href="{{ route('admin.reports.teachers') }}" class="btn btn-outline-light">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Teachers
                    </a>
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-outline-light">
                        <i class="fas fa-user-graduate me-2"></i>Students
                    </a>
                    <a href="{{ route('admin.reports.bookings') }}" class="btn btn-outline-light">
                        <i class="fas fa-calendar-alt me-2"></i>Bookings
                    </a>
        </div>
                <button type="button" class="btn btn-light" onclick="clearReportCache()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
        </button>
            </div>
        </div>
    </div>
</div>



<!-- Overview Cards -->
<div class="stats-grid mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
                    </div>
        <div class="stat-content">
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ number_format($basicStats->totalUsers) }}</div>
                    </div>
                </div>

    <div class="stat-card stat-card-success">
        <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
            </div>
        <div class="stat-content">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($basicStats->totalRevenue, 2) }}</div>
        </div>
    </div>

    <div class="stat-card stat-card-info">
        <div class="stat-icon">
            <i class="fas fa-calendar-alt"></i>
                    </div>
        <div class="stat-content">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value">{{ number_format($basicStats->totalBookings) }}</div>
                    </div>
                </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-icon">
            <i class="fas fa-star"></i>
            </div>
        <div class="stat-content">
            <div class="stat-label">Average Rating</div>
            <div class="stat-value">{{ number_format($basicStats->averageRating, 1) }}/5</div>
        </div>
    </div>
</div>



<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Trends Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-line me-2"></i>Revenue Trends (Last 30 Days)
                </h5>
                <p class="modern-card-subtitle">Daily revenue performance over the past month</p>
            </div>
            <div class="modern-card-body">
                <canvas id="revenueChart" width="100%" height="40"></canvas>
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
                <p class="modern-card-subtitle">Current booking status overview</p>
            </div>
            <div class="modern-card-body">
                <canvas id="bookingStatusChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
</div>



<!-- Data Tables Row -->
<div class="row">
    <!-- Top Performing Teachers -->
    <div class="col-lg-6 mb-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-trophy me-2"></i>Top Performing Teachers
                </h5>
                <p class="modern-card-subtitle">Teachers with highest bookings and ratings</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Bookings</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topTeachers as $teacher)
                                <tr>
                                    <td>
                                        <div class="teacher-info">
                                            <div class="teacher-name">{{ $teacher->user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="booking-count">{{ $teacher->total_bookings }}</span>
                                    </td>
                                    <td>
                                        @if($teacher->feedback_avg_rating)
                                            <div class="rating-display">
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $teacher->feedback_avg_rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <div class="rating-value">{{ number_format($teacher->feedback_avg_rating, 1) }}/5</div>
                                            </div>
                                        @else
                                            <span class="no-rating">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Utilization Rates -->
    <div class="col-lg-6 mb-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-bar me-2"></i>Teacher Utilization Rates
                </h5>
                <p class="modern-card-subtitle">How efficiently teachers are being utilized</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Utilization Rate</th>
                                <th>Booked Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherUtilization->take(5) as $teacher)
                                <tr>
                                    <td>
                                        <div class="teacher-info">
                                            <div class="teacher-name">{{ $teacher->user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="utilization-display">
                                            <div class="progress modern-progress">
                                            <div class="progress-bar {{ $teacher->utilization_rate >= 80 ? 'bg-success' : ($teacher->utilization_rate >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" style="width: {{ $teacher->utilization_rate }}%">
                                                </div>
                                            </div>
                                            <span class="utilization-percentage">{{ $teacher->utilization_rate }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="hours-count">{{ $teacher->total_booked_hours }}h</span>
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



<!-- Additional Metrics Row -->
<div class="row mb-4">
    <!-- Teacher Acceptance Rates -->
    <div class="col-lg-6 mb-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-check-circle me-2"></i>Teacher Acceptance Rates
                </h5>
                <p class="modern-card-subtitle">How often teachers accept booking requests</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Acceptance Rate</th>
                                <th>Total Requests</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherAcceptance->take(5) as $teacher)
                                <tr>
                                    <td>
                                        <div class="teacher-info">
                                            <div class="teacher-name">{{ $teacher->user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="acceptance-badge {{ $teacher->acceptance_rate >= 80 ? 'badge-success' : ($teacher->acceptance_rate >= 60 ? 'badge-warning' : 'badge-danger') }}">
                                            {{ $teacher->acceptance_rate }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="requests-count">{{ $teacher->total_requests }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Subjects -->
    <div class="col-lg-6 mb-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-book me-2"></i>Popular Subjects
                </h5>
                <p class="modern-card-subtitle">Most requested subjects by students</p>
            </div>
            <div class="modern-card-body">
                <div class="modern-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($popularSubjects as $subject)
                                <tr>
                                    <td>
                                        <div class="subject-info">
                                            <div class="subject-name">{{ $subject->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="booking-count">{{ $subject->booking_count }}</span>
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
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
        border: none;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .stat-card-primary {
        border-left: 4px solid #007bff;
    }
    
    .stat-card-success {
        border-left: 4px solid #28a745;
    }
    
    .stat-card-info {
        border-left: 4px solid #17a2b8;
    }
    
    .stat-card-warning {
        border-left: 4px solid #ffc107;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-card-primary .stat-icon {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .stat-card-success .stat-icon {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .stat-card-info .stat-icon {
        background: linear-gradient(135deg, #17a2b8, #138496);
    }
    
    .stat-card-warning .stat-icon {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
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
    
    /* Teacher Info */
    .teacher-info {
        display: flex;
        align-items: center;
    }
    
    .teacher-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
    }
    
    /* Rating Display */
    .rating-display {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .rating-stars {
        display: flex;
        gap: 0.125rem;
    }
    
    .rating-stars i {
        font-size: 0.875rem;
    }
    
    .rating-value {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .no-rating {
        color: #6c757d;
        font-style: italic;
        font-size: 0.85rem;
    }
    
    /* Booking Count */
    .booking-count {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    /* Utilization Display */
    .utilization-display {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
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
    
    .utilization-percentage {
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
        text-align: center;
    }
    
    .hours-count {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    /* Acceptance Badge */
    .acceptance-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .requests-count {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    /* Subject Info */
    .subject-info {
        display: flex;
        align-items: center;
    }
    
    .subject-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.95rem;
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
        
        .header-actions .btn-group {
            justify-content: center;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .stat-card {
            padding: 1.5rem;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 1.5rem;
        }
        
        .stat-card {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// Revenue Trends Chart

const revenueCtx = document.getElementById('revenueChart').getContext('2d');

const revenueChart = new Chart(revenueCtx, {

    type: 'line',

    data: {

        labels: @json($revenueTrends->pluck('date')),

        datasets: [{

            label: 'Daily Revenue ($)',

            data: @json($revenueTrends->pluck('daily_revenue')),

            borderColor: 'rgb(75, 192, 192)',

            backgroundColor: 'rgba(75, 192, 192, 0.2)',

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

        labels: @json($bookingStatusDistribution->pluck('status')),

        datasets: [{

            data: @json($bookingStatusDistribution->pluck('count')),

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



function clearReportCache() {

    if (confirm('Are you sure you want to refresh all report data? This will clear cached data and may take a moment to reload.')) {

        fetch('{{ route("admin.reports.clear-cache") }}', {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                'Content-Type': 'application/json',

            },

        })

        .then(response => response.json())

        .then(data => {

            if (data.message) {

                // Show success message

                const alert = document.createElement('div');

                alert.className = 'alert alert-success alert-dismissible fade show';

                alert.innerHTML = `

                    <i class="fas fa-check-circle me-2"></i>${data.message}

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                `;

                document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.container-fluid').firstChild);

                

                // Reload the page after a short delay

                setTimeout(() => {

                    window.location.reload();

                }, 1500);

            }

        })

        .catch(error => {

            console.error('Error:', error);

            alert('Error clearing cache. Please try again.');

        });

    }

}

</script>

@endsection

