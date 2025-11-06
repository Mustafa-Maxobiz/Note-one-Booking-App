@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-3"></i>My Bookings
            </h1>
            <p class="page-subtitle">Manage and track all your learning sessions</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('student.booking.calendar') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Book New Session
            </a>
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
                <p class="modern-card-subtitle">View and manage your session bookings</p>
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
                        <button type="button" class="btn btn-sm btn-outline-danger" id="bulkCancel">
                            <i class="fas fa-times me-1"></i>Cancel
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
                            <th class="checkbox-column">
                                <input type="checkbox" id="selectAll" class="modern-checkbox">
                            </th>
                            <th>Teacher</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr class="booking-row">
                                <td class="checkbox-column">
                                    <input type="checkbox" class="modern-checkbox booking-checkbox" value="{{ $booking->id }}">
                                </td>
                                <td>
                                    <div class="teacher-info">
                                        <div class="teacher-avatar">
                                            <img src="{{ $booking->teacher->user->small_profile_picture_url }}" 
                                                 alt="{{ $booking->teacher->user->name }}"
                                                 class="avatar-img">
                                        </div>
                                        <div class="teacher-details">
                                            <div class="teacher-name">{{ $booking->teacher->user->name }}</div>
                                            <div class="teacher-qualifications">{{ $booking->teacher->qualifications }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="booking-datetime">
                                        <div class="booking-date">{{ $booking->start_time->format('M d, Y') }}</div>
                                        <div class="booking-time">{{ $booking->start_time->format('g:i A') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="duration-badge">{{ $booking->duration_minutes }} min</span>
                                </td>
                                <td>
                                    @if($booking->status === 'confirmed')
                                        <span class="status-badge status-confirmed">
                                            <i class="fas fa-check-circle me-1"></i>Confirmed
                                        </span>
                                    @elseif($booking->status === 'pending')
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($booking->status === 'completed')
                                        <span class="status-badge status-completed">
                                            <i class="fas fa-check-double me-1"></i>Completed
                                        </span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="status-badge status-cancelled">
                                            <i class="fas fa-times-circle me-1"></i>Cancelled
                                        </span>
                                    @elseif($booking->status === 'declined')
                                        <span class="status-badge status-declined">
                                            <i class="fas fa-ban me-1"></i>Declined
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($booking->zoom_join_url && $booking->status === 'confirmed')
                                            @if($booking->start_time <= now() && $booking->end_time > now())
                                                <a href="{{ route('meeting.join', $booking) }}" target="_blank" class="btn btn-sm btn-primary action-btn">
                                                    <i class="fas fa-video me-1"></i>Join
                                                </a>
                                            @elseif($booking->start_time > now())
                                                <span class="btn btn-sm btn-outline-secondary action-btn disabled">
                                                    <i class="fas fa-clock me-1"></i>Not Started
                                                </span>
                                            @else
                                                <span class="btn btn-sm btn-outline-secondary action-btn disabled">
                                                    <i class="fas fa-check-circle me-1"></i>Ended
                                                </span>
                                            @endif
                                        @endif
                                        <a href="{{ route('student.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary action-btn">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        @if(in_array($booking->status, ['pending', 'confirmed']))
                                            @if($booking->end_time > now())
                                            <form method="POST" action="{{ route('student.bookings.cancel', $booking) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-btn" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            </form>
                                            @endif
                                        @endif
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
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h5 class="empty-state-title">No bookings found</h5>
                <p class="empty-state-description">Start by booking your first session with a qualified teacher.</p>
                <a href="{{ route('student.booking.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Book New Session
                </a>
            </div>
        @endif
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
    
    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
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
        display: flex;
        justify-content: flex-end;
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
        width: 300px;
        outline: none;
    }
    
    .search-clear {
        border: none;
        background: transparent;
        padding: 0.5rem;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    
    .search-clear:hover {
        color: #ef473e;
    }
    
    /* Bulk Actions */
    .bulk-actions-toolbar {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 1px solid #ffeaa7;
    }
    
    .bulk-actions-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
    }
    
    .bulk-actions-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .bulk-selection-count {
        font-weight: 600;
        color: #856404;
    }
    
    .bulk-actions-buttons {
        display: flex;
        gap: 0.5rem;
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
    
    .checkbox-column {
        width: 50px;
        text-align: center;
    }
    
    .modern-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid #dee2e6;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .modern-checkbox:checked {
        background: linear-gradient(135deg, #ef473e, #fdb838);
        border-color: #ef473e;
    }
    
    /* Teacher Info */
    .teacher-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .teacher-avatar {
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
    
    .teacher-details {
        flex: 1;
    }
    
    .teacher-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .teacher-qualifications {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Booking DateTime */
    .booking-datetime {
        display: flex;
        flex-direction: column;
    }
    
    .booking-date {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .booking-time {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Duration Badge */
    .duration-badge {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #1976d2;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
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
    
    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-declined {
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
        
        .search-input {
            width: 200px;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .teacher-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .bulk-actions-content {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
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
    const bulkCancelBtn = document.getElementById('bulkCancel');
    
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
            'cancel': 'cancel'
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
        fetch(`{{ route('student.bookings.bulk-actions') }}`, {
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
    bulkCancelBtn.addEventListener('click', function() {
        performBulkAction('cancel', getSelectedBookingIds());
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
</script>
@endsection

