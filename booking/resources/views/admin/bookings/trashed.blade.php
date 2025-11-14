@extends('layouts.app')

@section('title', 'Trashed Bookings')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-trash me-3"></i>Trashed Bookings
            </h1>
            <p class="page-subtitle">View and restore deleted bookings</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="modern-card-body">
        @if($bookings->count() > 0)
            <div class="modern-table-container">
                <table class="modern-table" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Teacher</th>
                            <th>Student</th>
                            <th>Scheduled Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $booking->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-primary text-white rounded-circle">
                                                {{ substr($booking->teacher->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $booking->teacher->user->name ?? 'Unknown Teacher' }}</div>
                                            <small class="text-muted">{{ $booking->teacher->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-success text-white rounded-circle">
                                                {{ substr($booking->student->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $booking->student->user->name ?? 'Unknown Student' }}</div>
                                            <small class="text-muted">{{ $booking->student->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $booking->scheduled_at ? $booking->scheduled_at->format('M d, Y') : 'Not set' }}</div>
                                    <small class="text-muted">
                                        {{ $booking->start_time ? $booking->start_time->format('g:i A') : 'No time set' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $booking->duration_minutes ?? 0 }} min</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($booking->status) {
                                            'pending' => 'bg-warning',
                                            'confirmed' => 'bg-primary',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger">{{ $booking->deleted_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $booking->deleted_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success action-btn" 
                                                title="Restore Booking" 
                                                onclick="showRestoreConfirmation(
                                                    '{{ $booking->id }} - {{ $booking->teacher->user->name ?? 'Unknown Teacher' }} & {{ $booking->student->user->name ?? 'Unknown Student' }}',
                                                    'Booking',
                                                    '{{ route('admin.bookings.restore') }}',
                                                    '{{ $booking->deleted_at->format('M d, Y g:i A') }}',
                                                    { id: {{ $booking->id }} }
                                                )">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="empty-state">
                <h4 class="empty-state-title">No Trashed Bookings</h4>
                <p class="empty-state-text">There are no deleted bookings to display.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const table = document.getElementById('bookingsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    if (clearSearch && searchInput) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        });
    }
});
</script>

<!-- Include Delete Confirmation Modal -->
@include('components.delete-confirmation-modal')

@endsection

@section('styles')
<style>
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
        border-bottom: 1px solid #f8f9fa;
        vertical-align: middle;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
</style>
@endsection
