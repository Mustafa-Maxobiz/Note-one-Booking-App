@extends('layouts.app')

@section('title', 'Bookings Management')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-3"></i>Bookings Management
            </h1>
            <p class="page-subtitle">Manage all system bookings, reassignments, and cancellations</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Add New
                </a>
                <a href="{{ route('admin.bookings.trashed') }}" class="btn btn-danger btn-lg">
                    <i class="fas fa-trash me-2"></i>Trashed
                </a>
                <form method="POST" action="{{ route('admin.bookings.cleanup-orphaned') }}" class="d-inline d-none">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-lg" 
                            onclick="return confirm('This will delete all bookings with missing teacher or student relationships. Are you sure?')"
                            title="Clean up orphaned bookings">
                        <i class="fas fa-broom me-2"></i>Cleanup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="modern-card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="modern-card-title">
                    <i class="fas fa-list me-2"></i>All Bookings
                </h5>
                <p class="modern-card-subtitle">View and manage all system bookings</p>
            </div>
            <div class="col-md-6">
                <div class="search-container">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Search bookings...">
                        <button class="btn btn-outline-secondary search-clear" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modern-card-body">
        <!-- Bulk Actions Toolbar -->
        <div id="bulkActionsToolbar" class="bulk-actions-toolbar" style="display: none;">
            <div class="bulk-actions-content">
                <div class="bulk-actions-left">
                    <span class="bulk-selection-count">
                        <span id="selectedCount">0</span> booking(s) selected
                    </span>
                    <div class="bulk-actions-buttons">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDelete">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="bulkConfirm">
                            <i class="fas fa-check me-1"></i>Confirm
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="bulkCancel">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" id="bulkComplete">
                            <i class="fas fa-check-circle me-1"></i>Complete
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelection">
                    <i class="fas fa-times me-1"></i>Clear Selection
                </button>
            </div>
        </div>
        
        @if($bookings->count() > 0)
            <div class="modern-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="modern-checkbox">
                            </th>
                            <th>Teacher</th>
                            <th>Student</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentDate = null;
                            $groupedBookings = $bookings->groupBy(function($booking) {
                                return $booking->start_time->format('Y-m-d');
                            });
                        @endphp
                        @foreach($groupedBookings as $date => $dayBookings)
                            @php
                                $dateObj = \Carbon\Carbon::parse($date);
                                $isToday = $dateObj->isToday();
                                $isTomorrow = $dateObj->isTomorrow();
                                $isPast = $dateObj->isPast();
                            @endphp
                            
                            <!-- Date Group Header -->
                            <tr class="date-group-header {{ $isToday ? 'today-group' : ($isTomorrow ? 'tomorrow-group' : ($isPast ? 'past-group' : 'future-group')) }}">
                                <td colspan="8">
                                    <div class="date-group-content">
                                        <div class="date-group-info">
                                            @if($isToday)
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <span class="date-group-title">Today's Sessions</span>
                                                <span class="date-group-date">{{ $dateObj->format('M d, Y') }}</span>
                                            @elseif($isTomorrow)
                                                <i class="fas fa-calendar-plus text-info me-2"></i>
                                                <span class="date-group-title">Tomorrow's Sessions</span>
                                                <span class="date-group-date">{{ $dateObj->format('M d, Y') }}</span>
                                            @elseif($isPast)
                                                <i class="fas fa-history text-muted me-2"></i>
                                                <span class="date-group-title">Past Sessions</span>
                                                <span class="date-group-date">{{ $dateObj->format('M d, Y') }}</span>
                                            @else
                                                <i class="fas fa-calendar text-primary me-2"></i>
                                                <span class="date-group-title">Upcoming Sessions</span>
                                                <span class="date-group-date">{{ $dateObj->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                        <div class="date-group-count">
                                            <span class="badge bg-primary">{{ $dayBookings->count() }} session{{ $dayBookings->count() > 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            @foreach($dayBookings as $booking)
                            @php
                                $isToday = $booking->start_time->isToday();
                                $isTomorrow = $booking->start_time->isTomorrow();
                                $isPast = $booking->start_time->isPast();
                            @endphp
                            <tr class="booking-row {{ $isToday ? 'today-session' : ($isTomorrow ? 'tomorrow-session' : '') }} {{ $isPast ? 'past-session' : '' }}">
                                <td>
                                    <input type="checkbox" class="modern-checkbox booking-checkbox" value="{{ $booking->id }}">
                                </td>
                                <td>
                                    <div class="teacher-info">
                                        @if($booking->teacher && $booking->teacher->user)
                                            <div class="teacher-avatar">
                                                <img src="{{ $booking->teacher->user->small_profile_picture_url }}" 
                                                     alt="{{ $booking->teacher->user->name }}"
                                                     class="avatar-img">
                                            </div>
                                            <div class="teacher-details">
                                                <div class="teacher-name">{{ $booking->teacher->user->name }}</div>
                                                <div class="teacher-qualifications">{{ $booking->teacher->qualifications }}</div>
                                            </div>
                                        @else
                                            <div class="teacher-details">
                                                <div class="teacher-name text-muted">Teacher not found</div>
                                                <div class="teacher-qualifications">N/A</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="student-info">
                                        @if($booking->student && $booking->student->user)
                                            <div class="student-avatar">
                                                <img src="{{ $booking->student->user->small_profile_picture_url }}" 
                                                     alt="{{ $booking->student->user->name }}"
                                                     class="avatar-img">
                                            </div>
                                            <div class="student-details">
                                                <div class="student-name">{{ $booking->student->user->name }}</div>
                                                <div class="student-level">{{ $booking->student->level }} level</div>
                                            </div>
                                        @else
                                            <div class="student-details">
                                                <div class="student-name text-muted">Student not found</div>
                                                <div class="student-level">N/A</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="booking-datetime">
                                        <div class="date-primary">
                                            @if($isToday)
                                                <span class="today-badge">Today</span>
                                            @elseif($isTomorrow)
                                                <span class="tomorrow-badge">Tomorrow</span>
                                            @else
                                                {{ $booking->start_time->format('M d, Y') }}
                                            @endif
                                        </div>
                                        <div class="time-secondary">{{ $booking->start_time->format('g:i A') }}</div>
                                        @if($isToday)
                                            <div class="today-indicator">
                                                <i class="fas fa-star text-warning me-1"></i>
                                                <small class="text-warning fw-bold">Today's Session</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="duration-badge">{{ $booking->duration_minutes }} min</span>
                                </td>
                                <td>
                                    <div class="notes-container">
                                        <div class="notes-text">{{ $booking->notes }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="status-badge status-confirmed">
                                            <i class="fas fa-check-circle me-1"></i>Confirmed
                                        </span>
                                    @elseif($booking->status === 'completed')
                                        <span class="status-badge status-completed">
                                            <i class="fas fa-check-double me-1"></i>Completed
                                        </span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="status-badge status-cancelled">
                                            <i class="fas fa-times-circle me-1"></i>Cancelled
                                        </span>
                                    @elseif($booking->status === 'no_show')
                                        <span class="status-badge status-no-show">
                                            <i class="fas fa-user-times me-1"></i>No Show
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning action-btn" title="Edit Booking">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-info action-btn" data-bs-toggle="modal" data-bs-target="#reassignModal{{ $booking->id }}" title="Reassign Booking">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger action-btn" 
                                                title="Delete Booking" 
                                                onclick="showDeleteConfirmation(
                                                    '{{ $booking->id }} - {{ $booking->teacher->user->name ?? 'Unknown Teacher' }} & {{ $booking->student->user->name ?? 'Unknown Student' }}',
                                                    'Booking',
                                                    '{{ route('admin.bookings.delete') }}',
                                                    [],
                                                    { id: {{ $booking->id }} }
                                                )">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h5 class="empty-state-title">No bookings found</h5>
                <p class="empty-state-description">Start by adding your first booking to the system.</p>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Booking
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Reassign Modals -->
@foreach($bookings as $booking)
    <div class="modal fade" id="reassignModal{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-modal">
                <form method="POST" action="{{ route('admin.bookings.reassign', $booking) }}">
                    @csrf
                    <div class="modal-header modern-modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exchange-alt me-2"></i>Reassign Booking
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body modern-modal-body">
                        <div class="current-assignment mb-4">
                            <h6 class="mb-2">
                                <i class="fas fa-info-circle me-2"></i>Current Assignment
                            </h6>
                            <div class="assignment-info">
                                <span class="teacher-name">
                                    @if($booking->teacher && $booking->teacher->user)
                                        {{ $booking->teacher->user->name }}
                                    @else
                                        <span class="text-muted">Teacher not found</span>
                                    @endif
                                </span>
                                <i class="fas fa-arrow-right mx-2"></i>
                                <span class="student-name">
                                    @if($booking->student && $booking->student->user)
                                        {{ $booking->student->user->name }}
                                    @else
                                        <span class="text-muted">Student not found</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new_teacher_id_{{ $booking->id }}" class="form-label">
                                <i class="fas fa-chalkboard-teacher me-2"></i>New Teacher
                            </label>
                            <select class="form-select modern-select" id="new_teacher_id_{{ $booking->id }}" name="new_teacher_id" required>
                                <option value="">Select teacher</option>
                                @foreach(\App\Models\Teacher::with('user')->get() as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $teacher->id == $booking->teacher_id ? 'selected' : '' }}>
                                        @if($teacher->user)
                                            {{ $teacher->user->name }} ({{ $teacher->qualifications }})
                                        @else
                                            Teacher #{{ $teacher->id }} ({{ $teacher->qualifications }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new_student_id_{{ $booking->id }}" class="form-label">
                                <i class="fas fa-user-graduate me-2"></i>New Student
                            </label>
                            <select class="form-select modern-select" id="new_student_id_{{ $booking->id }}" name="new_student_id" required>
                                <option value="">Select student</option>
                                @foreach(\App\Models\Student::with('user')->get() as $student)
                                    <option value="{{ $student->id }}" {{ $student->id == $booking->student_id ? 'selected' : '' }}>
                                        @if($student->user)
                                            {{ $student->user->name }} ({{ $student->level }})
                                        @else
                                            Student #{{ $student->id }} ({{ $student->level }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if($errors->any())
                            <div class="alert alert-danger modern-alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer modern-modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-exchange-alt me-2"></i>Reassign Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
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
    
    /* Search Container */
    .search-container {
        width: 100%;
    }
    
    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .search-input-group:focus-within {
        border-color: #ef473e;
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.2);
    }
    
    .search-icon {
        position: absolute;
        left: 1rem;
        color: #6c757d;
        z-index: 2;
    }
    
    .search-input {
        border: none;
        padding: 0.75rem 1rem 0.75rem 3rem;
        font-size: 0.95rem;
        background: transparent;
        flex: 1;
        outline: none;
    }
    
    .search-clear {
        border: none;
        padding: 0.75rem 1rem;
        background: #6c757d;
        color: white;
        transition: all 0.3s ease;
    }
    
    .search-clear:hover {
        background: #5a6268;
        color: white;
    }
    
    /* Bulk Actions */
    .bulk-actions-toolbar {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 2px solid #e9ecef;
    }
    
    .bulk-actions-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .bulk-actions-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .bulk-selection-count {
        font-weight: 600;
        color: #2c3e50;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .bulk-actions-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .bulk-actions-buttons .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .bulk-actions-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
    
    /* Modern Checkbox */
    .modern-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid #e9ecef;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .modern-checkbox:checked {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        border-color: #ef473e;
    }
    
    .modern-checkbox:focus {
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);
        outline: none;
    }
    
    /* Teacher/Student Info */
    .teacher-info, .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .teacher-avatar, .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .teacher-details, .student-details {
        flex: 1;
    }
    
    .teacher-name, .student-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .teacher-qualifications, .student-level {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Booking DateTime */
    .booking-datetime {
        display: flex;
        flex-direction: column;
    }
    
    .date-primary {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .time-secondary {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Duration Badge */
    .duration-badge {
        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-confirmed {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-no-show {
        background: #e2e3e5;
        color: #383d41;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .action-btn {
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .empty-state-description {
        color: #6c757d;
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }
    
    /* Modern Modal */
    .modern-modal {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }
    
    .modern-modal-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e9ecef;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }
    
    .modern-modal-header .modal-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .modern-modal-body {
        padding: 2rem;
    }
    
    .modern-modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 16px 16px;
        padding: 1.5rem;
    }
    
    .current-assignment {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #ef473e;
    }
    
    .assignment-info {
        display: flex;
        align-items: center;
        font-weight: 500;
        color: #2c3e50;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        display: block;
    }
    
    .form-label i {
        color: #ef473e;
    }
    
    .modern-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }
    
    .modern-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);
        background: white;
        outline: none;
    }
    
    .modern-alert {
        border-radius: 12px;
        border: none;
        padding: 1rem;
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Search Highlight */
    .search-highlight {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
    }
    
    .table-row-hidden {
        display: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .modern-card-body {
            padding: 1rem;
        }
        
        .search-input-group {
            flex-direction: column;
        }
        
        .search-input {
            padding: 0.75rem 1rem;
        }
        
        .search-clear {
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .bulk-actions-content {
            flex-direction: column;
            align-items: stretch;
        }
        
        .bulk-actions-left {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .bulk-actions-buttons {
            justify-content: center;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .teacher-info, .student-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .modern-modal-body {
            padding: 1rem;
        }
    }
    
    /* Today's Sessions Highlighting */
    .today-session {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
        border-left: 4px solid #ffc107 !important;
    }
    
    .today-session:hover {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%) !important;
    }
    
    .tomorrow-session {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%) !important;
        border-left: 4px solid #17a2b8 !important;
    }
    
    .tomorrow-session:hover {
        background: linear-gradient(135deg, #bee5eb 0%, #a2d5f2 100%) !important;
    }
    
    .past-session {
        opacity: 0.7;
        background: #f8f9fa !important;
    }
    
    .today-badge {
        background: linear-gradient(135deg, #ffc107, #ff8c00);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
    }
    
    .tomorrow-badge {
        background: linear-gradient(135deg, #17a2b8, #007bff);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
    }
    
    .today-indicator {
        margin-top: 0.25rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    /* Date Group Headers */
    .date-group-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
    }
    
    .date-group-header.today-group {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-bottom: 2px solid #ffc107;
    }
    
    .date-group-header.tomorrow-group {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-bottom: 2px solid #17a2b8;
    }
    
    .date-group-header.past-group {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #6c757d;
        opacity: 0.8;
    }
    
    .date-group-header.future-group {
        background: linear-gradient(135deg, #e8f4fd 0%, #d1ecf1 100%);
        border-bottom: 2px solid #007bff;
    }
    
    .date-group-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
    }
    
    .date-group-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .date-group-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2c3e50;
    }
    
    .date-group-date {
        font-size: 0.9rem;
        color: #6c757d;
        background: rgba(255, 255, 255, 0.8);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
    }
    
    .date-group-count {
        display: flex;
        align-items: center;
    }
    
    .date-group-count .badge {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const tableRows = document.querySelectorAll('tbody tr');
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (searchTerm === '' || text.includes(searchTerm)) {
                row.classList.remove('table-row-hidden');
                if (searchTerm !== '') {
                    highlightText(row, searchTerm);
                } else {
                    removeHighlights(row);
                }
            } else {
                row.classList.add('table-row-hidden');
            }
        });
        
        updateResultsCount();
    }
    
    function highlightText(element, searchTerm) {
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }
        
        textNodes.forEach(textNode => {
            const text = textNode.textContent;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            if (regex.test(text)) {
                const highlightedText = text.replace(regex, '<span class="search-highlight">$1</span>');
                const span = document.createElement('span');
                span.innerHTML = highlightedText;
                textNode.parentNode.replaceChild(span, textNode);
            }
        });
    }
    
    function removeHighlights(element) {
        const highlights = element.querySelectorAll('.search-highlight');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
    }
    
    function updateResultsCount() {
        const visibleRows = document.querySelectorAll('tbody tr:not(.table-row-hidden)');
        const totalRows = tableRows.length;
        
        // Update pagination info if it exists
        const paginationInfo = document.querySelector('.pagination-container');
        if (paginationInfo && searchInput.value.trim() !== '') {
            const existingInfo = paginationInfo.querySelector('.search-results-info');
            if (!existingInfo) {
                const info = document.createElement('div');
                info.className = 'search-results-info text-center mb-2';
                info.innerHTML = `<small class="text-muted">Showing ${visibleRows.length} of ${totalRows} results</small>`;
                paginationInfo.insertBefore(info, paginationInfo.firstChild);
            } else {
                existingInfo.innerHTML = `<small class="text-muted">Showing ${visibleRows.length} of ${totalRows} results</small>`;
            }
        } else {
            const existingInfo = paginationInfo?.querySelector('.search-results-info');
            if (existingInfo) {
                existingInfo.remove();
            }
        }
    }
    
    // Event listeners
    searchInput.addEventListener('input', performSearch);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            clearSearch();
        }
    });
    
    clearSearchBtn.addEventListener('click', clearSearch);
    
    function clearSearch() {
        searchInput.value = '';
        performSearch();
        searchInput.focus();
    }
    
    // Initial search to handle any pre-filled values
    performSearch();
    
    // ========================================
    // BULK ACTIONS FUNCTIONALITY
    // ========================================
    
    const selectAllCheckbox = document.getElementById('selectAll');
    const bookingCheckboxes = document.querySelectorAll('.booking-checkbox');
    const bulkActionsToolbar = document.getElementById('bulkActionsToolbar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelection');
    
    // Bulk action buttons
    const bulkDeleteBtn = document.getElementById('bulkDelete');
    const bulkConfirmBtn = document.getElementById('bulkConfirm');
    const bulkCancelBtn = document.getElementById('bulkCancel');
    const bulkCompleteBtn = document.getElementById('bulkComplete');
    
    function updateBulkActionsToolbar() {
        const checkedBoxes = document.querySelectorAll('.booking-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCountSpan.textContent = count;
        
        if (count > 0) {
            bulkActionsToolbar.style.display = 'block';
        } else {
            bulkActionsToolbar.style.display = 'none';
        }
        
        // Update select all checkbox state
        const totalCheckboxes = bookingCheckboxes.length;
        const checkedCount = checkedBoxes.length;
        
        if (checkedCount === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCount === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
    
    function getSelectedBookingIds() {
        const checkedBoxes = document.querySelectorAll('.booking-checkbox:checked');
        return Array.from(checkedBoxes).map(checkbox => checkbox.value);
    }
    
    function performBulkAction(action, bookingIds) {
        if (!bookingIds || bookingIds.length === 0) {
            alert('Please select at least one booking.');
            return;
        }
        
        const actionText = {
            'delete': 'delete',
            'confirm': 'confirm',
            'cancel': 'cancel',
            'complete': 'complete'
        }[action];
        
        if (!confirm(`Are you sure you want to ${actionText} ${bookingIds.length} booking(s)?`)) {
            return;
        }
        
        // Create form data
        const formData = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        formData.append('_token', csrfToken);
        formData.append('action', action);
        bookingIds.forEach(id => formData.append('booking_ids[]', id));
        
        // Send AJAX request
        fetch(`{{ route('admin.bookings.bulk-actions') }}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.message}`);
                // Reload page to show updated data
                window.location.reload();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while performing the bulk action.');
        });
    }
    
    // Event listeners for checkboxes
    selectAllCheckbox.addEventListener('change', function() {
        bookingCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsToolbar();
    });
    
    bookingCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsToolbar);
    });
    
    // Event listeners for bulk action buttons
    bulkDeleteBtn.addEventListener('click', function() {
        performBulkAction('delete', getSelectedBookingIds());
    });
    
    bulkConfirmBtn.addEventListener('click', function() {
        performBulkAction('confirm', getSelectedBookingIds());
    });
    
    bulkCancelBtn.addEventListener('click', function() {
        performBulkAction('cancel', getSelectedBookingIds());
    });
    
    bulkCompleteBtn.addEventListener('click', function() {
        performBulkAction('complete', getSelectedBookingIds());
    });
    
    clearSelectionBtn.addEventListener('click', function() {
        bookingCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateBulkActionsToolbar();
    });
    
    // Initialize bulk actions toolbar
    updateBulkActionsToolbar();
});

