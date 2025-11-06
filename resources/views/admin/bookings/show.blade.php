@extends('layouts.app')

@section('title', 'Session Details')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-3"></i>Session Details
            </h1>
            <p class="page-subtitle">View detailed information about this booking session</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-edit me-2"></i>Edit Session
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sessions
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
                    <i class="fas fa-info-circle me-2"></i>Session Information
                </h5>
                <p class="modern-card-subtitle">Complete details about this booking session</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Teacher</h5>
                        <div class="d-flex align-items-center mb-3">
                            @if($booking->teacher && $booking->teacher->user)
                            <img src="{{ $booking->teacher->user->small_profile_picture_url }}" 
                                 alt="{{ $booking->teacher->user->name }}" 
                                 class="rounded-circle me-3" 
                                 style="width: 48px; height: 48px; object-fit: cover;">
                            <div>
                                <div class="fw-bold">{{ $booking->teacher->user->name }}</div>
                                <small class="text-muted">{{ $booking->teacher->qualifications }}</small>
                            </div>
                            @else
                                <div class="text-muted">Teacher not found</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Student</h5>
                        <div class="d-flex align-items-center mb-3">
                            @if($booking->student && $booking->student->user)
                            <img src="{{ $booking->student->user->small_profile_picture_url }}" 
                                 alt="{{ $booking->student->user->name }}" 
                                 class="rounded-circle me-3" 
                                 style="width: 48px; height: 48px; object-fit: cover;">
                            <div>
                                <div class="fw-bold">{{ $booking->student->user->name }}</div>
                                <small class="text-muted">{{ $booking->student->level }} level</small>
                            </div>
                            @else
                                <div class="text-muted">Student not found</div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Session Details</h5>
                        <p><strong>Date:</strong> {{ $booking->start_time->format('M d, Y') }}</p>
                        <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                        <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Status</h5>
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning fs-6">Pending</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-info fs-6">Confirmed</span>
                        @elseif($booking->status === 'completed')
                            <span class="badge bg-success fs-6">Completed</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge bg-danger fs-6">Cancelled</span>
                        @elseif($booking->status === 'no_show')
                            <span class="badge bg-secondary fs-6">No Show</span>
                        @endif
                    </div>
                </div>

                @if($booking->notes)
                    <hr>
                    <h5>Notes</h5>
                    <p>{{ $booking->notes }}</p>
                @endif

                @if($booking->zoom_meeting_id)
                    <hr>
                    <h5>Zoom Meeting</h5>
                    <p><strong>Meeting ID:</strong> {{ $booking->zoom_meeting_id }}</p>
                    <p><strong>Join URL:</strong> <a href="{{ $booking->zoom_join_url }}" target="_blank">{{ $booking->zoom_join_url }}</a></p>
                @endif

                @if($booking->sessionRecordings->count() > 0)
                    <hr>
                    <h5>Session Recordings</h5>
                    <div class="modern-table-container">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>File Name</th>
                                    <th>Duration</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->sessionRecordings as $recording)
                                    <tr>
                                        <td>
                                            @if($recording->recording_type === 'video')
                                                <i class="fas fa-video text-primary"></i> Video
                                            @elseif($recording->recording_type === 'audio')
                                                <i class="fas fa-microphone text-success"></i> Audio
                                            @elseif($recording->recording_type === 'chat')
                                                <i class="fas fa-comments text-info"></i> Chat
                                            @else
                                                <i class="fas fa-file text-secondary"></i> {{ ucfirst($recording->recording_type) }}
                                            @endif
                                        </td>
                                        <td>{{ $recording->file_name }}</td>
                                        <td>{{ $recording->formatted_duration }}</td>
                                        <td>{{ $recording->formatted_file_size }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($recording->recording_type === 'video' || $recording->recording_type === 'shared_screen_with_speaker_view')
                                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Play Video" onclick="playRecordingDirect('{{ $recording->play_url }}', 'video', '{{ $recording->file_name }}')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @elseif($recording->recording_type === 'audio')
                                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Play Audio" onclick="playRecordingDirect('{{ $recording->play_url }}', 'audio', '{{ $recording->file_name }}')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Open Recording">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-outline-success btn-sm" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Session
                    </a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#reassignModal">
                        <i class="fas fa-exchange-alt me-2"></i>Reassign Session
                    </button>
                    <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this session?')">
                            <i class="fas fa-trash me-2"></i>Delete Session
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
<div class="modal fade" id="reassignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.bookings.reassign', $booking) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reassign Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Current: {{ $booking->teacher->user->name }} → {{ $booking->student->user->name }}</p>
                    <div class="mb-3">
                        <label for="new_teacher_id" class="form-label">New Teacher</label>
                        <select class="form-select" id="new_teacher_id" name="new_teacher_id" required>
                            <option value="">Select teacher</option>
                            @foreach(\App\Models\Teacher::with('user')->get() as $teacher)
                                <option value="{{ $teacher->id }}" {{ $teacher->id == $booking->teacher_id ? 'selected' : '' }}>
                                    {{ $teacher->user->name ?? '' }} ({{ $teacher->qualifications ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_student_id" class="form-label">New Student</label>
                        <select class="form-select" id="new_student_id" name="new_student_id" required>
                            <option value="">Select student</option>
                            @foreach(\App\Models\Student::with('user')->get() as $student)
                                <option value="{{ $student->id }}" {{ $student->id == $booking->student_id ? 'selected' : '' }}>
                                    {{ $student->user->name }} ({{ $student->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reassign</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Include unified session recording modal component --}}
@include('components.session-recording-modal')

<script>
/**
 * Play recording using iframe for better compatibility
 */
function playRecordingDirect(playUrl, recordingType, fileName) {
    console.log('Playing recording with iframe:', { playUrl, recordingType, fileName });
    
    // Check if it's a Zoom recording URL
    const isZoomRecording = playUrl.includes('zoom.us/rec/') || playUrl.includes('zoom.us/');
    const isDirectVideo = playUrl.endsWith('.mp4') || playUrl.endsWith('.webm') || playUrl.endsWith('.ogg');
    const isDirectAudio = playUrl.endsWith('.mp3') || playUrl.endsWith('.wav') || playUrl.endsWith('.m4a');
    
    // Create modal with appropriate player
    const modalHtml = `
        <div class="modal fade" id="directPlayModal" tabindex="-1" aria-labelledby="directPlayModalLabel" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="directPlayModalLabel">
                            <i class="fas fa-play-circle me-2"></i>Playing Recording
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <h6>${fileName || 'Recording'}</h6>
                            <small class="text-muted">Type: ${recordingType}</small>
                        </div>
                        
                        ${isDirectVideo 
                            ? `<video controls class="w-100" style="max-height: 500px;">
                                 <source src="${playUrl}" type="video/mp4">
                                 Your browser does not support the video tag.
                               </video>`
                            : isDirectAudio
                            ? `<audio controls class="w-100">
                                 <source src="${playUrl}" type="audio/mpeg">
                                 Your browser does not support the audio tag.
                               </audio>`
                            : isZoomRecording
                            ? `<div class="recording-container" style="position: relative; width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                                 <iframe 
                                     src="${playUrl}" 
                                     style="width: 100%; height: 100%; border: none;"
                                     allow="autoplay; fullscreen; picture-in-picture"
                                     allowfullscreen>
                                 </iframe>
                                 <div class="recording-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; color: #666;">
                                     <div class="text-center">
                                         <i class="fas fa-play-circle fa-3x mb-2"></i>
                                         <p>Loading recording...</p>
                                     </div>
                                 </div>
                               </div>`
                            : `<div class="alert alert-info">
                                 <i class="fas fa-info-circle me-2"></i>
                                 This recording type (${recordingType}) cannot be played directly in the browser.
                                 <br><br>
                                 <a href="${playUrl}" target="_blank" class="btn btn-primary">
                                     <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                 </a>
                               </div>`
                        }
                        
                        <div class="mt-3 text-center">
                            <a href="${playUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('directPlayModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('directPlayModal'));
    modal.show();
    
    // Hide overlay after iframe loads (for Zoom recordings)
    if (isZoomRecording) {
        const iframe = document.querySelector('#directPlayModal iframe');
        const overlay = document.querySelector('#directPlayModal .recording-overlay');
        
        if (iframe && overlay) {
            iframe.onload = function() {
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 2000);
            };
        }
    }
    
    // Clean up modal when hidden
    document.getElementById('directPlayModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}
</script>
@endsection

@section('styles')
<style>
    .avatar-sm {
        width: 48px;
        height: 48px;
    }
    
    .table th {
        background-color: #f8f9fc;
        border-top: none;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    #recordingModal .modal-dialog {
        max-width: 90vw;
    }
    
    #recordingModal .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }
    
    #videoPlayer, #audioPlayer {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
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
    
    /* User Info Styling */
    .user-info {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
        border: 3px solid #e9ecef;
    }
    
    .user-details h6 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .user-details small {
        color: #6c757d;
    }
    
    /* Status Badge */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
    }
    
    .status-confirmed {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }
    
    /* Button Styling */
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-light {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .btn-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
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
        
        .user-info {
            flex-direction: column;
            text-align: center;
        }
        
        .user-avatar {
            margin-right: 0;
            margin-bottom: 0.5rem;
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