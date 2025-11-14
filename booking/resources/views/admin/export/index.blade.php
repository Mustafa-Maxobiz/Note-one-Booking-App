@extends('layouts.app')

@section('title', 'Export Data')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-download me-3"></i>Export Data
            </h1>
            <p class="page-subtitle">Export your platform data in various formats for analysis and backup</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <span class="export-info">
                    <i class="fas fa-info-circle me-2"></i>Data Export
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-download me-2"></i>Export Data
                    </h5>
                    <p class="modern-card-subtitle">Choose what data you want to export and in which format</p>
                </div>
                <div class="modern-card-body">

                    <div class="row">

                        <!-- Bookings Export -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="modern-card h-100">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-calendar-check me-2"></i>Export Bookings
                                    </h5>
                                    <p class="modern-card-subtitle">Export booking data and session information</p>
                                </div>
                                <div class="modern-card-body">

                                    <p class="card-text">Export booking data with filters for date range and status.</p>

                                    <form action="{{ route('admin.export.bookings') }}" method="GET" target="_blank">

                                        <div class="mb-3">

                                            <label for="booking_date_from" class="form-label">Date From</label>
                                            <input type="date" class="form-control" id="booking_date_from" name="date_from">

                                        </div>

                                        <div class="mb-3">

                                            <label for="booking_date_to" class="form-label">Date To</label>
                                            <input type="date" class="form-control" id="booking_date_to" name="date_to">

                                        </div>

                                        <div class="mb-3">

                                            <label for="booking_status" class="form-label">Status</label>
                                            <select class="form-select" id="booking_status" name="status">

                                                <option value="">All Statuses</option>

                                                <option value="pending">Pending</option>

                                                <option value="accepted">Accepted</option>

                                                <option value="declined">Declined</option>

                                                <option value="completed">Completed</option>

                                                <option value="cancelled">Cancelled</option>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label for="booking_format" class="form-label">Format</label>
                                            <select class="form-select" id="booking_format" name="format">

                                                <option value="csv">CSV</option>

                                                <option value="json">JSON</option>

                                                <option value="excel">Excel</option>

                                            </select>

                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-download me-2"></i>
                                            Export Bookings
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>



                        <!-- Users Export -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="modern-card h-100">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-users me-2"></i>Export Users
                                    </h5>
                                    <p class="modern-card-subtitle">Export user data with role-based filtering</p>
                                </div>
                                <div class="modern-card-body">

                                    <p class="card-text">Export user data with role-based filtering.</p>

                                    <form action="{{ route('admin.export.users') }}" method="GET" target="_blank">

                                        <div class="mb-3">

                                            <label for="user_role" class="form-label">Role</label>

                                            <select class="form-select" id="user_role" name="role">

                                                <option value="">All Roles</option>

                                                <option value="admin">Admin</option>

                                                <option value="teacher">Teacher</option>

                                                <option value="student">Student</option>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label for="user_format" class="form-label">Format</label>

                                            <select class="form-select" id="user_format" name="format">

                                                <option value="csv">CSV</option>

                                                <option value="json">JSON</option>

                                                <option value="excel">Excel</option>

                                            </select>

                                        </div>

                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-download me-2"></i>
                                            Export Users
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>



                        <!-- Payments Export -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="modern-card h-100">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-credit-card me-2"></i>Export Payments
                                    </h5>
                                    <p class="modern-card-subtitle">Export payment data with status and date filters</p>
                                </div>
                                <div class="modern-card-body">

                                    <p class="card-text">Export payment data with status and date filters.</p>

                                    <form action="{{ route('admin.export.payments') }}" method="GET" target="_blank">

                                        <div class="mb-3">

                                            <label for="payment_status" class="form-label">Status</label>

                                            <select class="form-select" id="payment_status" name="status">

                                                <option value="">All Statuses</option>

                                                <option value="pending">Pending</option>

                                                <option value="completed">Completed</option>

                                                <option value="failed">Failed</option>

                                                <option value="refunded">Refunded</option>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label for="payment_date_from" class="form-label">Date From</label>

                                            <input type="date" class="form-control" id="payment_date_from" name="date_from">

                                        </div>

                                        <div class="mb-3">

                                            <label for="payment_date_to" class="form-label">Date To</label>

                                            <input type="date" class="form-control" id="payment_date_to" name="date_to">

                                        </div>

                                        <div class="mb-3">

                                            <label for="payment_format" class="form-label">Format</label>

                                            <select class="form-select" id="payment_format" name="format">

                                                <option value="csv">CSV</option>

                                                <option value="json">JSON</option>

                                                <option value="excel">Excel</option>

                                            </select>

                                        </div>

                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-download me-2"></i>
                                            Export Payments
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>



                        <!-- Feedback Export -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="modern-card h-100">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-star me-2"></i>Export Feedback
                                    </h5>
                                    <p class="modern-card-subtitle">Export feedback and ratings data</p>
                                </div>
                                <div class="modern-card-body">

                                    <p class="card-text">Export feedback and ratings data.</p>

                                    <form action="{{ route('admin.export.feedback') }}" method="GET" target="_blank">

                                        <div class="mb-3">

                                            <label for="feedback_type" class="form-label">Type</label>

                                            <select class="form-select" id="feedback_type" name="type">

                                                <option value="">All Types</option>

                                                <option value="student_to_teacher">Student to Teacher</option>

                                                <option value="teacher_to_student">Teacher to Student</option>

                                            </select>

                                        </div>

                                        <div class="mb-3">

                                            <label for="feedback_format" class="form-label">Format</label>

                                            <select class="form-select" id="feedback_format" name="format">

                                                <option value="csv">CSV</option>

                                                <option value="json">JSON</option>

                                                <option value="excel">Excel</option>

                                            </select>

                                        </div>

                                        <button type="submit" class="btn btn-info w-100">
                                            <i class="fas fa-download me-2"></i>
                                            Export Feedback
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- Export Statistics -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-chart-bar me-2"></i>Export Statistics
                                    </h5>
                                    <p class="modern-card-subtitle">Overview of system data and metrics</p>
                                </div>
                                <div class="modern-card-body">

                                    <div class="row">

                                        <div class="col-md-3">

                                            <div class="text-center">

                                                <h4 class="text-primary">{{ \App\Models\Session::count() }}</h4>

                                                <p class="text-muted">Total Bookings</p>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <div class="text-center">

                                                <h4 class="text-success">{{ \App\Models\User::count() }}</h4>

                                                <p class="text-muted">Total Users</p>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <div class="text-center">

                                                <h4 class="text-warning">{{ \App\Models\Payment::count() }}</h4>

                                                <p class="text-muted">Total Payments</p>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <div class="text-center">

                                                <h4 class="text-info">{{ \App\Models\Feedback::count() }}</h4>

                                                <p class="text-muted">Total Feedback</p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

    // Set default dates for better UX

    document.addEventListener('DOMContentLoaded', function() {

        const today = new Date();

        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

        

        // Set default date ranges (fixed timezone issue)

        // Helper function to format date in local timezone
        function formatLocalDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        document.getElementById('booking_date_from').value = formatLocalDate(lastMonth);

        document.getElementById('booking_date_to').value = formatLocalDate(today);

        document.getElementById('payment_date_from').value = formatLocalDate(lastMonth);

        document.getElementById('payment_date_to').value = formatLocalDate(today);

    });

</script>

@endpush

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
    
    .export-info {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
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
    
    /* Export Cards */
    .export-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .export-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .export-card-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        padding: 1.5rem;
        color: white;
    }
    
    .export-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .export-card-body {
        padding: 1.5rem;
    }
    
    .export-card-text {
        color: #6c757d;
        margin-bottom: 1rem;
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
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
        color: white;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        color: white;
    }
    
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(23, 162, 184, 0.3);
        color: white;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #212529;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(255, 193, 7, 0.3);
        color: #212529;
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
        .modern-card-body,
        .export-card-header,
        .export-card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

