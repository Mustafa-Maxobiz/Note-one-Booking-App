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
            <p class="page-subtitle">Manage and view all session recordings</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="results-info">
                <i class="fas fa-info-circle me-2"></i>
                Showing {{ $recordings->firstItem() ?? 0 }} to {{ $recordings->lastItem() ?? 0 }} of {{ $recordings->total() }} results
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="modern-card mb-4">
    <div class="modern-card-header">
        <h5 class="modern-card-title">
            <i class="fas fa-filter me-2"></i>Advanced Filters
        </h5>
        <p class="modern-card-subtitle">Filter recordings by type, date, and other criteria</p>
    </div>
    <div class="modern-card-body">
        <form method="GET" action="{{ route('admin.session-recordings.index') }}" id="filterForm" class="filter-form">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-2"></i>Search
                    </label>
                    <input type="text" class="form-control modern-input" id="search" name="search" value="{{ request('search') }}" placeholder="Search recordings...">
                </div>
                <div class="filter-group">
                    <label for="type" class="form-label">
                        <i class="fas fa-file-video me-2"></i>Type
                    </label>
                    <select class="form-select modern-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="chat" {{ request('type') == 'chat' ? 'selected' : '' }}>Chat</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from" class="form-label">
                        <i class="fas fa-calendar me-2"></i>From Date
                    </label>
                    <input type="date" class="form-control modern-input" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label for="date_to" class="form-label">
                        <i class="fas fa-calendar me-2"></i>To Date
                    </label>
                    <input type="date" class="form-control modern-input" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary filter-btn">
                    <i class="fas fa-search me-2"></i>Apply Filters
                </button>
                <a href="{{ route('admin.session-recordings.index') }}" class="btn btn-secondary clear-btn">
                    <i class="fas fa-times me-2"></i>Clear All
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions Toolbar -->
<div id="bulkActionsToolbar" class="modern-card mb-3" style="display: none;">
    <div class="modern-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <span id="selectedCount" class="bulk-selection-text">0 recordings selected</span>
            <div class="bulk-actions">
                <button type="button" id="bulkDelete" class="btn btn-danger me-2">
                    <i class="fas fa-trash me-2"></i>Delete Selected
                </button>
                <button type="button" id="clearSelection" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Clear Selection
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recordings Table -->
<div class="modern-card">
    <div class="modern-card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="modern-card-title">
                    <i class="fas fa-list me-2"></i>All Recordings
                    @if(request()->hasAny(['search', 'type', 'date_from', 'date_to']))
                        <span class="filter-badge">Filtered</span>
                    @endif
                </h5>
                <p class="modern-card-subtitle">View and manage all session recordings</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="results-info text-dark">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ $recordings->total() }} total recordings
                </div>
            </div>
        </div>
    </div>
    <div class="modern-card-body">
        @if($recordings->count() > 0)
            <div class="modern-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Recording</th>
                            <th>Participants</th>
                            <th>Passcode</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recordings as $recording)
                            <tr class="recording-row">
                                <td>
                                    <input type="checkbox" class="form-check-input recording-checkbox" value="{{ $recording->id }}">
                                </td>
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
                                                @if(isset($recording->additional_files_count) && $recording->additional_files_count > 0)
                                                    <span class="additional-files">+{{ $recording->additional_files_count }} more</span>
                                                @endif
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
                                    <div class="participants-info">
                                        <div class="participant">
                                            <i class="fas fa-chalkboard-teacher me-2"></i>
                                            <span class="participant-name">{{ $recording->booking->teacher->user->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="participant">
                                            <i class="fas fa-user-graduate me-2"></i>
                                            <span class="participant-name">{{ $recording->booking->student->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($recording->passcode)
                                        <span class="passcode-badge" title="Click to copy" onclick="copyToClipboard('{{ $recording->passcode }}')" style="cursor: pointer;">
                                            <i class="fas fa-key me-1"></i>{{ $recording->passcode }}
                                        </span>
                                    @else
                                        <span class="no-passcode">No passcode</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div class="date-main">{{ $recording->recording_start ? $recording->recording_start->format('M d, Y') : 'N/A' }}</div>
                                        @if($recording->recording_start)
                                            <div class="date-time">{{ $recording->recording_start->format('g:i A') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.session-recordings.show', $recording) }}" class="btn btn-sm btn-outline-info action-btn" title="View Details">
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
                                        <a href="{{ $recording->download_url }}" target="_blank" class="btn btn-sm btn-outline-success action-btn" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if(isset($recording->additional_files_count) && $recording->additional_files_count > 0)
                                            <button type="button" class="btn btn-sm btn-outline-secondary action-btn" title="View All Files" onclick="showAllFiles({{ json_encode($recording->all_recordings_data ?? []) }})">
                                                <i class="fas fa-folder-open"></i>
                                            </button>
                                        @endif
                                        <form method="POST" action="{{ route('admin.session-recordings.destroy', $recording) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this recording?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $recordings->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h5 class="empty-state-title">No recordings found</h5>
                <p class="empty-state-description">No session recordings match your search criteria.</p>
            </div>
        @endif
    </div>
</div>

{{-- Include unified session recording modal component --}}
@include('components.session-recording-modal')

<script>
// Copy passcode to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showMessage('Passcode copied to clipboard!', 'success');
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showMessage('Passcode copied to clipboard!', 'success');
    });
}

// Show message function
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

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
                        <button type="button" class="btn-close btn btn-close-white btn-secondary" data-bs-dismiss="modal" aria-label="Close"></button>
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
    
    .results-info {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #ffffff;
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
    
    .filter-badge {
        background: linear-gradient(135deg, #ef473e, #fdb838);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
    
    /* Filter Form */
    .filter-form {
        max-width: 100%;
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
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
        width: 16px;
    }
    
    .modern-input, .modern-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .modern-input:focus, .modern-select:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
    }
    
    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 1rem;
    }
    
    .filter-btn {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
        color: white;
    }
    
    .clear-btn {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .clear-btn:hover {
        background: #5a6268;
        color: white;
    }
    
    /* Bulk Actions */
    .bulk-selection-text {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }
    
    .bulk-actions {
        display: flex;
        gap: 0.5rem;
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
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    
    .modern-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Recording Info */
    .recording-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .recording-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
    }
    
    .recording-icon i {
        font-size: 1.2rem;
    }
    
    .recording-details {
        flex: 1;
    }
    
    .recording-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }
    
    .recording-type {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .additional-files {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
    
    .recording-meta {
        font-size: 0.8rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }
    
    /* Participants Info */
    .participants-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .participant {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }
    
    .participant i {
        width: 16px;
        color: #6c757d;
    }
    
    .participant-name {
        font-weight: 500;
        color: #2c3e50;
    }
    
    /* Passcode Badge */
    .passcode-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .passcode-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    
    .no-passcode {
        color: #6c757d;
        font-style: italic;
        font-size: 0.85rem;
    }
    
    /* Date Info */
    .date-info {
        display: flex;
        flex-direction: column;
    }
    
    .date-main {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }
    
    .date-time {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.125rem;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    
    .action-btn {
        border-radius: 8px;
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
        border: 1px solid;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 3px solid #e9ecef;
    }
    
    .empty-state-icon i {
        font-size: 2rem;
        color: #6c757d;
    }
    
    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .empty-state-description {
        color: #6c757d;
        margin-bottom: 0;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .filter-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .filter-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .recording-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .participants-info {
            gap: 0.25rem;
        }
    }
</style>
@endsection