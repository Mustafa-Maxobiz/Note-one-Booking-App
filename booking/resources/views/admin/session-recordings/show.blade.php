@extends('layouts.app')

@section('title', 'Session Recording Details')

@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-video me-3"></i>Session Recording Details
            </h1>
            <p class="page-subtitle">View and manage session recording information</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.session-recordings.edit', $session_recording) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-edit me-2"></i>Edit Recording
                </a>
                <form method="POST" action="{{ route('admin.session-recordings.destroy', $session_recording) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-light me-2" onclick="return confirm('Are you sure you want to delete this recording?')">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
                <a href="{{ route('admin.session-recordings.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Recordings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Recording Details -->
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-info-circle me-2"></i>Recording Information
                </h5>
                <p class="modern-card-subtitle">Complete details about this session recording</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Recording Details</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>File Name:</strong></td>
                                <td>{{ $session_recording->file_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Recording Type:</strong></td>
                                <td>
                                    @if($session_recording->recording_type === 'video')
                                        <span class="badge bg-primary"><i class="fas fa-video me-1"></i>Video</span>
                                    @elseif($session_recording->recording_type === 'audio')
                                        <span class="badge bg-success"><i class="fas fa-microphone me-1"></i>Audio</span>
                                    @elseif($session_recording->recording_type === 'chat')
                                        <span class="badge bg-info"><i class="fas fa-comments me-1"></i>Chat</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-file me-1"></i>{{ ucfirst($session_recording->recording_type) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $session_recording->formatted_duration }}</td>
                            </tr>
                            <tr>
                                <td><strong>File Size:</strong></td>
                                <td>{{ $session_recording->formatted_file_size }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $session_recording->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Session Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Session ID:</strong></td>
                                <td>#{{ $session_recording->session_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Teacher:</strong></td>
                                <td>
                                    @if($session_recording->session && $session_recording->booking->teacher && $session_recording->booking->teacher->user)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $session_recording->booking->teacher->user->small_profile_picture_url }}"
                                                 alt="{{ $session_recording->booking->teacher->user->name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $session_recording->booking->teacher->user->name }}</div>
                                                <small class="text-muted">{{ $session_recording->booking->teacher->qualifications }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Teacher information not available</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Student:</strong></td>
                                <td>
                                    @if($session_recording->session && $session_recording->booking->student && $session_recording->booking->student->user)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $session_recording->booking->student->user->small_profile_picture_url }}"
                                                 alt="{{ $session_recording->booking->student->user->name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $session_recording->booking->student->user->name }}</div>
                                                <small class="text-muted">{{ $session_recording->booking->student->level }} level</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Student information not available</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Session Date:</strong></td>
                                <td>
                                    @if($session_recording->session && $session_recording->booking->start_time)
                                        {{ $session_recording->booking->start_time->format('M d, Y g:i A') }}
                                    @else
                                        <span class="text-muted">Date not available</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Session Status:</strong></td>
                                <td>
                                    @if($session_recording->session && $session_recording->booking->status)
                                        @if($session_recording->booking->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($session_recording->booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($session_recording->booking->status) }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Status not available</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recording Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-play-circle me-2"></i>Recording Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        @if($session_recording->play_url)
                            @if($session_recording->recording_type === 'video' || $session_recording->recording_type === 'shared_screen_with_speaker_view')
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="playRecordingDirect('{{ $session_recording->play_url }}', 'video', '{{ $session_recording->file_name }}')">
                                    <i class="fas fa-play me-2"></i>Play Video
                                </button>
                            @elseif($session_recording->recording_type === 'audio')
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="playRecordingDirect('{{ $session_recording->play_url }}', 'audio', '{{ $session_recording->file_name }}')">
                                    <i class="fas fa-play me-2"></i>Play Audio
                                </button>
                            @else
                                <a href="{{ $session_recording->play_url }}" target="_blank" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-external-link-alt me-2"></i>Open Recording
                                </a>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ $session_recording->download_url }}" target="_blank" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-download me-2"></i>Download Recording
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        @if($session_recording->session)
                            <a href="{{ route('admin.bookings.show', $session_recording->session) }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-eye me-2"></i>View Session
                            </a>
                        @else
                            <button class="btn btn-info btn-lg w-100" disabled>
                                <i class="fas fa-eye me-2"></i>View Session
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.session-recordings.edit', $session_recording) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Recording
                    </a>
                    @if($session_recording->session)
                        <a href="{{ route('admin.bookings.show', $session_recording->session) }}" class="btn btn-info">
                            <i class="fas fa-calendar me-2"></i>View Session Details
                        </a>
                        @if($session_recording->booking->teacher && $session_recording->booking->teacher->user)
                            <a href="{{ route('admin.users.show', $session_recording->booking->teacher->user) }}" class="btn btn-primary">
                                <i class="fas fa-chalkboard-teacher me-2"></i>View Teacher Profile
                            </a>
                        @else
                            <button class="btn btn-primary" disabled>
                                <i class="fas fa-chalkboard-teacher me-2"></i>View Teacher Profile
                            </button>
                        @endif
                        @if($session_recording->booking->student && $session_recording->booking->student->user)
                            <a href="{{ route('admin.users.show', $session_recording->booking->student->user) }}" class="btn btn-success">
                                <i class="fas fa-user-graduate me-2"></i>View Student Profile
                            </a>
                        @else
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-user-graduate me-2"></i>View Student Profile
                            </button>
                        @endif
                    @else
                        <button class="btn btn-info" disabled>
                            <i class="fas fa-calendar me-2"></i>View Session Details
                        </button>
                        <button class="btn btn-primary" disabled>
                            <i class="fas fa-chalkboard-teacher me-2"></i>View Teacher Profile
                        </button>
                        <button class="btn btn-success" disabled>
                            <i class="fas fa-user-graduate me-2"></i>View Student Profile
                        </button>
                    @endif
                    <form method="POST" action="{{ route('admin.session-recordings.destroy', $session_recording) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this recording? This action cannot be undone.')">
                            <i class="fas fa-trash me-2"></i>Delete Recording
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recording Metadata -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database me-2"></i>Recording Metadata
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>{{ $session_recording->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Zoom Meeting ID:</strong></td>
                        <td>{{ $session_recording->zoom_meeting_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Recording ID:</strong></td>
                        <td>{{ $session_recording->zoom_recording_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>File Extension:</strong></td>
                        <td>{{ pathinfo($session_recording->file_name, PATHINFO_EXTENSION) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated:</strong></td>
                        <td>{{ $session_recording->updated_at->format('M d, Y g:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

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
                        <h5 class="modal-title text-white" id="directPlayModalLabel">
                            <i class="fas fa-play-circle me-2"></i>Playing Recording
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
    
    /* Table Styling */
    .table-borderless td {
        border: none;
        padding: 0.75rem 0;
        vertical-align: middle;
    }
    
    .table-borderless td:first-child {
        font-weight: 600;
        color: #2c3e50;
        width: 40%;
    }
    
    .table-borderless td:last-child {
        color: #6c757d;
    }
    
    /* Badge Styling */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }
    
    .bg-success {
        background: linear-gradient(135deg, #28a745, #1e7e34) !important;
    }
    
    .bg-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800) !important;
        color: #212529 !important;
    }
    
    .bg-danger {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
    }
    
    .bg-info {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
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
        
        .table-borderless td:first-child {
            width: 100%;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .table-borderless td:last-child {
            display: block;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-borderless tr:last-child td:last-child {
            border-bottom: none;
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

