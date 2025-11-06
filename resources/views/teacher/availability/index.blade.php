@extends('layouts.app')

@section('title', 'My Availability')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-clock me-3"></i>My Availability
            </h1>
            <p class="page-subtitle">Manage your teaching schedule and available time slots</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                <i class="fas fa-plus me-2"></i>Add Time Slot
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-calendar-week me-2"></i>My Weekly Schedule
                </h5>
                <p class="modern-card-subtitle">Manage your available time slots for students to book</p>
            </div>
            <div class="modern-card-body">
                @if($availabilities->count() > 0)
                    <div class="availability-grid">
                        @foreach($availabilities->groupBy('day_of_week') as $day => $dayAvailabilities)
                            <div class="day-schedule">
                                <div class="day-header">
                                    <h6 class="day-name">{{ ucfirst($day) }}</h6>
                                    <span class="day-count">{{ $dayAvailabilities->count() }} slot{{ $dayAvailabilities->count() > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="time-slots">
                                    @foreach($dayAvailabilities as $availability)
                                        <div class="time-slot">
                                            <div class="slot-time">
                                                <i class="fas fa-clock me-2"></i>
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $availability->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $availability->end_time)->format('g:i A') }}
                                            </div>
                                            <div class="slot-duration">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $availability->start_time)->diffInMinutes(\Carbon\Carbon::createFromFormat('H:i:s', $availability->end_time)) }} min
                                            </div>
                                            <div class="slot-actions">
                                                <form method="POST" action="{{ route('teacher.availability.destroy', $availability) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this time slot?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="empty-state-title">No availability set</h5>
                        <p class="empty-state-description">Add your available time slots to start receiving bookings from students.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                            <i class="fas fa-plus me-2"></i>Add Your First Time Slot
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-chart-bar me-2"></i>Availability Stats
                </h5>
                <p class="modern-card-subtitle">Your schedule overview</p>
            </div>
            <div class="modern-card-body">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon stat-icon-primary">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $availabilities->count() }}</div>
                            <div class="stat-label">Time Slots</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon stat-icon-success">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $availabilities->groupBy('day_of_week')->count() }}</div>
                            <div class="stat-label">Days Available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modern-card mt-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-lightbulb me-2"></i>Best Practices
                </h5>
                <p class="modern-card-subtitle">Tips for better availability management</p>
            </div>
            <div class="modern-card-body">
                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Be Consistent</h6>
                            <p>Keep your schedule steady so students can rely on your availability each week.</p>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Update Regularly</h6>
                            <p>Review your schedule and adjust your availability as needed.</p>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Confirm Early</h6>
                            <p>Accept or decline new session requests within 24 hours to keep the system up to date and students engaged.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Availability Modal -->
