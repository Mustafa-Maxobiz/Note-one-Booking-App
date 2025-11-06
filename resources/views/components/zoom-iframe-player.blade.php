{{-- Enhanced Zoom Recording Player with Iframe Support --}}
{{-- Handles Zoom recordings without passcode or redirect --}}

<!-- Zoom Recording Player Modal -->
<div class="modal fade" id="zoomRecordingModal" tabindex="-1" aria-labelledby="zoomRecordingModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoomRecordingModalLabel">
                    <i class="fas fa-video me-2"></i>Zoom Recording Player
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Recording Info -->
                <div id="zoomRecordingInfo" class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>File:</strong> <span id="zoomFileName"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong> <span id="zoomRecordingType"></span>
                        </div>
                    </div>
                </div>

                <!-- Passcode Display -->
                <div id="zoomPasscodeSection" class="mb-3" style="display: none;">
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-key me-2"></i>
                            <strong>Recording Passcode:</strong>
                            <span class="ms-2 fw-bold text-success fs-5" id="zoomDisplayPasscode"></span>
                            <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="copyZoomPasscode()" title="Copy passcode">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block text-center">
                            <i class="fas fa-info-circle me-1"></i>
                            This passcode is automatically applied when playing the recording.
                        </small>
                    </div>
                </div>

                <!-- Player Controls -->
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" id="playInIframeBtn" onclick="playInIframe()">
                            <i class="fas fa-play me-1"></i>Play in Iframe
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="playInNewTabBtn" onclick="playInNewTab()">
                            <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="downloadBtn" onclick="downloadRecording()">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>

                <!-- Iframe Player -->
                <div id="iframePlayer" class="text-center" style="display: none;">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe id="recordingIframe" 
                                class="embed-responsive-item w-100" 
                                style="height: 500px; border: 1px solid #dee2e6; border-radius: 0.375rem;"
                                frameborder="0" 
                                allowfullscreen>
                        </iframe>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            If the recording doesn't load, try opening in a new tab or check your internet connection.
                        </small>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="zoomLoadingState" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading recording...</span>
                    </div>
                    <p class="mt-2">Preparing recording player...</p>
                </div>

                <!-- Error State -->
                <div id="zoomErrorState" class="alert alert-danger" style="display: none;">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Recording Error</h6>
                    <p id="zoomErrorMessage"></p>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary me-2" onclick="playInNewTab()">
                            <i class="fas fa-external-link-alt me-1"></i>Try New Tab
                        </button>
                        <button class="btn btn-outline-secondary" onclick="retryIframe()">
                            <i class="fas fa-redo me-1"></i>Retry Iframe
                        </button>
                    </div>
                </div>

                <!-- Success State -->
                <div id="zoomSuccessState" class="alert alert-success" style="display: none;">
                    <h6><i class="fas fa-check-circle me-2"></i>Recording Loaded Successfully</h6>
                    <p>The recording is now playing in the iframe below.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="fullscreenBtn" onclick="toggleFullscreen()" style="display: none;">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for Zoom recording player
let currentZoomRecording = null;
let iframePlayer = null;

/**
 * Open Zoom recording modal with enhanced iframe support
 */
function openZoomRecordingModal(playUrl, recordingType, fileName, meetingId, passcode = null) {
    console.log('Opening Zoom recording modal:', { playUrl, recordingType, fileName, meetingId, passcode });
    
    currentZoomRecording = {
        playUrl: playUrl,
        recordingType: recordingType,
        fileName: fileName,
        meetingId: meetingId,
        passcode: passcode
    };
    
    // Update modal content
    document.getElementById('zoomFileName').textContent = fileName || 'Unknown File';
    document.getElementById('zoomRecordingType').textContent = recordingType || 'Unknown Type';
    
    // Show passcode if available
    const passcodeSection = document.getElementById('zoomPasscodeSection');
    const passcodeDisplay = document.getElementById('zoomDisplayPasscode');
    
    if (passcode && passcode.trim()) {
        passcodeDisplay.textContent = passcode;
        passcodeSection.style.display = 'block';
    } else {
        passcodeSection.style.display = 'none';
    }
    
    // Show loading state
    showZoomLoadingState();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('zoomRecordingModal'));
    modal.show();
    
    // Auto-attempt iframe loading
    setTimeout(() => {
        playInIframe();
    }, 1000);
}

/**
 * Play recording in iframe
 */
