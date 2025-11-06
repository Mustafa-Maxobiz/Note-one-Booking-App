<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoom Recording Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .player-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .player-header {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        
        .player-iframe {
            flex: 1;
            width: 100%;
            border: none;
            background: #000;
        }
        
        .player-controls {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            z-index: 1001;
            display: none;
        }
        
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1002;
        }
        
        .error-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            z-index: 1003;
            display: none;
        }
        
        .fullscreen-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1004;
        }
    </style>
</head>
<body>
    <div class="player-container">
        <!-- Player Header -->
        <div class="player-header">
            <div>
                <i class="fas fa-video me-2"></i>
                <strong>Zoom Recording Player</strong>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-light me-2" onclick="toggleControls()">
                    <i class="fas fa-cog"></i>
                </button>
                <button class="btn btn-sm btn-outline-light" onclick="openInNewTab()">
                    <i class="fas fa-external-link-alt"></i>
                </button>
            </div>
        </div>
        
        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-light">Loading recording...</p>
        </div>
        
        <!-- Error Message -->
        <div class="error-message" id="errorMessage">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Recording Error</h5>
            <p id="errorText">Failed to load recording</p>
            <button class="btn btn-light mt-2" onclick="retryLoad()">
                <i class="fas fa-redo me-1"></i>Retry
            </button>
        </div>
        
        <!-- Player Controls -->
        <div class="player-controls" id="playerControls">
            <h6>Player Controls</h6>
            <div class="btn-group-vertical">
                <button class="btn btn-light mb-2" onclick="toggleFullscreen()">
                    <i class="fas fa-expand me-1"></i>Toggle Fullscreen
                </button>
                <button class="btn btn-light mb-2" onclick="openInNewTab()">
                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                </button>
                <button class="btn btn-light" onclick="hideControls()">
                    <i class="fas fa-times me-1"></i>Hide Controls
                </button>
            </div>
        </div>
        
        <!-- Main Iframe -->
        <iframe id="recordingIframe" 
                class="player-iframe" 
                src="{{ $iframeUrl }}"
                frameborder="0" 
                allowfullscreen
                allow="autoplay; fullscreen; encrypted-media">
        </iframe>
        
        <!-- Fullscreen Button -->
        <button class="btn btn-primary fullscreen-btn" onclick="toggleFullscreen()">
            <i class="fas fa-expand"></i>
        </button>
    </div>

    <script>
        const iframe = document.getElementById('recordingIframe');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const playerControls = document.getElementById('playerControls');
        
        // Recording metadata
        const metadata = @json($metadata);
        
        // Initialize player
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Zoom iframe player initialized');
            console.log('Metadata:', metadata);
            
            // Monitor iframe loading
            iframe.onload = function() {
                console.log('Iframe loaded successfully');
                hideLoading();
            };
            
            iframe.onerror = function() {
                console.error('Iframe failed to load');
                showError('Failed to load recording. The recording may require authentication or may not support iframe embedding.');
            };
            
            // Set timeout for loading
            setTimeout(() => {
                if (iframe.src && !iframe.contentDocument) {
                    console.log('Iframe loading timeout');
                    showError('Recording may require authentication. Try opening in a new tab.');
                }
            }, 15000);
        });
        
        function hideLoading() {
            loadingSpinner.style.display = 'none';
        }
        
        function showError(message) {
            hideLoading();
            errorMessage.style.display = 'block';
            document.getElementById('errorText').textContent = message;
        }
        
        function hideError() {
            errorMessage.style.display = 'none';
        }
        
        function retryLoad() {
            hideError();
            loadingSpinner.style.display = 'block';
            iframe.src = iframe.src; // Reload iframe
        }
        
        function toggleControls() {
            if (playerControls.style.display === 'none' || playerControls.style.display === '') {
                playerControls.style.display = 'block';
            } else {
                playerControls.style.display = 'none';
            }
        }
        
        function hideControls() {
            playerControls.style.display = 'none';
        }
        
        function toggleFullscreen() {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                document.documentElement.requestFullscreen();
            }
        }
        
        function openInNewTab() {
            window.open('{{ $originalUrl }}', '_blank', 'noopener,noreferrer');
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'Escape':
                    hideControls();
                    break;
                case 'F11':
                    e.preventDefault();
                    toggleFullscreen();
                    break;
                case 'c':
                case 'C':
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        toggleControls();
                    }
                    break;
            }
        });
        
        // Auto-hide controls after 3 seconds
        let controlsTimeout;
        document.addEventListener('mousemove', function() {
            clearTimeout(controlsTimeout);
            if (playerControls.style.display === 'block') {
                controlsTimeout = setTimeout(hideControls, 3000);
            }
        });
    </script>
</body>
</html>
