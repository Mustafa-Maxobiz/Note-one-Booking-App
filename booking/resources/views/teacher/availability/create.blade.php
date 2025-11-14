@extends('layouts.app')

@section('title', 'Add Availability')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-clock me-3"></i>Add Availability
            </h1>
            <p class="page-subtitle">Create new time slots for students to book</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('teacher.availability.index') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Schedule
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-plus me-2"></i>Add New Time Slot
                </h5>
                <p class="modern-card-subtitle">Configure your availability for students to book</p>
            </div>
            <div class="modern-card-body">
                <form method="POST" action="{{ route('teacher.availability.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="day_of_week" class="form-label">Day of Week</label>
                        <select class="form-select @error('day_of_week') is-invalid @enderror" id="day_of_week" name="day_of_week" required>
                            <option value="">Select day</option>
                            <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>Monday</option>
                            <option value="tuesday" {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="wednesday" {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="thursday" {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>Friday</option>
                            <option value="saturday" {{ old('day_of_week') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="sunday" {{ old('day_of_week') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>
                        @error('day_of_week')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <select class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" required>
                                    <option value="">Select start time</option>
                                    <option value="00:00" {{ old('start_time') == '00:00' ? 'selected' : '' }}>12:00 AM</option>
                                    <option value="00:30" {{ old('start_time') == '00:30' ? 'selected' : '' }}>12:30 AM</option>
                                    <option value="01:00" {{ old('start_time') == '01:00' ? 'selected' : '' }}>1:00 AM</option>
                                    <option value="01:30" {{ old('start_time') == '01:30' ? 'selected' : '' }}>1:30 AM</option>
                                    <option value="02:00" {{ old('start_time') == '02:00' ? 'selected' : '' }}>2:00 AM</option>
                                    <option value="02:30" {{ old('start_time') == '02:30' ? 'selected' : '' }}>2:30 AM</option>
                                    <option value="03:00" {{ old('start_time') == '03:00' ? 'selected' : '' }}>3:00 AM</option>
                                    <option value="03:30" {{ old('start_time') == '03:30' ? 'selected' : '' }}>3:30 AM</option>
                                    <option value="04:00" {{ old('start_time') == '04:00' ? 'selected' : '' }}>4:00 AM</option>
                                    <option value="04:30" {{ old('start_time') == '04:30' ? 'selected' : '' }}>4:30 AM</option>
                                    <option value="05:00" {{ old('start_time') == '05:00' ? 'selected' : '' }}>5:00 AM</option>
                                    <option value="05:30" {{ old('start_time') == '05:30' ? 'selected' : '' }}>5:30 AM</option>
                                    <option value="06:00" {{ old('start_time') == '06:00' ? 'selected' : '' }}>6:00 AM</option>
                                    <option value="06:30" {{ old('start_time') == '06:30' ? 'selected' : '' }}>6:30 AM</option>
                                    <option value="07:00" {{ old('start_time') == '07:00' ? 'selected' : '' }}>7:00 AM</option>
                                    <option value="07:30" {{ old('start_time') == '07:30' ? 'selected' : '' }}>7:30 AM</option>
                                    <option value="08:00" {{ old('start_time') == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                    <option value="08:30" {{ old('start_time') == '08:30' ? 'selected' : '' }}>8:30 AM</option>
                                    <option value="09:00" {{ old('start_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="09:30" {{ old('start_time') == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                                    <option value="10:00" {{ old('start_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="10:30" {{ old('start_time') == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                                    <option value="11:00" {{ old('start_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="11:30" {{ old('start_time') == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                                    <option value="12:00" {{ old('start_time') == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                                    <option value="12:30" {{ old('start_time') == '12:30' ? 'selected' : '' }}>12:30 PM</option>
                                    <option value="13:00" {{ old('start_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="13:30" {{ old('start_time') == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                                    <option value="14:00" {{ old('start_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="14:30" {{ old('start_time') == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                                    <option value="15:00" {{ old('start_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="15:30" {{ old('start_time') == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                                    <option value="16:00" {{ old('start_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                    <option value="16:30" {{ old('start_time') == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                                    <option value="17:00" {{ old('start_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                    <option value="17:30" {{ old('start_time') == '17:30' ? 'selected' : '' }}>5:30 PM</option>
                                    <option value="18:00" {{ old('start_time') == '18:00' ? 'selected' : '' }}>6:00 PM</option>
                                    <option value="18:30" {{ old('start_time') == '18:30' ? 'selected' : '' }}>6:30 PM</option>
                                    <option value="19:00" {{ old('start_time') == '19:00' ? 'selected' : '' }}>7:00 PM</option>
                                    <option value="19:30" {{ old('start_time') == '19:30' ? 'selected' : '' }}>7:30 PM</option>
                                    <option value="20:00" {{ old('start_time') == '20:00' ? 'selected' : '' }}>8:00 PM</option>
                                    <option value="20:30" {{ old('start_time') == '20:30' ? 'selected' : '' }}>8:30 PM</option>
                                    <option value="21:00" {{ old('start_time') == '21:00' ? 'selected' : '' }}>9:00 PM</option>
                                    <option value="21:30" {{ old('start_time') == '21:30' ? 'selected' : '' }}>9:30 PM</option>
                                    <option value="22:00" {{ old('start_time') == '22:00' ? 'selected' : '' }}>10:00 PM</option>
                                    <option value="22:30" {{ old('start_time') == '22:30' ? 'selected' : '' }}>10:30 PM</option>
                                    <option value="23:00" {{ old('start_time') == '23:00' ? 'selected' : '' }}>11:00 PM</option>
                                    <option value="23:30" {{ old('start_time') == '23:30' ? 'selected' : '' }}>11:30 PM</option>
                                </select>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <select class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" required>
                                    <option value="">Select end time</option>
                                    <option value="00:00" {{ old('end_time') == '00:00' ? 'selected' : '' }}>12:00 AM</option>
                                    <option value="00:30" {{ old('end_time') == '00:30' ? 'selected' : '' }}>12:30 AM</option>
                                    <option value="01:00" {{ old('end_time') == '01:00' ? 'selected' : '' }}>1:00 AM</option>
                                    <option value="01:30" {{ old('end_time') == '01:30' ? 'selected' : '' }}>1:30 AM</option>
                                    <option value="02:00" {{ old('end_time') == '02:00' ? 'selected' : '' }}>2:00 AM</option>
                                    <option value="02:30" {{ old('end_time') == '02:30' ? 'selected' : '' }}>2:30 AM</option>
                                    <option value="03:00" {{ old('end_time') == '03:00' ? 'selected' : '' }}>3:00 AM</option>
                                    <option value="03:30" {{ old('end_time') == '03:30' ? 'selected' : '' }}>3:30 AM</option>
                                    <option value="04:00" {{ old('end_time') == '04:00' ? 'selected' : '' }}>4:00 AM</option>
                                    <option value="04:30" {{ old('end_time') == '04:30' ? 'selected' : '' }}>4:30 AM</option>
                                    <option value="05:00" {{ old('end_time') == '05:00' ? 'selected' : '' }}>5:00 AM</option>
                                    <option value="05:30" {{ old('end_time') == '05:30' ? 'selected' : '' }}>5:30 AM</option>
                                    <option value="06:00" {{ old('end_time') == '06:00' ? 'selected' : '' }}>6:00 AM</option>
                                    <option value="06:30" {{ old('end_time') == '06:30' ? 'selected' : '' }}>6:30 AM</option>
                                    <option value="07:00" {{ old('end_time') == '07:00' ? 'selected' : '' }}>7:00 AM</option>
                                    <option value="07:30" {{ old('end_time') == '07:30' ? 'selected' : '' }}>7:30 AM</option>
                                    <option value="08:00" {{ old('end_time') == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                    <option value="08:30" {{ old('end_time') == '08:30' ? 'selected' : '' }}>8:30 AM</option>
                                    <option value="09:00" {{ old('end_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="09:30" {{ old('end_time') == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                                    <option value="10:00" {{ old('end_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="10:30" {{ old('end_time') == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                                    <option value="11:00" {{ old('end_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="11:30" {{ old('end_time') == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                                    <option value="12:00" {{ old('end_time') == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                                    <option value="12:30" {{ old('end_time') == '12:30' ? 'selected' : '' }}>12:30 PM</option>
                                    <option value="13:00" {{ old('end_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="13:30" {{ old('end_time') == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                                    <option value="14:00" {{ old('end_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="14:30" {{ old('end_time') == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                                    <option value="15:00" {{ old('end_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="15:30" {{ old('end_time') == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                                    <option value="16:00" {{ old('end_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                    <option value="16:30" {{ old('end_time') == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                                    <option value="17:00" {{ old('end_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                    <option value="17:30" {{ old('end_time') == '17:30' ? 'selected' : '' }}>5:30 PM</option>
                                    <option value="18:00" {{ old('end_time') == '18:00' ? 'selected' : '' }}>6:00 PM</option>
                                    <option value="18:30" {{ old('end_time') == '18:30' ? 'selected' : '' }}>6:30 PM</option>
                                    <option value="19:00" {{ old('end_time') == '19:00' ? 'selected' : '' }}>7:00 PM</option>
                                    <option value="19:30" {{ old('end_time') == '19:30' ? 'selected' : '' }}>7:30 PM</option>
                                    <option value="20:00" {{ old('end_time') == '20:00' ? 'selected' : '' }}>8:00 PM</option>
                                    <option value="20:30" {{ old('end_time') == '20:30' ? 'selected' : '' }}>8:30 PM</option>
                                    <option value="21:00" {{ old('end_time') == '21:00' ? 'selected' : '' }}>9:00 PM</option>
                                    <option value="21:30" {{ old('end_time') == '21:30' ? 'selected' : '' }}>9:30 PM</option>
                                    <option value="22:00" {{ old('end_time') == '22:00' ? 'selected' : '' }}>10:00 PM</option>
                                    <option value="22:30" {{ old('end_time') == '22:30' ? 'selected' : '' }}>10:30 PM</option>
                                    <option value="23:00" {{ old('end_time') == '23:00' ? 'selected' : '' }}>11:00 PM</option>
                                    <option value="23:30" {{ old('end_time') == '23:30' ? 'selected' : '' }}>11:30 PM</option>
                                </select>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teacher.availability.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Availability
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Availability
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-lightbulb me-2"></i>Tips
                </h5>
                <p class="modern-card-subtitle">Helpful information for setting availability</p>
            </div>
            <div class="modern-card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Set your available time slots for each day
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Students can only book during these times
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        You can add multiple time slots per day
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Avoid overlapping time slots
                    </li>
                </ul>
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
