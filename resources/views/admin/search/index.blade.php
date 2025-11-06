@extends('layouts.app')

@section('title', 'Search')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-search me-3"></i>Search System
            </h1>
            <p class="page-subtitle">Search and filter users, bookings, and content across the platform</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <span class="search-stats">
                    <i class="fas fa-chart-bar me-2"></i>Advanced Search
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
                        <i class="fas fa-search me-2"></i>Search System
                    </h5>
                    <p class="modern-card-subtitle">Find users, bookings, and content quickly and efficiently</p>
                </div>
                <div class="modern-card-body">

                    <!-- Quick Search -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-search me-2"></i>Quick Search
                                    </h5>
                                    <p class="modern-card-subtitle">Search across all content types</p>
                                </div>
                                <div class="modern-card-body">

                                    <form action="{{ route('admin.search') }}" method="POST">

                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4">

                                                <div class="mb-3">

                                                    <label for="query" class="form-label">Search Query</label>

                                                    <input type="text" class="form-control" id="query" name="query" 

                                                           placeholder="Enter search term..." value="{{ old('query') }}">

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <div class="mb-3">

                                                    <label for="type" class="form-label">Search Type</label>

                                                    <select class="form-select" id="type" name="type">

                                                        <option value="all" {{ old('type') == 'all' ? 'selected' : '' }}>All</option>

                                                        <option value="bookings" {{ old('type') == 'bookings' ? 'selected' : '' }}>Bookings</option>

                                                        <option value="users" {{ old('type') == 'users' ? 'selected' : '' }}>Users</option>

                                                        <option value="payments" {{ old('type') == 'payments' ? 'selected' : '' }}>Payments</option>

                                                        <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>Feedback</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="date_from" class="form-label">Date From</label>

                                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ old('date_from') }}">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="date_to" class="form-label">Date To</label>

                                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ old('date_to') }}">

                                                </div>

                                            </div>

                                            <div class="col-md-1">

                                                <div class="mb-3">

                                                    <label class="form-label">&nbsp;</label>

                                                    <button type="submit" class="btn btn-primary w-100">

                                                        <i class="fas fa-search"></i>

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- Advanced Search -->
                    <div class="row">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-cogs me-2"></i>Advanced Search
                                    </h5>
                                    <p class="modern-card-subtitle">Use advanced filters to find specific records</p>
                                </div>
                                <div class="modern-card-body">

                                    <form action="{{ route('admin.search.advanced') }}" method="POST">

                                        @csrf

                                        

                                        <!-- Bookings Search -->

                                        <div class="row mb-4">

                                            <div class="col-12">

                                                <h6 class="text-primary">

                                                    <i class="fas fa-calendar-check me-2"></i>

                                                    Bookings Search

                                                </h6>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="booking_status" class="form-label">Status</label>

                                                    <select class="form-select" id="booking_status" name="booking_status">

                                                        <option value="">All</option>

                                                        <option value="pending">Pending</option>

                                                        <option value="accepted">Accepted</option>

                                                        <option value="declined">Declined</option>

                                                        <option value="completed">Completed</option>

                                                        <option value="cancelled">Cancelled</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="booking_date_from" class="form-label">Date From</label>

                                                    <input type="date" class="form-control" id="booking_date_from" name="booking_date_from">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="booking_date_to" class="form-label">Date To</label>

                                                    <input type="date" class="form-control" id="booking_date_to" name="booking_date_to">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label class="form-label">&nbsp;</label>

                                                    <button type="submit" name="search_bookings" value="1" class="btn btn-primary w-100">

                                                        Search Bookings

                                                    </button>

                                                </div>

                                            </div>

                                        </div>



                                        <!-- Users Search -->

                                        <div class="row mb-4">

                                            <div class="col-12">

                                                <h6 class="text-success">

                                                    <i class="fas fa-users me-2"></i>

                                                    Users Search

                                                </h6>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="user_role" class="form-label">Role</label>

                                                    <select class="form-select" id="user_role" name="user_role">

                                                        <option value="">All</option>

                                                        <option value="admin">Admin</option>

                                                        <option value="teacher">Teacher</option>

                                                        <option value="student">Student</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="user_status" class="form-label">Email Status</label>

                                                    <select class="form-select" id="user_status" name="user_status">

                                                        <option value="">All</option>

                                                        <option value="verified">Verified</option>

                                                        <option value="unverified">Unverified</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="user_name" class="form-label">Name</label>

                                                    <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Search by name">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="user_email" class="form-label">Email</label>

                                                    <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Search by email">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="user_date_from" class="form-label">Created From</label>

                                                    <input type="date" class="form-control" id="user_date_from" name="user_date_from">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label class="form-label">&nbsp;</label>

                                                    <button type="submit" name="search_users" value="1" class="btn btn-success w-100">

                                                        Search Users

                                                    </button>

                                                </div>

                                            </div>

                                        </div>



                                        <!-- Teacher-Specific Search -->

                                        <div class="row mb-4">

                                            <div class="col-12">

                                                <h6 class="text-info">

                                                    <i class="fas fa-chalkboard-teacher me-2"></i>

                                                    Teacher-Specific Search

                                                </h6>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="teacher_verification" class="form-label">Verification</label>

                                                    <select class="form-select" id="teacher_verification" name="teacher_verification">

                                                        <option value="">All Teachers</option>

                                                        <option value="verified">Verified Only</option>

                                                        <option value="unverified">Unverified Only</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="teacher_availability" class="form-label">Availability</label>

                                                    <select class="form-select" id="teacher_availability" name="teacher_availability">

                                                        <option value="">All Teachers</option>

                                                        <option value="available">Available Only</option>

                                                        <option value="unavailable">Unavailable Only</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="teacher_qualifications" class="form-label">Qualifications</label>

                                                    <input type="text" class="form-control" id="teacher_qualifications" name="teacher_qualifications" placeholder="Search qualifications">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label class="form-label">&nbsp;</label>

                                                    <button type="submit" name="search_users" value="1" class="btn btn-info w-100">

                                                        Search Teachers

                                                    </button>

                                                </div>

                                            </div>

                                        </div>



                                        <!-- Payments Search -->

                                        <div class="row mb-4">

                                            <div class="col-12">

                                                <h6 class="text-warning">

                                                    <i class="fas fa-credit-card me-2"></i>

                                                    Payments Search

                                                </h6>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="payment_status" class="form-label">Status</label>

                                                    <select class="form-select" id="payment_status" name="payment_status">

                                                        <option value="">All</option>

                                                        <option value="pending">Pending</option>

                                                        <option value="completed">Completed</option>

                                                        <option value="failed">Failed</option>

                                                        <option value="refunded">Refunded</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="payment_method" class="form-label">Method</label>

                                                    <select class="form-select" id="payment_method" name="payment_method">

                                                        <option value="">All</option>

                                                        <option value="credit_card">Credit Card</option>

                                                        <option value="paypal">PayPal</option>

                                                        <option value="bank_transfer">Bank Transfer</option>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="payment_amount_min" class="form-label">Min Amount</label>

                                                    <input type="number" class="form-control" id="payment_amount_min" name="payment_amount_min" step="0.01">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="payment_amount_max" class="form-label">Max Amount</label>

                                                    <input type="number" class="form-control" id="payment_amount_max" name="payment_amount_max" step="0.01">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label for="payment_date_from" class="form-label">Date From</label>

                                                    <input type="date" class="form-control" id="payment_date_from" name="payment_date_from">

                                                </div>

                                            </div>

                                            <div class="col-md-2">

                                                <div class="mb-3">

                                                    <label class="form-label">&nbsp;</label>

                                                    <button type="submit" name="search_payments" value="1" class="btn btn-warning w-100">

                                                        Search Payments

                                                    </button>

                                                </div>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- Search Tips -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="modern-card">
                                <div class="modern-card-header">
                                    <h5 class="modern-card-title">
                                        <i class="fas fa-lightbulb me-2"></i>Search Tips
                                    </h5>
                                    <p class="modern-card-subtitle">Learn how to search more effectively</p>
                                </div>
                                <div class="modern-card-body">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <h6>Quick Search:</h6>

                                            <ul>

                                                <li>Enter any text to search across all fields</li>

                                                <li>Use date ranges to filter by time period</li>

                                                <li>Select specific types to narrow results</li>

                                                <li><strong>Teacher Search:</strong> Type "verified", "unverified", "available", or "unavailable"</li>

                                                <li>Search teacher bio, qualifications, and hourly rates</li>

                                            </ul>

                                        </div>

                                        <div class="col-md-6">

                                            <h6>Advanced Search:</h6>

                                            <ul>

                                                <li>Use specific filters for precise results</li>

                                                <li>Combine multiple criteria for better accuracy</li>

                                                <li>Search individual sections separately</li>

                                                <li><strong>Teacher Filters:</strong> Verification, availability, hourly rate range</li>

                                                <li>Search by qualifications and teacher-specific fields</li>

                                            </ul>

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

        document.getElementById('date_from').value = formatLocalDate(lastMonth);

        document.getElementById('date_to').value = formatLocalDate(today);

        document.getElementById('booking_date_from').value = formatLocalDate(lastMonth);

        document.getElementById('booking_date_to').value = formatLocalDate(today);

        document.getElementById('payment_date_from').value = formatLocalDate(lastMonth);

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
    
    .search-stats {
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
    
    .btn-outline-primary {
        border: 2px solid #ef473e;
        color: #ef473e;
        background: transparent;
    }
    
    .btn-outline-primary:hover {
        background: #ef473e;
        color: white;
        border-color: #ef473e;
    }
    
    /* Search Results */
    .search-result-item {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .search-result-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
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

