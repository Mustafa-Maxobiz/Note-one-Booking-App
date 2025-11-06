@extends('layouts.app')

@section('title', 'Book a Session')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="page-title">
                <i class="fas fa-calendar-plus me-3"></i>Book Your Session
            </h1>
            <p class="page-subtitle">Pick a time, choose a teacher - it's that simple!</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Calendar Section -->
    <div class="col-lg-9">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-calendar me-2"></i>Select Date & Time
                </h5>
                <p class="modern-card-subtitle">Choose your preferred 30-minute time slot</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <!-- Calendar Section -->
                    <div class="col-md-5">
                        <div class="calendar-container">
                            <div class="calendar-header">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="prevMonth">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h6 class="calendar-month" id="currentMonth"></h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="nextMonth">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-grid" id="calendarGrid">
                                <!-- Calendar will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Time Slots Section -->
                    <div class="col-md-7">
                        <div class="time-slots-header mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="selected-date" id="selectedDate">Select a date</h6>
                                    <p class="selected-date-subtitle" id="selectedDateSubtitle">Choose a date to see available slots</p>
                                </div>
                                <div class="time-format-toggle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="timeFormat" id="format12h" value="12" checked>
                                        <label class="btn btn-outline-secondary" for="format12h">12h</label>
                                        
                                        <input type="radio" class="btn-check" name="timeFormat" id="format24h" value="24">
                                        <label class="btn btn-outline-secondary" for="format24h">24h</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="time-slots" id="timeSlots">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                <p>Please select a date to view available time slots</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Teacher Selection (Inline, shown after time slot selection) -->
        <div class="modern-card" id="teacherSelection" style="display: none;">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-user-friends me-2"></i>Available Teachers
                </h5>
                <p class="modern-card-subtitle" id="teacherSelectionSubtitle">Select your teacher for this time slot</p>
            </div>
            <div class="modern-card-body">
                <div id="teachersList" class="teachers-grid">
                    <!-- Teachers will be populated here -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Summary -->
    <div class="col-lg-3">
        <div class="modern-card sticky-top" style="top: 20px;">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-check-circle me-2"></i>Your Session
                </h5>
            </div>
            <div class="modern-card-body">
                <div class="booking-summary" id="bookingSummary">
                    <div class="summary-item">
                        <div class="summary-icon"><i class="fas fa-calendar"></i></div>
                        <div>
                            <div class="summary-label">Date</div>
                            <div class="summary-value" id="summaryDate">Not selected</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="summary-label">Time</div>
                            <div class="summary-value" id="summaryTime">Not selected</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon"><i class="fas fa-hourglass-half"></i></div>
                        <div>
                            <div class="summary-label">Duration</div>
                            <div class="summary-value">30 minutes</div>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="summary-label">Teacher</div>
                            <div class="summary-value" id="summaryTeacher">Not selected</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simplified Booking Confirmation Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <form method="POST" action="{{ route('student.bookings.store') }}" id="bookingModalForm">
                @csrf
                <input type="hidden" name="teacher_id" id="modal_teacher_id">
                <input type="hidden" name="date" id="modal_date">
                <input type="hidden" name="time" id="modal_time">
                <input type="hidden" name="duration_minutes" value="30">
                
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="modal-icon mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="modal-title mb-3">Confirm Your Lesson</h5>
                    
                    <div class="confirmation-details mb-4">
                        <p class="mb-2"><strong><span id="modal_date_display"></span></strong></p>
                        <p class="mb-2"><strong><span id="modal_time_display"></span></strong> (30 min)</p>
                        <p class="mb-0">with <strong><span id="modal_teacher_name"></span></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <textarea class="form-control modern-input" id="modal_notes" name="notes" rows="2" 
                                  placeholder="Any notes for your teacher? (optional)"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                        <i class="fas fa-check me-2"></i>Book This Session
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Message (will be shown via JavaScript after booking) -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="successToast" class="toast" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Success!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <p class="mb-0" id="successMessage"></p>
        </div>
    </div>
</div>