// Global message display function
function showMessage(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to page
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enhanced delete confirmation function
function showDeleteConfirmation(itemName, itemType, deleteUrl, warnings = [], data = {}) {
    // Update modal content
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteItemType').textContent = itemType;
    
    // Show warnings if any
    const warningsDiv = document.getElementById('deleteWarnings');
    if (warnings.length > 0) {
        warningsDiv.innerHTML = '<div class="alert alert-danger"><strong>Related Records:</strong><ul>' + 
            warnings.map(warning => `<li>${warning}</li>`).join('') + 
            '</ul></div>';
    } else {
        warningsDiv.innerHTML = '';
    }
    
    // Set up delete button handler
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const confirmCheckbox = document.getElementById('confirmDeleteCheckbox');
    const forceCheckbox = document.getElementById('forceDeleteCheckbox');
    
    // Reset form
    confirmCheckbox.checked = false;
    forceCheckbox.checked = false;
    confirmBtn.disabled = true;
    confirmBtn.classList.remove('btn-danger', 'btn-warning');
    confirmBtn.classList.add('btn-danger');
    confirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
    
    // Remove existing event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Add new event listener
    newConfirmBtn.addEventListener('click', function() {
        if (!confirmCheckbox.checked) return;
        
        const isForceDelete = forceCheckbox.checked;
        const method = 'POST';
        const url = deleteUrl;
        
        // Add force delete parameter if needed
        const requestData = { ...data };
        if (isForceDelete) {
            requestData.force_delete = true;
        }
        
        // Show loading state
        newConfirmBtn.disabled = true;
        newConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
        
        // Perform delete request
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Item deleted successfully', 'success');
                // Reload page or update table
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                showMessage(data.message || 'Failed to delete item', 'error');
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('An error occurred while deleting: ' + error.message, 'error');
            newConfirmBtn.disabled = false;
            newConfirmBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
        });
    });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    modal.show();
}

