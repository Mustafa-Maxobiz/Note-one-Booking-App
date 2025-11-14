{{-- Session Recording Modal Component --}}
{{-- Reusable component for admin, teacher, and student views --}}

<!-- Recording Player Modal -->
<div class="modal fade" id="recordingModal" tabindex="-1" aria-labelledby="recordingModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordingModalLabel">
                    <i class="fas fa-video me-2"></i>Session Recording
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Recording Info Section -->
                <div id="recordingInfo" class="mb-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>File:</strong> <span id="recordingFileName"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong> <span id="recordingType"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Passcode Input Section -->
                <div id="passcodeSection" class="mb-3" style="display: none;">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-key me-2"></i>Recording Passcode Required</h6>
                        <p class="mb-2">This recording is password protected. Please enter the passcode to view:</p>
                        <div class="input-group">
                            <input type="password" id="recordingPasscode" class="form-control" placeholder="Enter passcode">
                            <button class="btn btn-primary" onclick="loadRecordingWithPasscode()">
                                <i class="fas fa-unlock me-1"></i>Unlock Recording
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <strong>Common passcodes to try:</strong> 920266, 83312278098, or check your booking details.
                        </small>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="testUrlConstruction()">
                                <i class="fas fa-bug me-1"></i>Debug URL
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recording Player -->
                <div id="recordingPlayer" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading recording...</span>
                    </div>
                    <p class="mt-2">Loading recording player...</p>
                </div>

                <!-- Error Display -->
                <div id="recordingError" class="alert alert-danger" style="display: none;">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Recording Error</h6>
                    <p id="errorMessage"></p>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary me-2" onclick="openInNewTab()">
                            <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                        </button>
                        <button class="btn btn-outline-secondary me-2" onclick="tryDifferentPasscode()">
                            <i class="fas fa-key me-1"></i>Try Different Passcode
                        </button>
                        <button class="btn btn-outline-info" onclick="tryDownload()">
                            <i class="fas fa-download me-1"></i>Try Download Instead
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadBtn" style="display: none;">
                    <i class="fas fa-download me-1"></i>Download Recording
                </button>
            </div>
        </div>
    </div>
</div>

<!-- All Files Modal -->
<div class="modal fade" id="allFilesModal" tabindex="-1" aria-labelledby="allFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allFilesModalLabel">
                    <i class="fas fa-files me-2"></i>All Recording Files
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="allFilesList">
                    <!-- Files will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables to store current recording data
let currentRecordingData = null;

// Debug function to test modal
function testModal() {
    console.log('Testing modal...');
    const modalElement = document.getElementById('recordingModal');
    console.log('Modal element:', modalElement);
    
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('Modal should be visible now');
    } else {
        console.error('Modal element not found!');
    }
}

// Make testModal globally available
window.testModal = testModal;

/**
 * Reset modal state when closed
 */
function resetModalState() {
    // Clear current recording data
    currentRecordingData = null;
    
    // Reset all modal elements to default state
    const passcodeSection = document.getElementById('passcodeSection');
    const recordingError = document.getElementById('recordingError');
    const recordingPlayer = document.getElementById('recordingPlayer');
    const recordingPasscode = document.getElementById('recordingPasscode');
    const recordingInfo = document.getElementById('recordingInfo');
    
    if (passcodeSection) passcodeSection.style.display = 'none';
    if (recordingError) recordingError.style.display = 'none';
    if (recordingPlayer) recordingPlayer.innerHTML = '';
    if (recordingPasscode) recordingPasscode.value = '';
    if (recordingInfo) recordingInfo.style.display = 'none';
}

/**
 * Universal function to open recording modal
 * Works for admin, teacher, and student views
 */