<div class="modal fade" id="addAvailabilityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modern-modal">
            <form method="POST" action="{{ route('teacher.availability.store') }}" class="availability-form">
                @csrf
                <div class="modal-header modern-modal-header">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-plus-circle me-2"></i>Add Time Slot
                    </h5>
                    <button type="button" class="btn-close text-white bg-body" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modern-modal-body">
                    <div class="form-group">
                        <label for="day_of_week" class="form-label">
                            <i class="fas fa-calendar-day me-2"></i>Day of Week
                        </label>
                        <select class="form-select modern-select" id="day_of_week" name="day_of_week" required>
                            <option value="">Select a day</option>
                            <option value="monday">Monday</option>
                            <option value="tuesday">Tuesday</option>
                            <option value="wednesday">Wednesday</option>
                            <option value="thursday">Thursday</option>
                            <option value="friday">Friday</option>
                            <option value="saturday">Saturday</option>
                            <option value="sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_time" class="form-label">
                                <i class="fas fa-play me-2"></i>Start Time
                            </label>
                            <select class="form-select modern-select" id="start_time" name="start_time" required>
                                <option value="">Select start time</option>
                                <option value="00:00">12:00 AM</option>
                                <option value="00:30">12:30 AM</option>
                                <option value="01:00">1:00 AM</option>
                                <option value="01:30">1:30 AM</option>
                                <option value="02:00">2:00 AM</option>
                                <option value="02:30">2:30 AM</option>
                                <option value="03:00">3:00 AM</option>
                                <option value="03:30">3:30 AM</option>
                                <option value="04:00">4:00 AM</option>
                                <option value="04:30">4:30 AM</option>
                                <option value="05:00">5:00 AM</option>
                                <option value="05:30">5:30 AM</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="06:30">6:30 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="07:30">7:30 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="08:30">8:30 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="09:30">9:30 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="10:30">10:30 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="11:30">11:30 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="12:30">12:30 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="13:30">1:30 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="14:30">2:30 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="15:30">3:30 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="16:30">4:30 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="17:30">5:30 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="18:30">6:30 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="19:30">7:30 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="20:30">8:30 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="21:30">9:30 PM</option>
                                <option value="22:00">10:00 PM</option>
                                <option value="22:30">10:30 PM</option>
                                <option value="23:00">11:00 PM</option>
                                <option value="23:30">11:30 PM</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_time" class="form-label">
                                <i class="fas fa-stop me-2"></i>End Time
                            </label>
                            <select class="form-select modern-select" id="end_time" name="end_time" required>
                                <option value="">Select end time</option>
                                <option value="00:00">12:00 AM</option>
                                <option value="00:30">12:30 AM</option>
                                <option value="01:00">1:00 AM</option>
                                <option value="01:30">1:30 AM</option>
                                <option value="02:00">2:00 AM</option>
                                <option value="02:30">2:30 AM</option>
                                <option value="03:00">3:00 AM</option>
                                <option value="03:30">3:30 AM</option>
                                <option value="04:00">4:00 AM</option>
                                <option value="04:30">4:30 AM</option>
                                <option value="05:00">5:00 AM</option>
                                <option value="05:30">5:30 AM</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="06:30">6:30 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="07:30">7:30 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="08:30">8:30 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="09:30">9:30 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="10:30">10:30 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="11:30">11:30 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="12:30">12:30 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="13:30">1:30 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="14:30">2:30 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="15:30">3:30 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="16:30">4:30 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="17:30">5:30 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="18:30">6:30 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="19:30">7:30 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="20:30">8:30 PM</option>
                                <option value="21:00">9:00 PM</option>
                                <option value="21:30">9:30 PM</option>
                                <option value="22:00">10:00 PM</option>
                                <option value="22:30">10:30 PM</option>
                                <option value="23:00">11:00 PM</option>
                                <option value="23:30">11:30 PM</option>
                            </select>
                        </div>
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
                        <i class="fas fa-plus me-2"></i>Add Time Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startTimeSelect = document.getElementById('start_time');
    const endTimeSelect = document.getElementById('end_time');
    
    // Function to generate time options
    function generateTimeOptions(startValue = '') {
        const times = [
            '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30',
            '04:00', '04:30', '05:00', '05:30', '06:00', '06:30', '07:00', '07:30',
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
            '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
        ];
        
        const displayTimes = [
            '12:00 AM', '12:30 AM', '1:00 AM', '1:30 AM', '2:00 AM', '2:30 AM', '3:00 AM', '3:30 AM',
            '4:00 AM', '4:30 AM', '5:00 AM', '5:30 AM', '6:00 AM', '6:30 AM', '7:00 AM', '7:30 AM',
            '8:00 AM', '8:30 AM', '9:00 AM', '9:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM',
            '12:00 PM', '12:30 PM', '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM',
            '4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM', '6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM',
            '8:00 PM', '8:30 PM', '9:00 PM', '9:30 PM', '10:00 PM', '10:30 PM', '11:00 PM', '11:30 PM'
        ];
        
        // Clear existing options
        endTimeSelect.innerHTML = '<option value="">Select end time</option>';
        
        if (startValue) {
            // Find the index of the start time
            const startIndex = times.indexOf(startValue);
            
            // Add options after the start time
            for (let i = startIndex + 1; i < times.length; i++) {
                const option = document.createElement('option');
                option.value = times[i];
                option.textContent = displayTimes[i];
                endTimeSelect.appendChild(option);
            }
        } else {
            // If no start time selected, show all options
            times.forEach((time, index) => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = displayTimes[index];
                endTimeSelect.appendChild(option);
            });
        }
    }
    
    // Update end time options when start time changes
    startTimeSelect.addEventListener('change', function() {
        const selectedStartTime = this.value;
        generateTimeOptions(selectedStartTime);
        
        // Clear end time selection if it's now invalid
        if (selectedStartTime && endTimeSelect.value && endTimeSelect.value <= selectedStartTime) {
            endTimeSelect.value = '';
        }
    });
    
    // Initialize end time options
    generateTimeOptions();
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
    
    /* Availability Grid */
    .availability-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .day-schedule {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        border-left: 4px solid #ef473e;
        transition: all 0.3s ease;
    }
    
    .day-schedule:hover {
        background: #e9ecef;
        transform: translateX(4px);
    }
    
    .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .day-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    .day-count {
        background: linear-gradient(135deg, #ef473e, #fdb838);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .time-slots {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .time-slot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .time-slot:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
    
    .slot-time {
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }
    
    .slot-time i {
        color: #ef473e;
    }
    
    .slot-duration {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .slot-actions {
        display: flex;
        gap: 0.5rem;
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
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    }
    
    .modern-modal-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e9ecef;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }
    
    .modern-modal-title {
        font-size: 1.25rem;
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
    
    /* Form Elements */
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
        background: #fafafa;
    }
    
    .modern-input:focus,
    .modern-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);
        background: white;
        outline: none;
    }
    
    .modern-alert {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #f5c6cb;
        color: #721c24;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
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
        
        .time-slot {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .slot-actions {
            align-self: flex-end;
        }
        
        .day-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
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
</style>
@endsection

@section('scripts')
<script>
    // Auto-fill end time when start time changes
    document.getElementById('start_time').addEventListener('change', function() {
        const startTime = this.value;
        const endTimeInput = document.getElementById('end_time');
        
        if (startTime) {
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(start.getTime() + (60 * 60 * 1000)); // Add 1 hour
            endTimeInput.value = end.toTimeString().slice(0, 5);
        }
    });
</script>
@endsection
