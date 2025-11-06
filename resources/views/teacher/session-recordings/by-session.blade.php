@extends('layouts.app')

@section('title', 'Session Recordings')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-video me-3"></i>Session Recordings
            </h1>
            <p class="page-subtitle">Recordings for session #{{ $booking->id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('teacher.session-recordings.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>All Recordings
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

<!-- Session Information -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Session Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Date:</strong> {{ $booking->start_time->format('M d, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>
            </div>
            <div class="col-md-6">
                <p><strong>Student:</strong> {{ $booking->student->user->name }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
                @if($booking->notes)
                    <p><strong>Notes:</strong> {{ $booking->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recordings List -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Session Recordings ({{ $recordings->count() }})</h6>
    </div>
    <div class="card-body">
        @if($recordings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>File Name</th>
                            <th>Duration</th>
                            <th>File Size</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recordings as $recording)
                            <tr>
                                <td>
                                    @if($recording->recording_type === 'video')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-video me-1"></i>Video
                                        </span>
                                    @elseif($recording->recording_type === 'audio')
                                        <span class="badge bg-success">
                                            <i class="fas fa-microphone me-1"></i>Audio
                                        </span>
                                    @elseif($recording->recording_type === 'chat')
                                        <span class="badge bg-info">
                                            <i class="fas fa-comments me-1"></i>Chat
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-file me-1"></i>{{ ucfirst($recording->recording_type) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $recording->file_name }}">
                                        {{ $recording->file_name }}
                                    </span>
                                </td>
                                <td>{{ $recording->formatted_duration }}</td>
                                <td>{{ $recording->formatted_file_size }}</td>
                                <td>{{ $recording->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('teacher.session-recordings.show', $recording) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($recording->play_url)
                                            @if($recording->recording_type === 'video' || $recording->recording_type === 'shared_screen_with_speaker_view')
                                                <button type="button" class="btn btn-outline-success" title="Play Video" onclick="playRecordingDirect('{{ $recording->play_url }}', 'video', '{{ $recording->file_name }}')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @elseif($recording->recording_type === 'audio')
                                                <button type="button" class="btn btn-outline-success" title="Play Audio" onclick="playRecordingDirect('{{ $recording->play_url }}', 'audio', '{{ $recording->file_name }}')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @else
                                                <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-outline-success" title="Open Recording">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if($recording->download_url)
                                            <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-outline-info" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Recordings Found</h5>
                <p class="text-muted">No recordings are available for this session yet.</p>
            </div>
        @endif
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