function playInIframe() {
    if (!currentZoomRecording) return;
    
    console.log('Attempting to play in iframe:', currentZoomRecording.playUrl);
    
    showZoomLoadingState();
    
    // Construct iframe URL with enhanced parameters
    const iframeUrl = constructIframeUrl(currentZoomRecording.playUrl, currentZoomRecording.passcode);
    
    console.log('Constructed iframe URL:', iframeUrl);
    
    // Set iframe source
    const iframe = document.getElementById('recordingIframe');
    iframe.src = iframeUrl;
    
    // Show iframe player
    document.getElementById('iframePlayer').style.display = 'block';
    document.getElementById('zoomLoadingState').style.display = 'none';
    document.getElementById('zoomErrorState').style.display = 'none';
    document.getElementById('fullscreenBtn').style.display = 'inline-block';
    
    // Monitor iframe loading
    iframe.onload = function() {
        console.log('Iframe loaded successfully');
        showZoomSuccessState();
    };
    
    iframe.onerror = function() {
        console.error('Iframe failed to load');
        showZoomErrorState('Failed to load recording in iframe. The recording may require authentication or may not support iframe embedding.');
    };
    
    // Set timeout for iframe loading
    setTimeout(() => {
        if (iframe.src && !iframe.contentDocument) {
            console.log('Iframe loading timeout - may need authentication');
            showZoomErrorState('Recording may require authentication. Try opening in a new tab.');
        }
    }, 10000);
}

/**
 * Construct iframe URL with enhanced parameters
 */
function constructIframeUrl(originalUrl, passcode) {
    if (!originalUrl) return '';
    
    let url = originalUrl;
    
    // Add passcode if available
    if (passcode) {
        const separator = url.includes('?') ? '&' : '?';
        url += `${separator}passcode=${encodeURIComponent(passcode)}`;
    }
    
    // Add iframe-friendly parameters
    const iframeParams = [
        'autoplay=1',
        'controls=1',
        'modestbranding=1',
        'rel=0',
        'showinfo=0',
        'iv_load_policy=3',
        'fs=1',
        'cc_load_policy=1',
        'enablejsapi=1'
    ];
    
    const separator = url.includes('?') ? '&' : '?';
    url += separator + iframeParams.join('&');
    
    return url;
}

/**
 * Play recording in new tab
 */
function playInNewTab() {
    if (!currentZoomRecording) return;
    
    const newTabUrl = constructIframeUrl(currentZoomRecording.playUrl, currentZoomRecording.passcode);
    window.open(newTabUrl, '_blank', 'noopener,noreferrer');
}

/**
 * Download recording
 */
function downloadRecording() {
    if (!currentZoomRecording) return;
    
    // Create download link
    const link = document.createElement('a');
    link.href = currentZoomRecording.playUrl;
    link.download = currentZoomRecording.fileName || 'recording';
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Retry iframe loading
 */
function retryIframe() {
    console.log('Retrying iframe loading...');
    playInIframe();
}

/**
 * Toggle fullscreen mode
 */
function toggleFullscreen() {
    const iframe = document.getElementById('recordingIframe');
    if (iframe.requestFullscreen) {
        iframe.requestFullscreen();
    } else if (iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen();
    } else if (iframe.msRequestFullscreen) {
        iframe.msRequestFullscreen();
    }
}

/**
 * Show loading state
 */
function showZoomLoadingState() {
    document.getElementById('zoomLoadingState').style.display = 'block';
    document.getElementById('iframePlayer').style.display = 'none';
    document.getElementById('zoomErrorState').style.display = 'none';
    document.getElementById('zoomSuccessState').style.display = 'none';
}

/**
 * Show error state
 */
function showZoomErrorState(message) {
    document.getElementById('zoomLoadingState').style.display = 'none';
    document.getElementById('iframePlayer').style.display = 'none';
    document.getElementById('zoomErrorState').style.display = 'block';
    document.getElementById('zoomSuccessState').style.display = 'none';
    document.getElementById('zoomErrorMessage').textContent = message;
}

/**
 * Show success state
 */
function showZoomSuccessState() {
    document.getElementById('zoomLoadingState').style.display = 'none';
    document.getElementById('iframePlayer').style.display = 'block';
    document.getElementById('zoomErrorState').style.display = 'none';
    document.getElementById('zoomSuccessState').style.display = 'block';
}

/**
 * Copy passcode to clipboard
 */
function copyZoomPasscode() {
    const passcodeElement = document.getElementById('zoomDisplayPasscode');
    if (passcodeElement) {
        const passcode = passcodeElement.textContent;
        navigator.clipboard.writeText(passcode).then(() => {
            showZoomMessage('Passcode copied to clipboard!', 'success');
        }).catch(err => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = passcode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showZoomMessage('Passcode copied to clipboard!', 'success');
        });
    }
}

/**
 * Show message for Zoom player
 */
function showZoomMessage(message, type = 'info') {
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
 * Enhanced Zoom recording detection
 */
function isZoomRecording(url) {
    if (!url) return false;
    
    const zoomPatterns = [
        'zoom.us/rec/play/',
        'zoom.us/rec/share/',
        'zoom.us/rec/',
        'zoom.us/j/',
        'zoom.us/meeting/'
    ];
    
    return zoomPatterns.some(pattern => url.includes(pattern));
}

/**
 * Get Zoom recording type from URL
 */
function getZoomRecordingType(url) {
    if (!url) return 'unknown';
    
    if (url.includes('/rec/play/')) return 'cloud_recording';
    if (url.includes('/rec/share/')) return 'shared_recording';
    if (url.includes('/j/')) return 'meeting_join';
    if (url.includes('/meeting/')) return 'meeting_link';
    
    return 'zoom_recording';
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Zoom iframe player initialized');
});
</script>