// Enhanced restore confirmation function
function showRestoreConfirmation(itemName, itemType, restoreUrl, deletedAt, data = {}) {
    // Update modal content
    document.getElementById('restoreItemName').textContent = itemName;
    document.getElementById('restoreItemType').textContent = itemType;
    document.getElementById('restoreDeletedAt').textContent = deletedAt;
    
    // Set up restore button handler
    const confirmBtn = document.getElementById('confirmRestoreBtn');
    
    // Reset button
    confirmBtn.disabled = false;
    confirmBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Restore';
    
    // Remove existing event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Add new event listener
    newConfirmBtn.addEventListener('click', function() {
        const url = restoreUrl;
        
        // Show loading state
        newConfirmBtn.disabled = true;
        newConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Restoring...';
        
        // Perform restore request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'Item restored successfully', 'success');
                // Reload page or update table
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                showMessage(data.message || 'Failed to restore item', 'error');
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Restore';
            }
        })
        .catch(error => {
            console.error('Restore error:', error);
            showMessage('An error occurred while restoring: ' + error.message, 'error');
            newConfirmBtn.disabled = false;
            newConfirmBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Restore';
        });
    });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('restoreConfirmationModal'));
    modal.show();
}
</script>

<!-- Include Delete Confirmation Modal -->
@include('components.delete-confirmation-modal')

@endsection
