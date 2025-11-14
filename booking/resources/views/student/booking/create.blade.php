@extends('layouts.app')



@section('title', 'Book New Session')



@section('content')

<!-- Page Header -->

<div class="page-header mb-4">

    <div class="row align-items-center">

        <div class="col-md-8">

            <h1 class="page-title">

                <i class="fas fa-calendar-plus me-3"></i>Book New Session

            </h1>

            <p class="page-subtitle">Find and book a session with qualified teachers</p>

        </div>

        <div class="col-md-4 text-end">

            <div class="gap-2 justify-content-end">

                <a href="{{ route('student.booking.calendar') }}" class="btn btn-primary btn-lg mb-1">

                    <i class="fas fa-calendar-alt me-2"></i>Calendar View

                </a>

                <a href="{{ route('student.bookings.index') }}" class="btn btn-secondary btn-lg">

                    <i class="fas fa-arrow-left me-2"></i>Back to My Bookings

                </a>

            </div>

        </div>

    </div>

</div>



<div class="row">

    <div class="col-lg-8">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-search me-2"></i>Search Available Teachers

                </h5>

                <p class="modern-card-subtitle">Select your preferred date, time, and duration</p>

            </div>

            <div class="modern-card-body">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Booking Error:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="bookingForm" class="booking-form">

                    @csrf

                    

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label for="date" class="form-label">

                                <i class="fas fa-calendar me-2"></i>Date

                            </label>

                            <input type="date" class="form-control modern-input @error('date') is-invalid @enderror" 

                                   id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 

                                    required>
                                    <!-- min="{{ date('Y-m-d', strtotime('+1 day')) }}" -->

                            @error('date')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label class="form-label">

                                <i class="fas fa-clock me-2"></i>Select Time Slot

                            </label>

                            <input type="hidden" id="time" name="time" value="{{ old('time') }}" required>
                            
                            <!-- Time Slot Selector -->
                            <div class="time-slots" id="timeSlots" style="display: none;">
                                <select class="form-select modern-select" id="timeSlotSelect">
                                    <option value="">Select a time slot</option>
                                    <!-- Time slots will be populated by JavaScript -->
                                </select>
                            </div>

                            @error('time')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror
                            <small class="text-muted mb-2 d-block">Available 30-minute slots:</small>
                        </div>

                    </div>



                    <!-- Duration is fixed at 30 minutes -->
                    <input type="hidden" id="duration_minutes" name="duration_minutes" value="30">



                    <div class="form-actions">

                        <button type="submit" class="btn btn-primary btn-lg search-btn" id="searchBtn">

                            <i class="fas fa-search me-2"></i>Search Available Teachers

                        </button>

                    </div>

                </form>

            </div>

        </div>



        <!-- Search Results Container -->

        <div class="modern-card" id="searchResultsContainer" style="display: none;">

                <div class="modern-card-header">

                    <h5 class="modern-card-title">

                        <i class="fas fa-users me-2"></i>Available Teachers

                    </h5>

                    <p class="modern-card-subtitle" id="searchResultsSubtitle">Teachers available for your selected time</p>

                </div>

                <div class="modern-card-body">

                    <div id="searchResultsContent">

                        <!-- Results will be loaded here via AJAX -->

                    </div>

                </div>

        </div>

    </div>



    <div class="col-lg-4">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-lightbulb me-2"></i>Booking Tips

                </h5>

                <p class="modern-card-subtitle">Helpful information for booking sessions</p>

            </div>

            <div class="modern-card-body">

                <div class="tips-list">

                    <div class="tip-item">

                        <div class="tip-icon">

                            <i class="fas fa-calendar-check"></i>

                        </div>

                        <div class="tip-content">

                            <h6>Advance Booking</h6>

                            <p>Select a date at least 24 hours in advance</p>

                        </div>

                    </div>

                    <div class="tip-item">

                        <div class="tip-icon">

                            <i class="fas fa-clock"></i>

                        </div>

                        <div class="tip-content">

                            <h6>Choose Your Time</h6>

                            <p>Pick a time that works best for your schedule</p>

                        </div>

                    </div>

                    <div class="tip-item">

                        <div class="tip-icon">

                            <i class="fas fa-hourglass-half"></i>

                        </div>

                        <div class="tip-content">

                            <h6>Flexible Duration</h6>

                            <p>Session duration can be 30 minutes to 2 hours</p>

                        </div>

                    </div>

                    <div class="tip-item">

                        <div class="tip-icon">

                            <i class="fas fa-users"></i>

                        </div>

                        <div class="tip-content">

                            <h6>Teacher Selection</h6>

                            <p>Available teachers will be shown based on your selection</p>

                        </div>

                    </div>

                    <div class="tip-item">

                        <div class="tip-icon">

                            <i class="fas fa-user-check"></i>

                        </div>

                        <div class="tip-content">

                            <h6>View Profiles</h6>

                            <p>You can view teacher profiles before booking</p>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="modern-card mt-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-chart-bar me-2"></i>Your Learning Stats

                </h5>

                <p class="modern-card-subtitle">Track your progress</p>

            </div>

            <div class="modern-card-body">

                <div class="stats-grid">

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-primary">

                            <i class="fas fa-calendar-alt"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ Auth::user()->student ? Auth::user()->student->bookings()->count() : 0 }}</div>

                            <div class="stat-label">Total Bookings</div>

                        </div>

                    </div>

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-success">

                            <i class="fas fa-check-circle"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ Auth::user()->student ? Auth::user()->student->bookings()->where('status', 'completed')->count() : 0 }}</div>

                            <div class="stat-label">Completed</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Book by Teacher Section -->
