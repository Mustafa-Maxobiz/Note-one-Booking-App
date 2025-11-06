@extends('layouts.app')

@section('title', 'Available Teachers')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-search me-3"></i>Available Teachers
            </h1>
            <p class="page-subtitle">Find the perfect teacher for your lesson</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('student.booking.create') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Search
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-filter me-2"></i>Search Criteria
                </h5>
                <p class="modern-card-subtitle">Your selected search parameters</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ $selectedDate->format('l, F d, Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($startTime)->format('g:i A') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Duration:</strong> {{ $duration }} minutes
                    </div>
                    <div class="col-md-3">
                        <strong>Available Teachers:</strong> {{ $availableTeachers->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($availableTeachers->count() > 0)
    <div class="row">
        @foreach($availableTeachers as $teacher)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                <span class="text-white fw-bold fs-4">{{ substr($teacher->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $teacher->user->name }}</h6>
                                <small class="text-muted">{{ $teacher->qualifications }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Experience:</strong> {{ $teacher->experience_years }} years
                        </div>
                        
                        @if($teacher->bio)
                            <div class="mb-3">
                                <strong>Bio:</strong>
                                <p class="text-muted small">{{ Str::limit($teacher->bio, 100) }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Email:</strong> {{ $teacher->user->email }}
                        </div>

                        @if($teacher->user->phone)
                            <div class="mb-3">
                                <strong>Phone:</strong> {{ $teacher->user->phone }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Available Days:</strong>
                            @php
                                $availableDays = $teacher->availabilities->pluck('day_of_week')->unique()->sort();
                            @endphp
                            <div class="small text-muted">
                                @foreach($availableDays as $day)
                                    <span class="badge bg-success me-1">{{ ucfirst($day) }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-primary" 
                                    onclick="bookSession({{ $teacher->id }}, '{{ $teacher->user->name }}')">
                                <i class="fas fa-calendar-plus me-2"></i>Book Session
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No teachers available</h5>
                    <p class="text-muted">No teachers are available for the selected date and time.</p>
                    <a href="{{ route('student.booking.create') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Try Different Time
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif



<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('student.booking.store') }}" id="bookingForm">
                @csrf
                <input type="hidden" name="teacher_id" id="teacher_id">
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                <input type="hidden" name="time" value="{{ $startTime }}">
                <input type="hidden" name="duration_minutes" value="{{ $duration }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Booking Error</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <p class="mb-0 small">
                                <strong>Tip:</strong> Try selecting a different date or time, or choose a different teacher who is available on your preferred day.
                            </p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Teacher:</strong> <span id="teacher_name"></span>
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> {{ $selectedDate->format('l, F d, Y') }}
                    </div>
                    <div class="mb-3">
                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($startTime)->format('g:i A') }}
                    </div>
                    <div class="mb-3">
                        <strong>Duration:</strong> {{ $duration }} minutes
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
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

@section('scripts')
{{-- Include form loading state component --}}
@include('components.form-loading-state')

<script>
function bookSession(teacherId, teacherName) {
    console.log('Booking session for teacher:', teacherId, teacherName);
    
    const teacherIdField = document.getElementById('teacher_id');
    const teacherNameField = document.getElementById('teacher_name');
    
    if (teacherIdField && teacherNameField) {
        teacherIdField.value = teacherId;
        teacherNameField.textContent = teacherName;
        
        console.log('Teacher ID set to:', teacherIdField.value);
        console.log('Teacher name set to:', teacherNameField.textContent);
        
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    } else {
        console.error('Could not find teacher_id or teacher_name elements');
        alert('Error: Could not set teacher information. Please try again.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('bookingForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const teacherId = document.getElementById('teacher_id').value;
            const date = document.querySelector('input[name="date"]').value;
            const time = document.querySelector('input[name="time"]').value;
            const duration = document.querySelector('input[name="duration_minutes"]').value;
            
            console.log('Form submission details:');
            console.log('- Teacher ID:', teacherId);
            console.log('- Date:', date);
            console.log('- Time:', time);
            console.log('- Duration:', duration);
            
            if (!teacherId) {
                e.preventDefault();
                alert('Please select a teacher.');
                return false;
            }
            
            if (!date || !time || !duration) {
                e.preventDefault();
                alert('Missing booking details. Please try again.');
                return false;
            }
            
            // Show loading state using unified component
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                setLoadingState(submitBtn, 'Creating Booking...');
            }
            
            console.log('Form is being submitted...');
        });
    } else {
        console.error('Could not find booking form');
    }
});


</script>
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
