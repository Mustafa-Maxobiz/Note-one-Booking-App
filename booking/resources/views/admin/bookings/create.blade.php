@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-plus me-3"></i>Create New Booking
            </h1>
            <p class="page-subtitle">Schedule a new lesson session between teacher and student</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>
            </div>
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
        
        <!-- Student Selection (Inline, shown after teacher selection) -->
        <div class="modern-card" id="studentSelection" style="display: none;">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-user-graduate me-2"></i>Select Student
                </h5>
                <p class="modern-card-subtitle" id="studentSelectionSubtitle">Choose the student for this session</p>
            </div>
            <div class="modern-card-body">
                <div id="studentsList" class="students-grid">
                    <!-- Students will be populated here -->
                </div>
            </div>
        </div>
        
        <!-- Booking Form (Hidden until all selections are made) -->
        <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm" style="display: none;">
            @csrf
            <input type="hidden" id="selectedDateInput" name="start_date">
            <input type="hidden" id="selectedTimeInput" name="start_time">
            <input type="hidden" id="selectedTeacherInput" name="teacher_id">
            <input type="hidden" id="selectedStudentInput" name="student_id">
            <input type="hidden" name="duration_minutes" value="30">
            <input type="hidden" name="status" value="pending">
            <input type="hidden" id="bookingNotes" name="notes">
        </form>
    </div>
    
    <!-- Sidebar Summary -->
    <div class="col-lg-3">
        <div class="modern-card sticky-top" style="top: 20px;">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-check-circle me-2"></i>Your Session
                </h5>
                <p class="modern-card-subtitle">Review your booking details</p>
            </div>
            <div class="modern-card-body">
                <div class="booking-summary">
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-calendar me-2"></i>DATE
                        </div>
                        <div class="summary-value" id="summaryDate">Not selected</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-clock me-2"></i>TIME
                        </div>
                        <div class="summary-value" id="summaryTime">Not selected</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-hourglass-half me-2"></i>DURATION
                        </div>
                        <div class="summary-value" id="summaryDuration">30 minutes</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-chalkboard-teacher me-2"></i>TEACHER
                        </div>
                        <div class="summary-value" id="summaryTeacher">Not selected</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-user-graduate me-2"></i>STUDENT
                        </div>
                        <div class="summary-value" id="summaryStudent">Not selected</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">
                            <i class="fas fa-info-circle me-2"></i>STATUS
                        </div>
                        <div class="summary-value" id="summaryStatus">Pending</div>
                    </div>
                </div>
                
                <!-- Submit Button (Hidden until all selections are made) -->
                <div class="mt-4" id="submitSection" style="display: none;">
                    <button type="button" class="btn btn-primary w-100" id="createBookingBtn">
                        <i class="fas fa-calendar-plus me-2"></i>Create Booking
                    </button>
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
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        color: white;
    }
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        background: transparent;
    }
    
    .btn-outline-light:hover {
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
    
    /* Booking Summary */
    .booking-summary {
        space-y: 1rem;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .summary-item:last-child {
        border-bottom: none;
    }
    
    .summary-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .summary-value {
        font-weight: 500;
        color: #2c3e50;
        text-align: right;
        font-size: 0.875rem;
    }
    
    /* Calendar Styles */
    .calendar-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e9ecef;
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
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
    }
    
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .calendar-day:hover {
        background: #e9ecef;
    }
    
    .calendar-day.selected {
        background: #fd7e14;
        color: white;
    }
    
    .calendar-day.other-month {
        color: #adb5bd;
    }
    
    .calendar-day.today {
        background: #17a2b8;
        color: white;
    }
    
    .calendar-day.past {
        color: #adb5bd;
        cursor: not-allowed;
    }
    
    .calendar-day.past:hover {
        background: transparent;
    }
    
    /* Time Slots */
    .time-slots {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .time-slot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .time-slot:hover {
        border-color: #fd7e14;
        background: #fff5f0;
    }
    
    .time-slot.selected {
        border-color: #fd7e14;
        background: #fd7e14;
        color: white;
    }
    
    .time-slot-info {
        display: flex;
        flex-direction: column;
    }
    
    .time-slot-time {
        font-weight: 600;
        font-size: 1rem;
    }
    
    .time-slot-teachers {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .time-slot.selected .time-slot-teachers {
        color: rgba(255, 255, 255, 0.8);
    }
    
    /* Teacher/Student Grid */
    .teachers-grid, .students-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .teacher-card, .student-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .teacher-card:hover, .student-card:hover {
        border-color: #fd7e14;
        background: #fff5f0;
    }
    
    .teacher-card.selected, .student-card.selected {
        border-color: #fd7e14;
        background: #fd7e14;
        color: white;
    }
    
    .teacher-info, .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .teacher-avatar, .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6c757d;
    }
    
    .teacher-card.selected .teacher-avatar,
    .student-card.selected .student-avatar {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .teacher-details, .student-details {
        flex: 1;
    }
    
    .teacher-name, .student-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .teacher-qualifications, .student-level {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .teacher-card.selected .teacher-qualifications,
    .student-card.selected .student-level {
        color: rgba(255, 255, 255, 0.8);
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendar variables
    let currentDate = new Date();
    let selectedDate = null;
    let selectedTime = null;
    let selectedTeacher = null;
    let selectedStudent = null;
    
    // Time slots are now generated dynamically based on selected date
    
    // Teachers data (from Laravel)
    let teachers = @json($teachers);
    const students = @json($students);
    
    // Initialize calendar
    function initCalendar() {
        updateCalendar();
        updateTimeSlots();
    }
    
    // Update calendar display
    function updateCalendar() {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        document.getElementById('currentMonth').textContent = 
            `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        
        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';
        
        // Add day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day';
            dayHeader.style.fontWeight = '600';
            dayHeader.style.color = '#6c757d';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Get first day of month and number of days
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        // Generate calendar days
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = date.getDate();
            
            // Add classes based on date
            if (date.getMonth() !== currentDate.getMonth()) {
                dayElement.classList.add('other-month');
            }
            
            if (date.toDateString() === new Date().toDateString()) {
                dayElement.classList.add('today');
            }
            
            if (date < new Date().setHours(0, 0, 0, 0)) {
                dayElement.classList.add('past');
            } else {
                dayElement.addEventListener('click', () => selectDate(date));
            }
            
            if (selectedDate && date.toDateString() === selectedDate.toDateString()) {
                dayElement.classList.add('selected');
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    // Select date
    function selectDate(date) {
        selectedDate = date;
        selectedTime = null;
        selectedTeacher = null;
        selectedStudent = null;
        
        updateCalendar();
        updateTimeSlots();
        updateSummary();
        hideSections();
    }
    
    // Update time slots
    function updateTimeSlots() {
        const timeSlotsContainer = document.getElementById('timeSlots');
        
        if (!selectedDate) {
            timeSlotsContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <p>Please select a date to view available time slots</p>
                </div>
            `;
            return;
        }
        
        const dateStr = selectedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        document.getElementById('selectedDate').textContent = dateStr;
        document.getElementById('selectedDateSubtitle').textContent = 'Select a 30-minute time slot';
        
        // Load time slots from server
        loadTimeSlots(selectedDate);
    }
    
    function loadTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading available slots...</p></div>';
        
        const dateStr = date.getFullYear() + '-' + 
                       String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(date.getDate()).padStart(2, '0');
        
        const timeFormat = document.querySelector('input[name="timeFormat"]:checked')?.value || '12';
        
        fetch(`{{ route('admin.bookings.time-slots') }}?date=${dateStr}&format=${timeFormat}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Time slots data:', data);
            if (data.success) {
                displayTimeSlots(data.timeSlots);
            } else {
                showTimeSlotsError(data.message || 'Failed to load time slots');
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            showTimeSlotsError('Failed to load time slots: ' + error.message);
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
            const teacherText = teacherCount === 1 ? '1 teacher' : `${teacherCount} teachers`;
            
            html += `
                <div class="time-slot" data-time="${slot.time}" data-teachers='${JSON.stringify(slot.teachers)}'>
                    <div class="time-slot-info">
                        <div class="time-slot-time">${formatTime(slot.time)}</div>
                        <div class="time-slot-teachers">${teacherText}</div>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>
            `;
        });
        
        timeSlotsContainer.innerHTML = html;
        
        // Add click event listeners
        timeSlotsContainer.querySelectorAll('.time-slot').forEach(slot => {
            slot.addEventListener('click', () => {
                const time = slot.dataset.time;
                selectTime(time);
            });
        });
    }
    
    function showTimeSlotsError(message) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = `
            <div class="text-center text-danger py-5">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <p>${message}</p>
                <button class="btn btn-outline-primary btn-sm" onclick="updateTimeSlots()">Try Again</button>
            </div>
        `;
    }
    
    // Time slots are now loaded from server via AJAX
    
    // Select time
    function selectTime(time) {
        selectedTime = time;
        selectedTeacher = null;
        selectedStudent = null;
        
        // Get teachers for this specific time slot
        const timeSlotElement = event.currentTarget;
        const teachersData = timeSlotElement.dataset.teachers;
        teachers = JSON.parse(teachersData);
        
        // Update time slot selection
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        timeSlotElement.classList.add('selected');
        
        showTeacherSelection();
        updateSummary();
    }
    
    // Show teacher selection
    function showTeacherSelection() {
        document.getElementById('teacherSelection').style.display = 'block';
        document.getElementById('teacherSelectionSubtitle').textContent = 
            `Select a teacher for ${formatTime(selectedTime)} on ${selectedDate.toLocaleDateString()}`;
        
        const teachersList = document.getElementById('teachersList');
        teachersList.innerHTML = '';
        
        teachers.forEach(teacher => {
            const teacherCard = document.createElement('div');
            teacherCard.className = 'teacher-card';
            teacherCard.innerHTML = `
                <div class="teacher-info">
                    <div class="teacher-avatar">
                        ${teacher.name ? teacher.name.charAt(0).toUpperCase() : 'T'}
                    </div>
                    <div class="teacher-details">
                        <div class="teacher-name">${teacher.name || 'No User'}</div>
                        <div class="teacher-qualifications">${teacher.qualifications}</div>
                    </div>
                </div>
            `;
            
            teacherCard.addEventListener('click', () => selectTeacher(teacher));
            teachersList.appendChild(teacherCard);
        });
    }
    
    // Select teacher
    function selectTeacher(teacher) {
        selectedTeacher = teacher;
        selectedStudent = null;
        
        // Update teacher selection
        document.querySelectorAll('.teacher-card').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
        
        showStudentSelection();
        updateSummary();
    }
    
    // Show student selection
    function showStudentSelection() {
        document.getElementById('studentSelection').style.display = 'block';
        document.getElementById('studentSelectionSubtitle').textContent = 
            `Select a student for this session`;
        
        const studentsList = document.getElementById('studentsList');
        studentsList.innerHTML = '';
        
        students.forEach(student => {
            const studentCard = document.createElement('div');
            studentCard.className = 'student-card';
            studentCard.innerHTML = `
                <div class="student-info">
                    <div class="student-avatar">
                        ${student.user ? student.user.name.charAt(0).toUpperCase() : 'S'}
                    </div>
                    <div class="student-details">
                        <div class="student-name">${student.user ? student.user.name : 'No User'}</div>
                        <div class="student-level">${student.level} level</div>
                    </div>
                </div>
            `;
            
            studentCard.addEventListener('click', () => selectStudent(student));
            studentsList.appendChild(studentCard);
        });
    }
    
    // Select student
    function selectStudent(student) {
        selectedStudent = student;
        
        // Update student selection
        document.querySelectorAll('.student-card').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
        
        showSubmitButton();
        updateSummary();
    }
    
    // Show submit button
    function showSubmitButton() {
        document.getElementById('submitSection').style.display = 'block';
    }
    
    // Hide sections
    function hideSections() {
        document.getElementById('teacherSelection').style.display = 'none';
        document.getElementById('studentSelection').style.display = 'none';
        document.getElementById('submitSection').style.display = 'none';
    }
    
    // Update summary
    function updateSummary() {
        document.getElementById('summaryDate').textContent = 
            selectedDate ? selectedDate.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) : 'Not selected';
        
        document.getElementById('summaryTime').textContent = 
            selectedTime ? formatTime(selectedTime) : 'Not selected';
        
        document.getElementById('summaryTeacher').textContent = 
            selectedTeacher ? (selectedTeacher.name || 'No User') : 'Not selected';
        
        document.getElementById('summaryStudent').textContent = 
            selectedStudent ? (selectedStudent.user ? selectedStudent.user.name : 'No User') : 'Not selected';
    }
    
    // Format time
    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }
    
    // Event listeners
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    });
    
    document.getElementById('createBookingBtn').addEventListener('click', () => {
        if (selectedDate && selectedTime && selectedTeacher && selectedStudent) {
            // Set form values
            const year = selectedDate.getFullYear();
            const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
            const day = String(selectedDate.getDate()).padStart(2, '0');
            document.getElementById('selectedDateInput').value = `${year}-${month}-${day}`;
            document.getElementById('selectedTimeInput').value = selectedTime;
            document.getElementById('selectedTeacherInput').value = selectedTeacher.id;
            document.getElementById('selectedStudentInput').value = selectedStudent.id;
            
            // Debug: Log form data
            console.log('Form data being submitted:', {
                start_date: document.getElementById('selectedDateInput').value,
                start_time: document.getElementById('selectedTimeInput').value,
                teacher_id: document.getElementById('selectedTeacherInput').value,
                student_id: document.getElementById('selectedStudentInput').value,
                duration_minutes: 30,
                status: 'pending'
            });
            
            // Submit form
            document.getElementById('bookingForm').submit();
        } else {
            console.log('Missing required data:', {
                selectedDate: !!selectedDate,
                selectedTime: !!selectedTime,
                selectedTeacher: !!selectedTeacher,
                selectedStudent: !!selectedStudent
            });
        }
    });
    
    // Initialize
    initCalendar();
});
</script>
@endsection
