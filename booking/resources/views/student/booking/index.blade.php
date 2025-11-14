@extends('layouts.app')

@section('title', 'Book a Session')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="page-title">
                <i class="fas fa-calendar-plus me-3"></i>Book a Session
            </h1>
            <p class="page-subtitle">Find and book sessions with available teachers</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <h5 class="modern-card-title">Search Available Teachers</h5>
                        <p class="modern-card-subtitle">Find teachers available for your preferred time</p>
                    </div>
                </div>
            </div>
            <div class="modern-card-body">
                <form method="POST" action="{{ route('student.booking.search') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       value="{{ old('date') }}">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="time" class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('time') is-invalid @enderror" 
                                       id="time" name="time" required value="{{ old('time') }}">
                                @error('time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="duration_minutes" class="form-label">Duration</label>
                                <select class="form-select @error('duration_minutes') is-invalid @enderror" 
                                        id="duration_minutes" name="duration_minutes" required>
                                    <option value="">Select duration</option>
                                    <option value="30" {{ old('duration_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                                    <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>1 hour</option>
                                    <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>1.5 hours</option>
                                    <option value="120" {{ old('duration_minutes') == '120' ? 'selected' : '' }}>2 hours</option>
                                </select>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Search Available Teachers
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">How it works</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                            <h5>1. Choose Date & Time</h5>
                            <p class="text-muted">Select when you want to have your session</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-success mb-3"></i>
                            <h5>2. View Available Teachers</h5>
                            <p class="text-muted">See all teachers available at your chosen time</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle fa-3x text-warning mb-3"></i>
                            <h5>3. Book Your Session</h5>
                            <p class="text-muted">Select a teacher and confirm your booking</p>
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
    display: flex;
    align-items: center;
}

.page-subtitle {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
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
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.modern-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.modern-card-subtitle {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0.25rem 0 0 0;
}

.modern-card-body {
    padding: 2rem;
}

/* Form Elements */
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
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
}
</style>
@endsection