<!-- Book by Teacher Section -->
<div class="row mt-4 d-none">
    <div class="col-12">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-user-tie me-2"></i>Prefer to Book by Teacher?
                </h5>
                <p class="modern-card-subtitle">Select a specific teacher and browse their available times</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-3">If you have a preferred teacher or want to browse teachers first, you can use our teacher-focused booking system.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('student.booking.search') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Browse Teachers
                            </a>
                            <a href="{{ route('student.booking.create') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-calendar-alt me-2"></i>Search-Based Booking
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="teacher-booking-icon">
                            <i class="fas fa-guitar fa-3x text-primary mb-3"></i>
                            <h6 class="text-muted">Find Your Perfect Teacher</h6>
                        </div>
                    </div>
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
        opacity: 0.95;
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
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modern-card-title {
        font-size: 1.1rem;
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
    
    /* Calendar */
    .calendar-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .calendar-month {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 1rem;
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
    }
    
    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        color: #6c757d;
        padding: 0.5rem 0;
        font-size: 0.75rem;
    }
    
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.875rem;
        background: white;
        border: 2px solid transparent;
    }
    
    .calendar-day:hover:not(.disabled) {
        background: #e9ecef;
        transform: scale(1.05);
    }
    
    .calendar-day.selected {
        background: linear-gradient(135deg, #ef473e, #fdb838);
        color: white;
        border-color: #ef473e;
        font-weight: 600;
    }
    
    .calendar-day.disabled {
        color: #adb5bd;
        cursor: not-allowed;
        background: #f8f9fa;
    }
    
    .calendar-day.other-month {
        color: #dee2e6;
    }
    
    /* Time Slots */
    .time-slots-header {
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .selected-date {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 1.1rem;
    }
    
    .selected-date-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }
    
    .time-format-toggle {
        flex-shrink: 0;
    }
    
    .time-format-toggle .btn-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 6px;
        overflow: hidden;
    }
    
    .time-format-toggle .btn {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        font-weight: 600;
    }
    
    .time-format-toggle .btn-check:checked + .btn {
        background: linear-gradient(135deg, #ef473e, #fdb838);
        color: white;
        border-color: #ef473e;
    }
    
    .time-slots {
        max-height: 450px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }
    
    .time-slot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        margin-bottom: 0.5rem;
        background: white;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .time-slot:hover:not(.unavailable) {
        border-color: #fdb838;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(253, 184, 56, 0.2);
    }
    
    .time-slot.selected {
        border-color: #ef473e;
        background: linear-gradient(135deg, rgba(239, 71, 62, 0.1), rgba(253, 184, 56, 0.1));
    }
    
    .time-slot-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .time-slot-time {
        font-weight: 600;
        font-size: 1.05rem;
        color: #2c3e50;
    }
    
    .time-slot-teachers {
        font-size: 0.875rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .teacher-count {
        background: #e7f5ff;
        color: #1971c2;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    /* Teachers Grid */
    .teachers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    
    .teacher-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .teacher-card:hover {
        border-color: #fdb838;
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(253, 184, 56, 0.2);
    }
    
    .teacher-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .teacher-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
    }
    
    .teacher-info h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 0.25rem 0;
    }
    
    .teacher-info p {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 0;
    }
    
    .teacher-details {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .teacher-details strong {
        color: #2c3e50;
    }
    
    .select-teacher-btn {
        width: 100%;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        border: none;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    
    .select-teacher-btn:hover {
        background: linear-gradient(135deg, #fdb838, #ef473e);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 71, 62, 0.3);
        color: white;
    }
    
    /* Booking Summary */
    .booking-summary {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .summary-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .summary-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    
    .summary-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .summary-value {
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1.3;
    }
    
    /* Modern Modal */
    .modern-modal .modal-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #ef473e, #fdb838);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .modern-modal .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .confirmation-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        border: 2px solid #e9ecef;
    }
    
    .confirmation-details p {
        font-size: 1rem;
        color: #2c3e50;
    }
    
    .modern-input {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .modern-input:focus {
        border-color: #fdb838;
        box-shadow: 0 0 0 0.2rem rgba(253, 184, 56, 0.15);
        outline: none;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .page-title {
            font-size: 2rem;
        }
        
        .teachers-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .calendar-day {
            font-size: 0.75rem;
        }
        
        .summary-item {
            flex-direction: row;
        }
    }
    
    /* Book by Teacher Section */
    .teacher-booking-icon {
        padding: 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }
    
    .teacher-booking-icon:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-color: #2196f3;
        transform: translateY(-2px);
    }
    
    .teacher-booking-icon i {
        animation: guitarFloat 3s ease-in-out infinite;
    }
    
    @keyframes guitarFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(-5px) rotate(2deg); }
        50% { transform: translateY(-2px) rotate(0deg); }
        75% { transform: translateY(-5px) rotate(-2deg); }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDate = new Date();
    let selectedDate = null;
    let selectedTimeSlot = null;
    let selectedTeacher = null;
    let timeFormat = '12'; // Default to 12-hour format
    
    // Initialize calendar
    initializeCalendar();
    
    // Time format toggle
    document.querySelectorAll('input[name="timeFormat"]').forEach(radio => {
        radio.addEventListener('change', function() {
            timeFormat = this.value;
            if (selectedDate) {
                loadTimeSlots(selectedDate);
            }
        });
    });
    
    function initializeCalendar() {
        updateCalendarDisplay();
        generateCalendar();
    }
    
    function updateCalendarDisplay() {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        document.getElementById('currentMonth').textContent = 
            `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
    
    function generateCalendar() {
        const calendarGrid = document.getElementById('calendarGrid');
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();
        
        calendarGrid.innerHTML = '';
        
        // Add day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Add empty cells
        for (let i = 0; i < startDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            calendarGrid.appendChild(emptyDay);
        }
        
        // Add days
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            const dayDate = new Date(year, month, day);
            
            if (dayDate < today) {
                dayElement.classList.add('disabled');
            } else {
                dayElement.addEventListener('click', () => selectDate(dayDate, dayElement));
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    function selectDate(date, element) {
        // Remove previous selection
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        
        // Add selection
        element.classList.add('selected');
        
        selectedDate = date;
        selectedTimeSlot = null;
        selectedTeacher = null;
        
        updateSelectedDateDisplay();
        loadTimeSlots(date);
        hideTeacherSelection();
    }
    
    function updateSelectedDateDisplay() {
        if (!selectedDate) return;
        
        const dateStr = selectedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        document.getElementById('selectedDate').textContent = dateStr;
        document.getElementById('selectedDateSubtitle').textContent = 'Select a 30-minute time slot';
        
        // Update summary
        document.getElementById('summaryDate').textContent = dateStr;
    }
    
    function loadTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading available slots...</p></div>';
        
        const dateStr = date.getFullYear() + '-' + 
                       String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(date.getDate()).padStart(2, '0');
        
        fetch(`{{ route('student.booking.time-slots') }}?date=${dateStr}&format=${timeFormat}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTimeSlots(data.timeSlots);
            } else {
                showTimeSlotsError(data.message || 'Failed to load time slots');
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            showTimeSlotsError('Failed to load time slots');
        });
    }
    
    function displayTimeSlots(timeSlots) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        
        if (timeSlots.length === 0) {
            timeSlotsContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>No available slots for this date</p>
                    <small>Try selecting a different date</small>
                </div>
            `;
            return;
        }
        
        let html = '';
        timeSlots.forEach(slot => {
            const teacherCount = slot.teachers.length;
            
            html += `
                <div class="time-slot" data-slot="${slot.time}" data-teachers='${JSON.stringify(slot.teachers)}'>
                    <div class="time-slot-info">
                        <div class="time-slot-time">${slot.displayTime}</div>
                    </div>
                    <div class="time-slot-teachers">
                        <span class="teacher-count">${teacherCount} teacher${teacherCount !== 1 ? 's' : ''}</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            `;
        });
        
        timeSlotsContainer.innerHTML = html;
        
        // Add click handlers
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.addEventListener('click', () => selectTimeSlot(slot));
        });
    }
    
    function selectTimeSlot(slotElement) {
        // Remove previous selection
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Add selection
        slotElement.classList.add('selected');
        
        selectedTimeSlot = {
            time: slotElement.dataset.slot,
            teachers: JSON.parse(slotElement.dataset.teachers)
        };
        
        selectedTeacher = null;
        
        updateBookingSummary();
        showTeacherSelection(selectedTimeSlot.teachers);
        
        // Scroll to teacher selection
        setTimeout(() => {
            document.getElementById('teacherSelection').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        }, 100);
    }
    
    function showTeacherSelection(teachers) {
        const teacherSelection = document.getElementById('teacherSelection');
        const teachersList = document.getElementById('teachersList');
        const subtitle = document.getElementById('teacherSelectionSubtitle');
        
        subtitle.textContent = `${teachers.length} teacher${teachers.length !== 1 ? 's' : ''} available - pick your favorite`;
        
        let teachersHtml = '';
        teachers.forEach(teacher => {
            teachersHtml += `
                <div class="teacher-card" data-teacher='${JSON.stringify(teacher)}'>
                    <div class="teacher-header">
                        <div class="teacher-avatar">${teacher.name.charAt(0)}</div>
                        <div class="teacher-info">
                            <h6>${teacher.name}</h6>
                            <p>${teacher.experience_years} years experience</p>
                        </div>
                    </div>
                    <div class="teacher-details mb-3">
                        <div class="mb-1"><strong>Qualifications:</strong> ${teacher.qualifications || 'Experienced Teacher'}</div>
                    </div>
                    <button type="button" class="btn select-teacher-btn">
                        <i class="fas fa-check me-2"></i>Select
                    </button>
                </div>
            `;
        });
        
        teachersList.innerHTML = teachersHtml;
        teacherSelection.style.display = 'block';
        
        // Add click handlers
        document.querySelectorAll('.teacher-card .select-teacher-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.teacher-card');
                const teacher = JSON.parse(card.dataset.teacher);
                selectTeacher(teacher);
            });
        });
    }
    
    function hideTeacherSelection() {
        document.getElementById('teacherSelection').style.display = 'none';
    }
    
    function selectTeacher(teacher) {
        selectedTeacher = teacher;
        updateBookingSummary();
        openBookingModal(teacher);
    }
    
    function updateBookingSummary() {
        if (selectedTimeSlot) {
            // Format time based on selected format
            const displayTime = formatTimeForDisplay(selectedTimeSlot.time, timeFormat);
            document.getElementById('summaryTime').textContent = displayTime;
        }
        
        if (selectedTeacher) {
            document.getElementById('summaryTeacher').textContent = selectedTeacher.name;
        } else if (selectedTimeSlot) {
            const count = selectedTimeSlot.teachers.length;
            document.getElementById('summaryTeacher').textContent = `${count} available`;
        }
    }
    
    function openBookingModal(teacher) {
        // Set modal values
        document.getElementById('modal_teacher_id').value = teacher.id;
        document.getElementById('modal_teacher_name').textContent = teacher.name;
        
        const year = selectedDate.getFullYear();
        const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
        const day = String(selectedDate.getDate()).padStart(2, '0');
        document.getElementById('modal_date').value = `${year}-${month}-${day}`;
        document.getElementById('modal_time').value = selectedTimeSlot.time;
        
        // Set display values
        const dateFormatted = selectedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        
        document.getElementById('modal_date_display').textContent = dateFormatted;
        
        // Format time based on selected format
        const displayTime = formatTimeForDisplay(selectedTimeSlot.time, timeFormat);
        document.getElementById('modal_time_display').textContent = displayTime;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }
    
    function formatTimeForDisplay(timeStr, format) {
        const [hours, minutes] = timeStr.split(':');
        const hour = parseInt(hours);
        const minute = parseInt(minutes);
        
        if (format === '24') {
            return timeStr;
        } else {
            // Convert to 12-hour format
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
            return `${displayHour}:${minutes.padStart(2, '0')} ${period}`;
        }
    }
    
    function showTimeSlotsError(message) {
        document.getElementById('timeSlots').innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p>${message}</p>
            </div>
        `;
    }
    
    // Navigation buttons
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendarDisplay();
        generateCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendarDisplay();
        generateCalendar();
    });
});
</script>
@endsection
