@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-edit me-3"></i>Edit Booking
            </h1>
            <p class="page-subtitle">Update booking details and information</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('teacher.bookings.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-info-circle me-2"></i>Booking Information
        </h5>
        <p class="modern-card-subtitle">Update your booking details</p>
    </div>
    <div class="modern-card-body">
        <form method="POST" action="{{ route('teacher.bookings.update', $booking) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Date</label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                           id="start_date" name="start_date" 
                           value="{{ old('start_date', $booking->start_time->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <select class="form-control @error('start_time') is-invalid @enderror" 
                            id="start_time" name="start_time" required>
                        <option value="">Select start time</option>
                        <option value="00:00" {{ old('start_time', $booking->start_time->format('H:i')) == '00:00' ? 'selected' : '' }}>12:00 AM</option>
                        <option value="00:30" {{ old('start_time', $booking->start_time->format('H:i')) == '00:30' ? 'selected' : '' }}>12:30 AM</option>
                        <option value="01:00" {{ old('start_time', $booking->start_time->format('H:i')) == '01:00' ? 'selected' : '' }}>1:00 AM</option>
                        <option value="01:30" {{ old('start_time', $booking->start_time->format('H:i')) == '01:30' ? 'selected' : '' }}>1:30 AM</option>
                        <option value="02:00" {{ old('start_time', $booking->start_time->format('H:i')) == '02:00' ? 'selected' : '' }}>2:00 AM</option>
                        <option value="02:30" {{ old('start_time', $booking->start_time->format('H:i')) == '02:30' ? 'selected' : '' }}>2:30 AM</option>
                        <option value="03:00" {{ old('start_time', $booking->start_time->format('H:i')) == '03:00' ? 'selected' : '' }}>3:00 AM</option>
                        <option value="03:30" {{ old('start_time', $booking->start_time->format('H:i')) == '03:30' ? 'selected' : '' }}>3:30 AM</option>
                        <option value="04:00" {{ old('start_time', $booking->start_time->format('H:i')) == '04:00' ? 'selected' : '' }}>4:00 AM</option>
                        <option value="04:30" {{ old('start_time', $booking->start_time->format('H:i')) == '04:30' ? 'selected' : '' }}>4:30 AM</option>
                        <option value="05:00" {{ old('start_time', $booking->start_time->format('H:i')) == '05:00' ? 'selected' : '' }}>5:00 AM</option>
                        <option value="05:30" {{ old('start_time', $booking->start_time->format('H:i')) == '05:30' ? 'selected' : '' }}>5:30 AM</option>
                        <option value="06:00" {{ old('start_time', $booking->start_time->format('H:i')) == '06:00' ? 'selected' : '' }}>6:00 AM</option>
                        <option value="06:30" {{ old('start_time', $booking->start_time->format('H:i')) == '06:30' ? 'selected' : '' }}>6:30 AM</option>
                        <option value="07:00" {{ old('start_time', $booking->start_time->format('H:i')) == '07:00' ? 'selected' : '' }}>7:00 AM</option>
                        <option value="07:30" {{ old('start_time', $booking->start_time->format('H:i')) == '07:30' ? 'selected' : '' }}>7:30 AM</option>
                        <option value="08:00" {{ old('start_time', $booking->start_time->format('H:i')) == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                        <option value="08:30" {{ old('start_time', $booking->start_time->format('H:i')) == '08:30' ? 'selected' : '' }}>8:30 AM</option>
                        <option value="09:00" {{ old('start_time', $booking->start_time->format('H:i')) == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                        <option value="09:30" {{ old('start_time', $booking->start_time->format('H:i')) == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                        <option value="10:00" {{ old('start_time', $booking->start_time->format('H:i')) == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                        <option value="10:30" {{ old('start_time', $booking->start_time->format('H:i')) == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                        <option value="11:00" {{ old('start_time', $booking->start_time->format('H:i')) == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                        <option value="11:30" {{ old('start_time', $booking->start_time->format('H:i')) == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                        <option value="12:00" {{ old('start_time', $booking->start_time->format('H:i')) == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                        <option value="12:30" {{ old('start_time', $booking->start_time->format('H:i')) == '12:30' ? 'selected' : '' }}>12:30 PM</option>
                        <option value="13:00" {{ old('start_time', $booking->start_time->format('H:i')) == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                        <option value="13:30" {{ old('start_time', $booking->start_time->format('H:i')) == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                        <option value="14:00" {{ old('start_time', $booking->start_time->format('H:i')) == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                        <option value="14:30" {{ old('start_time', $booking->start_time->format('H:i')) == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                        <option value="15:00" {{ old('start_time', $booking->start_time->format('H:i')) == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                        <option value="15:30" {{ old('start_time', $booking->start_time->format('H:i')) == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                        <option value="16:00" {{ old('start_time', $booking->start_time->format('H:i')) == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                        <option value="16:30" {{ old('start_time', $booking->start_time->format('H:i')) == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                        <option value="17:00" {{ old('start_time', $booking->start_time->format('H:i')) == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                        <option value="17:30" {{ old('start_time', $booking->start_time->format('H:i')) == '17:30' ? 'selected' : '' }}>5:30 PM</option>
                        <option value="18:00" {{ old('start_time', $booking->start_time->format('H:i')) == '18:00' ? 'selected' : '' }}>6:00 PM</option>
                        <option value="18:30" {{ old('start_time', $booking->start_time->format('H:i')) == '18:30' ? 'selected' : '' }}>6:30 PM</option>
                        <option value="19:00" {{ old('start_time', $booking->start_time->format('H:i')) == '19:00' ? 'selected' : '' }}>7:00 PM</option>
                        <option value="19:30" {{ old('start_time', $booking->start_time->format('H:i')) == '19:30' ? 'selected' : '' }}>7:30 PM</option>
                        <option value="20:00" {{ old('start_time', $booking->start_time->format('H:i')) == '20:00' ? 'selected' : '' }}>8:00 PM</option>
                        <option value="20:30" {{ old('start_time', $booking->start_time->format('H:i')) == '20:30' ? 'selected' : '' }}>8:30 PM</option>
                        <option value="21:00" {{ old('start_time', $booking->start_time->format('H:i')) == '21:00' ? 'selected' : '' }}>9:00 PM</option>
                        <option value="21:30" {{ old('start_time', $booking->start_time->format('H:i')) == '21:30' ? 'selected' : '' }}>9:30 PM</option>
                        <option value="22:00" {{ old('start_time', $booking->start_time->format('H:i')) == '22:00' ? 'selected' : '' }}>10:00 PM</option>
                        <option value="22:30" {{ old('start_time', $booking->start_time->format('H:i')) == '22:30' ? 'selected' : '' }}>10:30 PM</option>
                        <option value="23:00" {{ old('start_time', $booking->start_time->format('H:i')) == '23:00' ? 'selected' : '' }}>11:00 PM</option>
                        <option value="23:30" {{ old('start_time', $booking->start_time->format('H:i')) == '23:30' ? 'selected' : '' }}>11:30 PM</option>
                    </select>
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3 d-none">
                    <label for="duration_minutes" class="form-label">Duration</label>
                    <select class="form-control @error('duration_minutes') is-invalid @enderror" 
                            id="duration_minutes" name="duration_minutes" required>
                        <option value="">Select duration</option>
                        <option value="30" {{ old('duration_minutes', $booking->duration_minutes) == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="60" {{ old('duration_minutes', $booking->duration_minutes) == '60' ? 'selected' : '' }}>1 hour</option>
                        <option value="90" {{ old('duration_minutes', $booking->duration_minutes) == '90' ? 'selected' : '' }}>1.5 hours</option>
                        <option value="120" {{ old('duration_minutes', $booking->duration_minutes) == '120' ? 'selected' : '' }}>2 hours</option>
                        <option value="150" {{ old('duration_minutes', $booking->duration_minutes) == '150' ? 'selected' : '' }}>2.5 hours</option>
                        <option value="180" {{ old('duration_minutes', $booking->duration_minutes) == '180' ? 'selected' : '' }}>3 hours</option>
                        <option value="240" {{ old('duration_minutes', $booking->duration_minutes) == '240' ? 'selected' : '' }}>4 hours</option>
                        <option value="300" {{ old('duration_minutes', $booking->duration_minutes) == '300' ? 'selected' : '' }}>5 hours</option>
                        <option value="360" {{ old('duration_minutes', $booking->duration_minutes) == '360' ? 'selected' : '' }}>6 hours</option>
                        <option value="480" {{ old('duration_minutes', $booking->duration_minutes) == '480' ? 'selected' : '' }}>8 hours</option>
                    </select>
                    @error('duration_minutes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="pending" {{ old('status', $booking->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ old('status', $booking->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ old('status', $booking->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Note:</strong> Cancellations are not allowed within {{ \App\Models\SystemSetting::getValue('cancellation_policy_hours', 24) }} hours of the scheduled time.
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="zoom_join_url" class="form-label">Zoom Meeting URL (For Student)</label>
                <input type="url" class="form-control @error('zoom_join_url') is-invalid @enderror" 
                       id="zoom_join_url" name="zoom_join_url" 
                       value="{{ old('zoom_join_url', $booking->zoom_join_url) }}">
                @error('zoom_join_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="zoom_start_url" class="form-label">Zoom Meeting Start URL (For Teacher)</label>
                <input type="url" class="form-control @error('zoom_start_url') is-invalid @enderror" 
                       id="zoom_start_url" name="zoom_start_url" 
                       value="{{ old('zoom_start_url', $booking->zoom_start_url) }}">
                @error('zoom_start_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('teacher.bookings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Booking
                </button>
            </div>
        </form>
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
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
    }
    
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
    }
    
    /* Button Styling */
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
        transform: translateY(-2px);
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