<div class="row mt-4">
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
                            <a href="{{ route('student.booking.calendar') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-calendar-alt me-2"></i>Back to Calendar View
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

<!-- Booking Modal -->

<div class="modal fade" id="bookingModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="{{ route('student.booking.store') }}" id="bookingModalForm">

                @csrf

                <input type="hidden" name="teacher_id" id="modal_teacher_id">

                <input type="hidden" name="date" id="modal_date">

                <input type="hidden" name="time" id="modal_time">

                <input type="hidden" name="duration_minutes" id="modal_duration">

                

                <div class="modal-header">

                    <h5 class="modal-title text-white">Confirm Booking</h5>

                    <button type="button" class="btn-close text-white btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <strong>Teacher:</strong> <span id="modal_teacher_name"></span>

                    </div>

                    <div class="mb-3">

                        <strong>Date:</strong> <span id="modal_date_display"></span>

                    </div>

                    <div class="mb-3">

                        <strong>Time:</strong> <span id="modal_time_display"></span>

                    </div>

                    <div class="mb-3">

                        <strong>Duration:</strong> <span id="modal_duration_display"></span>

                    </div>

                    <div class="mb-3">

                        <label for="modal_notes" class="form-label">Notes (Optional)</label>

                        <textarea class="form-control" id="modal_notes" name="notes" rows="3" 

                                  placeholder="Any special requirements or notes for the teacher..."></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" class="btn btn-primary">

                        <i class="fas fa-calendar-plus me-2"></i>Confirm Booking

                    </button>

                </div>

            </form>

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

        padding: 2rem;

    }

    

    /* Booking Form */

    .booking-form {

        max-width: 100%;

    }

    

    .form-row {

        display: flex;

        gap: 1.5rem;

        margin-bottom: 1.5rem;

    }

    

    .form-group {

        flex: 1;

        margin-bottom: 1.5rem;

    }

    

    .form-label {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.75rem;

        display: block;

        font-size: 0.95rem;

    }

    

    .form-label i {

        color: #ef473e;

    }

    

    .modern-input,

    .modern-select {

        border: 2px solid #e9ecef;

        border-radius: 12px;

        padding: 0.875rem 1rem;

        font-size: 0.95rem;

        transition: all 0.3s ease;

        /* background: #fafafa; */

    }

    

    .modern-input:focus,

    .modern-select:focus {

        border-color: #ef473e;

        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);

        background: white;

        outline: none;

    }

    

    .form-actions {

        margin-top: 2rem;

        text-align: center;

    }

    

    .search-btn {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        border: none;

        border-radius: 12px;

        padding: 1rem 2rem;

        font-weight: 600;

        font-size: 1rem;

        color: white;

        transition: all 0.3s ease;

        min-width: 250px;

    }

    

    .search-btn:hover {

        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);

        transform: translateY(-2px);

        box-shadow: 0 8px 24px rgba(239, 71, 62, 0.4);

        color: white;

    }

    

    /* Tips List */

    .tips-list {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .tip-item {

        display: flex;

        align-items: flex-start;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 12px;

        border-left: 4px solid #ef473e;

        transition: all 0.3s ease;

    }

    

    .tip-item:hover {

        background: #e9ecef;

        transform: translateX(4px);

    }

    

    .tip-icon {

        width: 40px;

        height: 40px;

        border-radius: 10px;

        background: linear-gradient(135deg, #ef473e, #fdb838);

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 1rem;

        flex-shrink: 0;

    }

    

    .tip-content h6 {

        font-size: 0.95rem;

        font-weight: 600;

        color: #2c3e50;

        margin: 0 0 0.25rem 0;

    }

    

    .tip-content p {

        font-size: 0.875rem;

        color: #6c757d;

        margin: 0;

        line-height: 1.4;

    }

    

    /* Stats Grid */

    .stats-grid {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .stat-item {

        display: flex;

        align-items: center;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 12px;

        transition: all 0.3s ease;

    }

    

    .stat-item:hover {

        background: #e9ecef;

        transform: translateY(-2px);

    }

    

    .stat-icon {

        width: 50px;

        height: 50px;

        border-radius: 12px;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 1.25rem;

        color: white;

        flex-shrink: 0;

    }

    

    .stat-icon-primary {

        background: linear-gradient(135deg, #ef473e, #fdb838);

    }

    

    .stat-icon-success {

        background: linear-gradient(135deg, #28a745, #20c997);

    }

    

    .stat-content {

        flex: 1;

    }

    

    .stat-value {

        font-size: 1.5rem;

        font-weight: 700;

        color: #2c3e50;

        line-height: 1;

    }

    

    .stat-label {

        font-size: 0.875rem;

        color: #6c757d;

        margin-top: 0.25rem;

    }

    

    /* Teacher Cards */

    .avatar-lg {

        width: 50px;

        height: 50px;

    }

    

    .badge {

        font-size: 0.75rem;

    }

    

    /* Search Results Animation */

    #searchResultsContainer {

        animation: fadeInUp 0.5s ease-out;

    }

    

    @keyframes fadeInUp {

        from {

            opacity: 0;

            transform: translateY(20px);

        }

        to {

            opacity: 1;

            transform: translateY(0);

        }

    }

    

    /* Loading State */

    .search-btn:disabled {

        opacity: 0.7;

        cursor: not-allowed;

    }

    

    /* Responsive */

    @media (max-width: 768px) {

        .page-title {

            font-size: 2rem;

        }

        

        .form-row {

            flex-direction: column;

            gap: 0;

        }

        

        .modern-card-body {

            padding: 1.5rem;

        }

        

        .search-btn {

            width: 100%;

            min-width: auto;

        }

        

        .tip-item {

            flex-direction: column;

            text-align: center;

        }

        

        .stat-item {

            flex-direction: column;

            text-align: center;

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
    
    /* Time Slot Dropdown */
    .time-slots {
        /* background: #f8f9fa; */
        /* border-radius: 8px;
        padding: 1rem;
        border: 1px solid #e9ecef; */
    }
    
    .time-slots .form-select {
        /* border: 2px solid #dee2e6; */
        /* transition: all 0.2s ease; */
    }
    
    .time-slots .form-select:focus {
        /* border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25); */
    }

</style>

@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Set default date to today for testing filtering

    const today = new Date();

    // Fix timezone issue: use local date format instead of UTC
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const todayStr = `${year}-${month}-${day}`;

    

    const dateInput = document.getElementById('date');

    if (dateInput && !dateInput.value) {

        dateInput.value = todayStr;

    }
    
    // Time slot functionality
    const timeInput = document.getElementById('time');
    const timeSlots = document.getElementById('timeSlots');
    const timeSlotSelect = document.getElementById('timeSlotSelect');
    
    // Time slots are now generated server-side via AJAX
    
    // Display time slots in dropdown using server-side data
    function displayTimeSlots() {
        if (!dateInput.value) {
            timeSlotSelect.innerHTML = '<option value="">Select a date first</option>';
            return;
        }
        
        timeSlotSelect.innerHTML = '<option value="">Loading time slots...</option>';
        
        // Get time slots from server (same as calendar page)
        fetch(`{{ route('student.booking.time-slots') }}?date=${dateInput.value}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTimeSlotDropdown(data.timeSlots);
            } else {
                timeSlotSelect.innerHTML = '<option value="">No slots available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>';
        });
    }
    
    // Populate dropdown with server data
    function populateTimeSlotDropdown(timeSlots) {
        timeSlotSelect.innerHTML = '<option value="">Select a time slot</option>';
        
        timeSlots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.time;
            option.textContent = slot.displayTime;
            timeSlotSelect.appendChild(option);
        });
        
        // Add event listener for dropdown change
        timeSlotSelect.addEventListener('change', function() {
            timeInput.value = this.value;
        });
    }
    
    // Show/hide time slots based on date selection
    dateInput.addEventListener('change', function() {
        if (this.value) {
            timeSlots.style.display = 'block';
            displayTimeSlots();
        } else {
            timeSlots.style.display = 'none';
        }
    });
    
    // Initialize time slots if date is already selected
    if (dateInput.value) {
        timeSlots.style.display = 'block';
        displayTimeSlots();
    }
    
    // Always show time slots since we have a default date
    timeSlots.style.display = 'block';
    displayTimeSlots();

    

    // AJAX form submission

    const form = document.getElementById('bookingForm');

    const searchBtn = document.getElementById('searchBtn');

    const searchResultsContainer = document.getElementById('searchResultsContainer');

    const searchResultsContent = document.getElementById('searchResultsContent');

    const searchResultsSubtitle = document.getElementById('searchResultsSubtitle');

    

    form.addEventListener('submit', function(e) {

        e.preventDefault();

        

        const date = document.getElementById('date').value;

        const time = document.getElementById('time').value;

        const duration = document.getElementById('duration_minutes').value;

        

        if (!date || !time || !duration) {

            alert('Please fill in all required fields.');

            return false;

        }

        

        // Check if date is in the future

        const selectedDate = new Date(date + ' ' + time);

        const now = new Date();

        

        if (selectedDate <= now) {

            alert('Please select a future date and time.');

            return false;

        }

        

        // Show loading state

        searchBtn.disabled = true;

        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';

        

        // Hide previous results

        searchResultsContainer.style.display = 'none';

        

        // Make AJAX request

        const formData = new FormData();

        formData.append('date', date);

        formData.append('time', time);

        formData.append('duration_minutes', duration);

        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        

        fetch('{{ route("student.booking.search.post") }}', {

            method: 'POST',

            headers: {

                'X-Requested-With': 'XMLHttpRequest'

            },

            body: formData

        })

        .then(response => {

            if (!response.ok) {

                throw new Error(`HTTP error! status: ${response.status}`);

            }

            return response.json();

        })

        .then(data => {

            if (data.success) {

                displaySearchResults(data);

            } else {

                showError(data.message || 'Search failed. Please try again.');

            }

        })

        .catch(error => {

            console.error('Search error:', error);

            showError('An error occurred while searching. Please try again.');

        })

        .finally(() => {

            // Reset button state

            searchBtn.disabled = false;

            searchBtn.innerHTML = '<i class="fas fa-search me-2"></i>Search Available Teachers';

        });

    });

    

    function displaySearchResults(data) {

        const { teachers, count, search_criteria } = data;

        

        // Update subtitle

        const dateFormatted = new Date(search_criteria.date).toLocaleDateString('en-US', {

            weekday: 'long',

            year: 'numeric',

            month: 'long',

            day: 'numeric'

        });

        const timeFormatted = new Date('2000-01-01 ' + search_criteria.time).toLocaleTimeString('en-US', {

            hour: 'numeric',

            minute: '2-digit',

            hour12: true

        });

        

        // Use the actual array length for display

        const actualCount = Array.isArray(teachers) ? teachers.length : Object.keys(teachers).length;

        searchResultsSubtitle.textContent = `${actualCount} teacher${actualCount !== 1 ? 's' : ''} available for ${dateFormatted} at ${timeFormatted}`;

        

        if (actualCount === 0) {

            searchResultsContent.innerHTML = `

                <div class="text-center py-5">

                    <i class="fas fa-search fa-3x text-muted mb-3"></i>

                    <h5 class="text-muted">No teachers available</h5>

                    <p class="text-muted">No teachers are available for the selected date and time.</p>

                    <p class="text-muted small">Try selecting a different date, time, or duration.</p>

                </div>

            `;

        } else {

            // Ensure teachers is an array

            const teachersArray = Array.isArray(teachers) ? teachers : Object.values(teachers);

            let teachersHtml = '<div class="row">';

            

            teachersArray.forEach((teacher, index) => {

                const availableDays = teacher.available_days.map(day => 

                    `<span class="badge bg-success me-1">${day.charAt(0).toUpperCase() + day.slice(1)}</span>`

                ).join('');

                

                teachersHtml += `

                    <div class="col-lg-6 col-xl-6 mb-4">

                        <div class="modern-card h-100">

                            <div class="modern-card-header">

                                <div class="d-flex align-items-center">

                                    <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">

                                        <span class="text-white fw-bold fs-4">${teacher.name.charAt(0)}</span>

                                    </div>

                                    <div>

                                        <h6 class="mb-1">${teacher.name}</h6>

                                        <small class="text-muted">${teacher.qualifications || 'Qualified Teacher'}</small>

                                    </div>

                                </div>

                            </div>

                            <div class="card-body p-3">

                                <div class="mb-3">

                                    <strong>Experience:</strong> ${teacher.experience_years} years

                                </div>

                                

                                ${teacher.bio ? `

                                    <div class="mb-3">

                                        <strong>Bio:</strong>

                                        <p class="text-muted small">${teacher.bio.length > 100 ? teacher.bio.substring(0, 100) + '...' : teacher.bio}</p>

                                    </div>

                                ` : ''}



                                <div class="mb-3">

                                    <strong>Email:</strong> ${teacher.email}

                                </div>



                                ${teacher.phone ? `

                                    <div class="mb-3">

                                        <strong>Phone:</strong> ${teacher.phone}

                                    </div>

                                ` : ''}



                                <div class="mb-3">

                                    <strong>Available Days:</strong>

                                    <div class="small text-muted mt-1">

                                        ${availableDays}

                                    </div>

                                </div>



                                <div class="d-grid">

                                    <button type="button" class="btn btn-primary" 

                                            onclick="bookSession(${teacher.id}, '${teacher.name}', '${search_criteria.date}', '${search_criteria.time}', '${search_criteria.duration}')">

                                        <i class="fas fa-calendar-plus me-2"></i>Book Session

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                `;

            });

            

            teachersHtml += '</div>';

            searchResultsContent.innerHTML = teachersHtml;

        }

        

        // Show results container

        searchResultsContainer.style.display = 'block';

        

        // Scroll to results

        searchResultsContainer.scrollIntoView({ behavior: 'smooth' });

    }

    

    function showError(message) {

        searchResultsContent.innerHTML = `

            <div class="alert alert-danger">

                <i class="fas fa-exclamation-triangle me-2"></i>${message}

            </div>

        `;

        searchResultsContainer.style.display = 'block';

    }

    

    // Global function for booking session

    window.bookSession = function(teacherId, teacherName, date, time, duration) {

        // Set modal values

        document.getElementById('modal_teacher_id').value = teacherId;

        document.getElementById('modal_teacher_name').textContent = teacherName;

        document.getElementById('modal_date').value = date;

        document.getElementById('modal_time').value = time;

        document.getElementById('modal_duration').value = duration;

        

        // Set display values

        const dateFormatted = new Date(date).toLocaleDateString('en-US', {

            weekday: 'long',

            year: 'numeric',

            month: 'long',

            day: 'numeric'

        });

        const timeFormatted = new Date('2000-01-01 ' + time).toLocaleTimeString('en-US', {

            hour: 'numeric',

            minute: '2-digit',

            hour12: true

        });

        const durationText = duration === '30' ? '30 minutes' : 

                           duration === '60' ? '1 hour' : 

                           duration === '90' ? '1.5 hours' : 

                           duration === '120' ? '2 hours' : duration + ' minutes';

        

        document.getElementById('modal_date_display').textContent = dateFormatted;

        document.getElementById('modal_time_display').textContent = timeFormatted;

        document.getElementById('modal_duration_display').textContent = durationText;

        

        // Show modal

        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));

        modal.show();

    };

});

</script>

@endsection

