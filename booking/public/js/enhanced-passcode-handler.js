// Enhanced Passcode Handler for Zoom Recordings
// Shared JavaScript for Admin, Teacher, and Student views

/**
 * Enhanced recording loading with automatic passcode detection
 */
function loadRecordingEnhanced() {
    // Check if recording has stored passcode
    if (currentRecordingData.passcode && currentRecordingData.passcode.trim()) {
        console.log('Using stored passcode:', currentRecordingData.passcode);
        loadRecordingWithStoredPasscode(currentRecordingData.passcode);
        return;
    }
    
    // Check if recording requires passcode
    if (recordingRequiresPasscode(currentRecordingData.playUrl)) {
        showPasscodeInput();
    } else {
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
    
    if (isVideo) {
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

// Make functions globally available
window.loadRecordingEnhanced = loadRecordingEnhanced;
window.loadRecordingWithStoredPasscode = loadRecordingWithStoredPasscode;
window.recordingRequiresPasscode = recordingRequiresPasscode;
window.addPasscodeToUrl = addPasscodeToUrl;
window.showMessage = showMessage;
window.initializeRecordingPlayer = initializeRecordingPlayer;
window.loadRecordingWithUrl = loadRecordingWithUrl;
