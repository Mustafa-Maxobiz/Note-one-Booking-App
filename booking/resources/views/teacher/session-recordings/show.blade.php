@extends('layouts.app')

@section('title', 'Session Recording Details')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-video me-3"></i>Recording Details
            </h1>
            <p class="page-subtitle">Session #{{ $recording->booking->id }} - {{ $recording->booking->start_time->format('M d, Y') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('teacher.session-recordings.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Recordings
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-light">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-info-circle me-2"></i>Recording Information
                </h5>
                <p class="modern-card-subtitle">Session and recording details</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Session Details</h6>
                        <p><strong>Date:</strong> {{ $recording->booking->start_time->format('M d, Y') }}</p>
                        <p><strong>Time:</strong> {{ $recording->booking->start_time->format('g:i A') }} - {{ $recording->booking->end_time->format('g:i A') }}</p>
                        <p><strong>Duration:</strong> {{ $recording->booking->duration_minutes }} minutes</p>
                        <p><strong>Student:</strong> {{ $recording->booking->student->user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Recording Details</h6>
                        <p><strong>Type:</strong> 
                            @if($recording->recording_type === 'video')
                                <span class="badge bg-primary"><i class="fas fa-video me-1"></i>Video</span>
                            @elseif($recording->recording_type === 'audio')
                                <span class="badge bg-success"><i class="fas fa-microphone me-1"></i>Audio</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($recording->recording_type) }}</span>
                            @endif
                        </p>
                        <p><strong>File Name:</strong> {{ $recording->file_name }}</p>
                        <p><strong>Duration:</strong> {{ $recording->formatted_duration }}</p>
                        <p><strong>File Size:</strong> {{ $recording->formatted_file_size }}</p>
                        <p><strong>Created:</strong> {{ $recording->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($recording->play_url)
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-play-circle me-2"></i>Recording Player
                </h5>
                <p class="modern-card-subtitle">Watch or listen to your recording</p>
            </div>
            <div class="modern-card-body">
                <div class="text-center">
                    @if($recording->recording_type === 'video' || $recording->recording_type === 'shared_screen_with_speaker_view')
                        <video controls class="w-100" style="max-height: 500px;">
                            <source src="{{ $recording->play_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($recording->recording_type === 'audio')
                        <audio controls class="w-100">
                            <source src="{{ $recording->play_url }}" type="audio/mpeg">
                            Your browser does not support the audio tag.
                        </audio>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This recording type ({{ $recording->recording_type }}) cannot be played directly in the browser.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
                <p class="modern-card-subtitle">Manage your recording</p>
            </div>
            <div class="modern-card-body">
                <div class="d-grid gap-2">
                    @if($recording->play_url)
                        @if($recording->recording_type === 'video' || $recording->recording_type === 'shared_screen_with_speaker_view')
                            <button type="button" class="btn btn-primary" onclick="playRecordingDirect('{{ $recording->play_url }}', 'video', '{{ $recording->file_name }}')">
                                <i class="fas fa-play me-2"></i>Play Video
                            </button>
                        @elseif($recording->recording_type === 'audio')
                            <button type="button" class="btn btn-primary" onclick="playRecordingDirect('{{ $recording->play_url }}', 'audio', '{{ $recording->file_name }}')">
                                <i class="fas fa-play me-2"></i>Play Audio
                            </button>
                        @else
                            <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Open Recording
                            </a>
                        @endif
                    @endif
                    
                    @if($recording->download_url)
                        <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-outline-info">
                            <i class="fas fa-download me-2"></i>Download Recording
                        </a>
                    @endif
                    
                    <a href="{{ route('teacher.session-recordings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>All Recordings
                    </a>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-calendar-alt me-2"></i>Session Information
                </h5>
                <p class="modern-card-subtitle">Session details and information</p>
            </div>
            <div class="modern-card-body">
                <p><strong>Booking ID:</strong> #{{ $recording->booking->id }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $recording->booking->status === 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($recording->booking->status) }}
                    </span>
                </p>
                @if($recording->booking->notes)
                    <p><strong>Notes:</strong> {{ $recording->booking->notes }}</p>
                @endif
            </div>
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
</style>
@endsection