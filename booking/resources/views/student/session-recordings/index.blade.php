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
            <p class="page-subtitle">Access and review your recorded lesson sessions</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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

<div class="modern-card">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-list me-2"></i>All Session Recordings
        </h5>
        <p class="modern-card-subtitle">Browse and access your recorded lesson sessions</p>
    </div>
    <div class="modern-card-body">
        @if($recordings->count() > 0)
            <div class="modern-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Recording</th>
                            <th>Teacher</th>
                            <th>Passcode</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recordings as $recording)
                            <tr class="recording-row">
                                <td>
                                    <div class="recording-info">
                                        <div class="recording-icon">
                                            @if($recording->recording_type === 'video')
                                                <i class="fas fa-video text-primary"></i>
                                            @elseif($recording->recording_type === 'audio')
                                                <i class="fas fa-microphone text-success"></i>
                                            @elseif($recording->recording_type === 'chat')
                                                <i class="fas fa-comments text-info"></i>
                                            @else
                                                <i class="fas fa-file text-secondary"></i>
                                            @endif
                                        </div>
                                        <div class="recording-details">
                                            <div class="recording-name">
                                                {{ $recording->file_name }}
                                            </div>
                                            <div class="recording-type">
                                                {{ ucfirst($recording->recording_type) }}
                                            </div>
                                            @if($recording->formatted_duration)
                                                <div class="recording-meta">
                                                    <i class="fas fa-clock me-1"></i>{{ $recording->formatted_duration }}
                                                    @if($recording->formatted_file_size)
                                                        <span class="mx-2">•</span>
                                                        <i class="fas fa-hdd me-1"></i>{{ $recording->formatted_file_size }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="teacher-info">
                                        <div class="teacher-name">{{ $recording->booking->teacher->user->name ?? 'N/A' }}</div>
                                        <div class="session-duration">{{ $recording->booking->duration_minutes }} min</div>
                                    </div>
                                </td>
                                <td>
                                    @if($recording->passcode)
                                        <span class="passcode-badge" title="Click to copy" onclick="copyToClipboard('{{ $recording->passcode }}', this)" style="cursor: pointer;">
                                            <i class="fas fa-key me-1"></i>{{ $recording->passcode }}
                                        </span>
                                    @else
                                        <span class="no-passcode">No passcode</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div class="date-main">{{ $recording->booking->start_time->format('M d, Y') }}</div>
                                        <div class="date-time">{{ $recording->booking->start_time->format('g:i A') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('student.session-recordings.show', $recording) }}" class="btn btn-sm btn-outline-info action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($recording->play_url)
                                            @if($recording->recording_type === 'video' || $recording->recording_type === 'shared_screen_with_speaker_view')
                                                <button type="button" class="btn btn-sm btn-outline-primary action-btn" title="Play Video" onclick="playRecordingDirect('{{ $recording->play_url }}', 'video', '{{ $recording->file_name }}')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @elseif($recording->recording_type === 'audio')
                                                <button type="button" class="btn btn-sm btn-outline-primary action-btn" title="Play Audio" onclick="playRecordingDirect('{{ $recording->play_url }}', 'audio', '{{ $recording->file_name }}')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @else
                                                <a href="{{ $recording->play_url }}" target="_blank" class="btn btn-sm btn-outline-primary action-btn" title="Open Recording">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if($recording->download_url)
                                            <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-sm btn-outline-success action-btn" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        @if(isset($recording->additional_files_count) && $recording->additional_files_count > 0)
                                            <button type="button" class="btn btn-sm btn-outline-warning action-btn" title="View All Files ({{ $recording->additional_files_count }} additional)" onclick="showAllFiles({{ json_encode($recording->all_recordings_data) }})">
                                                <i class="fas fa-files"></i> +{{ $recording->additional_files_count }}
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $recordings->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Session Recordings Found</h5>
                <p class="text-muted">You don't have any session recordings yet. Recordings will appear here after your sessions are completed.</p>
                <a href="{{ route('student.bookings.index') }}" class="btn btn-primary">
                    <i class="fas fa-calendar me-2"></i>View Bookings
                </a>
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
    
    /* Recording Info Styles */
    .recording-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .recording-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 18px;
    }
    
    .recording-details {
        flex: 1;
        min-width: 0;
    }
    
    .recording-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
        word-break: break-word;
    }
    
    .recording-type {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 6px;
    }
    
    .recording-meta {
        font-size: 0.8rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .teacher-info, .student-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .teacher-name, .student-name {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .session-duration {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .date-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .date-main {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .date-time {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* Passcode Styles */
    .passcode-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .passcode-badge:hover {
        background: #c3e6cb;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .no-passcode {
        color: #6c757d;
        font-size: 0.875rem;
        font-style: italic;
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
        
        .recording-info {
            flex-direction: column;
            gap: 8px;
        }
        
        .action-buttons {
            justify-content: center;
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

<script>
function copyToClipboard(text, targetElement) {
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showCopySuccess(targetElement);
        }).catch(function(err) {
            console.error('Clipboard API failed: ', err);
            fallbackCopyTextToClipboard(text, targetElement);
        });
    } else {
        // Fallback for older browsers or non-secure contexts
        fallbackCopyTextToClipboard(text, targetElement);
    }
}

function fallbackCopyTextToClipboard(text, target) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(target);
        } else {
            showCopyError(target);
        }
    } catch (err) {
        console.error('Fallback copy failed: ', err);
        showCopyError(target);
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess(target) {
    const originalText = target.textContent;
    target.textContent = 'Copied!';
    target.style.background = '#28a745';
    target.style.color = 'white';
    
    setTimeout(function() {
        target.textContent = originalText;
        target.style.background = '#d4edda';
        target.style.color = '#155724';
    }, 1500);
}

function showCopyError(target) {
    const originalText = target.textContent;
    target.textContent = 'Copy failed';
    target.style.background = '#dc3545';
    target.style.color = 'white';
    
    setTimeout(function() {
        target.textContent = originalText;
        target.style.background = '#d4edda';
        target.style.color = '#155724';
    }, 2000);
}
</script>
@endsection