function openRecordingModal(playUrl, recordingType, fileName, meetingId, allRecordings, passcode, recordingId = null) {
    console.log('Opening recording modal:', { playUrl, recordingType, fileName, meetingId, passcode, recordingId });
    
    currentRecordingData = {
        id: recordingId,
        playUrl: playUrl,
        recordingType: recordingType,
        fileName: fileName,
        meetingId: meetingId,
        allRecordings: allRecordings || [],
        passcode: passcode || ''
    };
    
    // Show modal with null check
    const modalElement = document.getElementById('recordingModal');
    console.log('Modal element found:', modalElement);
    
    if (modalElement) {
        try {
            const modal = new bootstrap.Modal(modalElement);
            console.log('Bootstrap modal created:', modal);
            modal.show();
            console.log('Modal show() called');
        } catch (error) {
            console.error('Error creating/showing modal:', error);
        }
    } else {
        console.error('Modal element not found!');
    }
    
    // Reset UI with null checks
    const passcodeSection = document.getElementById('passcodeSection');
    const recordingError = document.getElementById('recordingError');
    const recordingPlayer = document.getElementById('recordingPlayer');
    const recordingInfo = document.getElementById('recordingInfo');
    const recordingFileName = document.getElementById('recordingFileName');
    const recordingTypeElement = document.getElementById('recordingType');
    const downloadBtn = document.getElementById('downloadBtn');
    
    if (passcodeSection) passcodeSection.style.display = 'none';
    if (recordingError) recordingError.style.display = 'none';
    if (recordingInfo) recordingInfo.style.display = 'block';
    if (recordingFileName) recordingFileName.textContent = fileName;
    if (recordingTypeElement) recordingTypeElement.textContent = recordingType.charAt(0).toUpperCase() + recordingType.slice(1);
    if (downloadBtn) {
        downloadBtn.href = playUrl;
        downloadBtn.style.display = 'inline-block';
    }
    
    if (recordingPlayer) {
        recordingPlayer.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading recording...</span>
            </div>
            <p class="mt-2">Loading recording player...</p>
        `;
    }
    
    // Try enhanced loading with automatic passcode detection
    initializeRecordingPlayer();
    
    // Fallback timeout to show passcode field if nothing happens
    setTimeout(() => {
        const passcodeSection = document.getElementById('passcodeSection');
        const recordingError = document.getElementById('recordingError');
        
        if (passcodeSection && passcodeSection.style.display === 'none' && 
            recordingError && recordingError.style.display === 'none') {
            console.log('Fallback: showing passcode field after timeout');
            showPasscodeInput();
        }
    }, 3000);
}


/**
 * Enhanced recording loading with automatic passcode detection
 */
function loadRecordingEnhanced() {
    console.log('loadRecordingEnhanced called with data:', currentRecordingData);
    
    // Check if recording has stored passcode
    if (currentRecordingData.passcode && currentRecordingData.passcode.trim()) {
        console.log('Using stored passcode:', currentRecordingData.passcode);
        loadRecordingWithStoredPasscode(currentRecordingData.passcode);
        return;
    }
    
    // Check if recording requires passcode
    const requiresPasscode = recordingRequiresPasscode(currentRecordingData.playUrl);
    console.log('Recording requires passcode:', requiresPasscode);
    console.log('Play URL:', currentRecordingData.playUrl);
    
    if (requiresPasscode) {
        console.log('Showing passcode input');
        showPasscodeInput();
    } else {
        console.log('Loading recording directly');
        loadRecordingDirectly();
    }
}

/**
 * Check if recording requires passcode based on URL patterns
 */
function recordingRequiresPasscode(playUrl) {
    if (!playUrl) return false;
    
    // Check if URL indicates passcode requirement
    return playUrl.includes('zoom.us/rec/play/') || 
           playUrl.includes('passcode=') ||
           playUrl.includes('password=') ||
           playUrl.includes('access_code=');
}

/**
 * Load recording with stored passcode automatically
 */
function loadRecordingWithStoredPasscode(passcode) {
    const originalUrl = currentRecordingData.playUrl;
    const newUrl = addPasscodeToUrl(originalUrl, passcode);
    
    console.log('Loading with stored passcode:', passcode);
    loadRecordingWithUrl(newUrl);
    
    // Show success message
    showMessage('Recording loaded with stored passcode', 'success');
}

/**
 * Add passcode to URL with proper encoding
 */
function addPasscodeToUrl(originalUrl, passcode) {
    if (!originalUrl || !passcode) return originalUrl;
    
    let newUrl;
    
    if (originalUrl.includes('zoom.us/rec/play/')) {
        // For Zoom Cloud Recordings, handle passcode differently
        if (originalUrl.includes('passcode=')) {
            // Replace existing passcode
            newUrl = originalUrl.replace(/passcode=[^&]*/, `passcode=${encodeURIComponent(passcode)}`);
        } else {
            // Add new passcode parameter
            const separator = originalUrl.includes('?') ? '&' : '?';
            newUrl = `${originalUrl}${separator}passcode=${encodeURIComponent(passcode)}`;
        }
    } else {
        // For other recording types, use standard query parameter
        const separator = originalUrl.includes('?') ? '&' : '?';
        newUrl = `${originalUrl}${separator}passcode=${encodeURIComponent(passcode)}`;
    }
    
    return newUrl;
}

/**
 * Load recording directly without passcode
 */
function loadRecordingDirectly() {
    console.log('loadRecordingDirectly called');
    console.log('Recording type:', currentRecordingData.recordingType);
    console.log('Play URL:', currentRecordingData.playUrl);
    
    const playerDiv = document.getElementById('recordingPlayer');
    playerDiv.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading recording...</span>
        </div>
        <p class="mt-2">Loading recording...</p>
    `;
    
    // Hide error message
    const errorDiv = document.getElementById('recordingError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    // Try to load the recording
    const isVideo = currentRecordingData.recordingType === 'video' || 
                   currentRecordingData.recordingType === 'shared_screen_with_speaker_view';
    
    console.log('Is video recording:', isVideo);
    
    if (isVideo) {
        console.log('Loading video recording');
        loadVideoRecording(currentRecordingData.playUrl);
    } else {
        loadAudioRecording(currentRecordingData.playUrl);
    }
}

/**
 * Load video recording with URL
 */
function loadVideoRecording(url) {
    const playerDiv = document.getElementById('recordingPlayer');
    
    // Check if this is a Zoom recording
    if (url.includes('zoom.us/rec/play/') || url.includes('zoom.us/rec/share/') || url.includes('zoom.us/rec/')) {
        // For Zoom recordings, use enhanced iframe player
        const passcode = currentRecordingData.passcode || null;
        const fileName = currentRecordingData.fileName || 'Zoom Recording';
        const meetingId = currentRecordingData.meetingId || null;
        
        playerDiv.innerHTML = `
                <div class="text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-video fa-4x text-primary mb-3"></i>
                        <h5>Zoom Cloud Recording</h5>
                        <p class="text-muted">Enhanced playback with iframe support and automatic passcode handling.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Enhanced Player:</strong> This recording supports iframe playback with automatic passcode handling.
                        </div>
                    </div>
                
                ${passcode ? `
                <div class="alert alert-success mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-key me-2"></i>
                        <strong>Passcode:</strong>
                        <span class="ms-2 fw-bold text-success fs-5" id="displayPasscode">${passcode}</span>
                        <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="copyPasscode()" title="Copy passcode">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                ` : ''}
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg" onclick="openZoomRecordingModal('${url}', '${currentRecordingData.recordingType || 'video'}', '${fileName}', '${meetingId}', '${passcode || ''}')">
                        <i class="fas fa-play me-2"></i>Open Enhanced Player
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="openZoomRecording('${url}')">
                        <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="tryDirectEmbed()">
                        <i class="fas fa-code me-2"></i>Try Direct Embed
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    // For non-Zoom recordings, try direct embedding
    const video = document.createElement('video');
    video.controls = true;
    video.className = 'w-100';
    video.style.maxHeight = '500px';
    video.src = url;
    
    video.addEventListener('loadeddata', function() {
        console.log('Video loaded successfully');
        playerDiv.innerHTML = '';
        playerDiv.appendChild(video);
        showMessage('Recording loaded successfully', 'success');
    });
    
    video.addEventListener('error', function(e) {
        console.log('Video loading error:', e);
        showError('Failed to load video recording. Please check if the URL is correct.');
    });
    
    playerDiv.innerHTML = '';
    playerDiv.appendChild(video);
}

/**
 * Load audio recording with URL
 */
function loadAudioRecording(url) {
    const playerDiv = document.getElementById('recordingPlayer');
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.className = 'w-100';
    audio.src = url;
    
    audio.addEventListener('loadeddata', function() {
        console.log('Audio loaded successfully');
        playerDiv.innerHTML = '';
        playerDiv.appendChild(audio);
        showMessage('Recording loaded successfully', 'success');
    });
    
    audio.addEventListener('error', function(e) {
        console.log('Audio loading error:', e);
        showError('Failed to load audio recording. Please check if the URL is correct.');
    });
    
    playerDiv.innerHTML = '';
    playerDiv.appendChild(audio);
}

/**
 * Load recording with URL (generic)
 */
function loadRecordingWithUrl(url) {
    const isVideo = currentRecordingData.recordingType === 'video' || 
                   currentRecordingData.recordingType === 'shared_screen_with_speaker_view';
    
    if (isVideo) {
        loadVideoRecording(url);
    } else {
        loadAudioRecording(url);
    }
}

/**
 * Show passcode input UI
 */
function showPasscodeInput() {
    const passcodeSection = document.getElementById('passcodeSection');
    if (passcodeSection) {
        passcodeSection.style.display = 'block';
    }
    
    // Focus on passcode input
    const passcodeInput = document.getElementById('recordingPasscode');
    if (passcodeInput) {
        passcodeInput.focus();
    }
    
    // Show info message
    showMessage('This recording requires a passcode to view', 'info');
}

/**
 * Enhanced loadRecordingWithPasscode function
 */
function loadRecordingWithPasscode() {
    const passcode = document.getElementById('recordingPasscode').value;
    if (!passcode.trim()) {
        showMessage('Please enter a passcode', 'error');
        return;
    }
    
    console.log('Loading recording with manual passcode:', passcode);
    
    // Construct URL with passcode
    const originalUrl = currentRecordingData.playUrl;
    const newUrl = addPasscodeToUrl(originalUrl, passcode);
    
    console.log('Original URL:', originalUrl);
    console.log('New URL with passcode:', newUrl);
    
    // Update current recording data
    currentRecordingData.playUrl = newUrl;
    
    // Show loading state
    const playerDiv = document.getElementById('recordingPlayer');
    playerDiv.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading recording with passcode...</span>
        </div>
        <p class="mt-2">Loading recording with passcode...</p>
    `;
    
    // Hide error message
    const errorDiv = document.getElementById('recordingError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    // Try to load the recording
    loadRecordingWithUrl(newUrl);
}

/**
 * Show message to user
 */
function showMessage(message, type = 'info') {
    // Create or update message element
    let messageDiv = document.getElementById('recordingMessage');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'recordingMessage';
        messageDiv.className = 'alert mt-3';
        document.getElementById('recordingPlayer').parentNode.insertBefore(messageDiv, document.getElementById('recordingPlayer'));
    }
    
    // Set message content and style
    messageDiv.className = `alert alert-${type} mt-3`;
    messageDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
    `;
    
    // Auto-hide after 5 seconds for success messages
    if (type === 'success') {
        setTimeout(() => {
            if (messageDiv) {
                messageDiv.style.display = 'none';
            }
        }, 5000);
    }
}

/**
 * Show error message
 */
function showError(message) {
    const errorDiv = document.getElementById('recordingError');
    if (errorDiv) {
        document.getElementById('errorMessage').textContent = message;
        errorDiv.style.display = 'block';
    }
    
    const playerDiv = document.getElementById('recordingPlayer');
    playerDiv.innerHTML = `
        <div class="text-center text-muted">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>Recording Error</h5>
            <p>${message}</p>
        </div>
    `;
}

/**
 * Initialize enhanced recording loading
 */
function initializeRecordingPlayer() {
    // Check if we have recording data
    if (!currentRecordingData || !currentRecordingData.playUrl) {
        showError('No recording data available');
        return;
    }
    
    // Try enhanced loading
    loadRecordingEnhanced();
}

/**
 * Open Zoom recording in new tab
 */
function openZoomRecording(url) {
    console.log('Opening Zoom recording in new tab:', url);
    window.open(url, '_blank');
    showMessage('Recording opened in new tab', 'success');
}

/**
 * Copy passcode to clipboard
 */
function copyPasscode() {
    const passcodeElement = document.getElementById('displayPasscode');
    if (passcodeElement) {
        const passcode = passcodeElement.textContent;
        navigator.clipboard.writeText(passcode).then(() => {
            showMessage('Passcode copied to clipboard!', 'success');
        }).catch(err => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = passcode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showMessage('Passcode copied to clipboard!', 'success');
        });
    }
}

/**
 * Try direct embedding for Zoom recordings (fallback)
 */
function tryDirectEmbed() {
    const playerDiv = document.getElementById('recordingPlayer');
    const video = document.createElement('video');
    video.controls = true;
    video.className = 'w-100';
    video.style.maxHeight = '500px';
    video.src = currentRecordingData.playUrl;
    
    video.addEventListener('loadeddata', function() {
        console.log('Video loaded successfully');
        playerDiv.innerHTML = '';
        playerDiv.appendChild(video);
        showMessage('Recording loaded successfully', 'success');
    });
    
    video.addEventListener('error', function(e) {
        console.log('Video loading error:', e);
        showError('Failed to load video recording. Please use "Open in New Tab" option.');
    });
    
    playerDiv.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading recording...</span>
        </div>
        <p class="mt-2">Attempting direct playback...</p>
    `;
    
    setTimeout(() => {
        playerDiv.innerHTML = '';
        playerDiv.appendChild(video);
    }, 1000);
}

function openInNewTab() {
    if (currentRecordingData && currentRecordingData.playUrl) {
        window.open(currentRecordingData.playUrl, '_blank');
    }
}

function tryDifferentPasscode() {
    document.getElementById('recordingPasscode').value = '';
    document.getElementById('recordingPasscode').focus();
    document.getElementById('recordingError').style.display = 'none';
}

function tryDownload() {
    if (currentRecordingData && currentRecordingData.playUrl) {
        // Try to construct download URL (this might not work for all cases)
        const downloadUrl = currentRecordingData.playUrl.replace('/play/', '/download/');
        window.open(downloadUrl, '_blank');
    }
}

function testUrlConstruction() {
    const passcode = document.getElementById('recordingPasscode').value || 'c3^?FcG4';
    const originalUrl = currentRecordingData.playUrl;
    
    console.log('=== URL Construction Debug ===');
    console.log('Original URL:', originalUrl);
    console.log('Test passcode:', passcode);
    console.log('Encoded passcode:', encodeURIComponent(passcode));
    
    let newUrl;
    if (originalUrl.includes('zoom.us/rec/play/')) {
        if (originalUrl.includes('passcode=')) {
            newUrl = originalUrl.replace(/passcode=[^&]*/, `passcode=${encodeURIComponent(passcode)}`);
        } else {
            const separator = originalUrl.includes('?') ? '&' : '?';
            newUrl = `${originalUrl}${separator}passcode=${encodeURIComponent(passcode)}`;
        }
    } else {
        const separator = originalUrl.includes('?') ? '&' : '?';
        newUrl = `${originalUrl}${separator}passcode=${encodeURIComponent(passcode)}`;
    }
    
    console.log('Constructed URL:', newUrl);
    console.log('URL difference:', newUrl !== originalUrl ? 'Changed' : 'No change');
    
    // Show in alert for easy copying
    alert(`Debug Info:\n\nOriginal: ${originalUrl}\n\nWith Passcode: ${newUrl}\n\nEncoded Passcode: ${encodeURIComponent(passcode)}`);
}

function showAllFiles(allRecordings) {
    console.log('Showing all files:', allRecordings);
    
    const filesList = document.getElementById('allFilesList');
    let filesHtml = '';
    
    if (allRecordings && allRecordings.length > 0) {
        allRecordings.forEach((recording, index) => {
            const typeIcon = recording.recording_type === 'video' ? 'fas fa-video' : 
                           recording.recording_type === 'audio' ? 'fas fa-microphone' : 
                           'fas fa-file';
            
            const typeBadge = recording.recording_type === 'video' ? 'bg-primary' : 
                             recording.recording_type === 'audio' ? 'bg-success' : 
                             'bg-secondary';
            
            filesHtml += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="card-title">
                                    <i class="${typeIcon} me-2"></i>
                                    <span class="badge ${typeBadge}">${recording.recording_type}</span>
                                    ${recording.file_name || 'Recording File'}
                                </h6>
                                <p class="card-text text-muted mb-2">
                                    <small>
                                        <i class="fas fa-clock me-1"></i>Duration: ${recording.duration || 'N/A'}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-hdd me-1"></i>Size: ${recording.file_size ? (recording.file_size / 1024 / 1024).toFixed(2) + ' MB' : 'N/A'}
                                    </small>
                                </p>
                            </div>
                            <div class="btn-group btn-group-sm">
                                ${recording.play_url ? `
                                    <button class="btn btn-outline-primary" onclick="openRecordingModal('${recording.play_url}', '${recording.recording_type}', '${recording.file_name}', '${recording.recording_id || ''}', ${JSON.stringify(allRecordings)})">
                                        <i class="fas fa-play"></i> Play
                                    </button>
                                ` : ''}
                                ${recording.download_url ? `
                                    <a href="${recording.download_url}" target="_blank" class="btn btn-outline-info">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        filesHtml = '<p class="text-muted">No additional files found.</p>';
    }
    
    filesList.innerHTML = filesHtml;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('allFilesModal'));
    modal.show();
}

// Reset modal when closed
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('recordingModal');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            resetModalState();
        });
    }
});
</script>

<!-- Include Zoom Iframe Player Component -->
@include('components.zoom-iframe-player')
