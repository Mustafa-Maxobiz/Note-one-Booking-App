@php
    $totalRecordings = \App\Models\SessionRecording::count();
    $recentRecordings = \App\Models\SessionRecording::with('booking')
        ->latest()
        ->take(5)
        ->get();
    
    $recordingTypes = \App\Models\SessionRecording::selectRaw('recording_type, COUNT(*) as count')
        ->groupBy('recording_type')
        ->pluck('count', 'recording_type')
        ->toArray();
    
    $totalSize = \App\Models\SessionRecording::sum('file_size');
    $averageDuration = \App\Models\SessionRecording::avg('duration') ?? 0;
@endphp

<div class="widget-analytics">
    <div class="widget-header">
        <div class="widget-title">
            <i class="voyager-video"></i>
            <span>Session Recordings</span>
        </div>
        <div class="widget-subtitle">Zoom Meeting Recordings Overview</div>
        <a href="{{ route('admin.session-recordings.index') }}" class="widget-action-btn">
            <i class="voyager-eye"></i>
            View All Recordings
        </a>
    </div>

    <div class="widget-metrics">
        <div class="metric-card primary">
            <div class="metric-icon">
                <i class="voyager-video"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($totalRecordings) }}</div>
                <div class="metric-label">Total Recordings</div>
            </div>
        </div>

        <div class="metric-card success">
            <div class="metric-icon">
                <i class="voyager-data"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($totalSize / (1024 * 1024), 1) }} MB</div>
                <div class="metric-label">Total Storage</div>
            </div>
        </div>

        <div class="metric-card info">
            <div class="metric-icon">
                <i class="voyager-clock"></i>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ gmdate('H:i:s', $averageDuration) }}</div>
                <div class="metric-label">Avg Duration</div>
            </div>
        </div>
    </div>

    <div class="widget-charts">
        <div class="chart-section">
            <h4>Recording Types</h4>
            <div class="recording-types">
                @foreach($recordingTypes as $type => $count)
                    <div class="type-item">
                        <div class="type-icon">
                            @if($type === 'video')
                                <i class="voyager-video"></i>
                            @elseif($type === 'audio')
                                <i class="voyager-audio"></i>
                            @else
                                <i class="voyager-chat"></i>
                            @endif
                        </div>
                        <div class="type-info">
                            <span class="type-name">{{ ucfirst($type) }}</span>
                            <span class="type-count">{{ $count }}</span>
                        </div>
                        <div class="type-progress">
                            <div class="progress-bar" style="width: {{ $totalRecordings > 0 ? ($count / $totalRecordings) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="chart-section">
            <h4>Recent Recordings</h4>
            <div class="recent-recordings">
                @forelse($recentRecordings as $recording)
                    <div class="recording-item">
                        <div class="recording-icon">
                            @if($recording->recording_type === 'video')
                                <i class="voyager-video"></i>
                            @elseif($recording->recording_type === 'audio')
                                <i class="voyager-audio"></i>
                            @else
                                <i class="voyager-chat"></i>
                            @endif
                        </div>
                        <div class="recording-info">
                            <div class="recording-name">{{ Str::limit($recording->file_name, 30) }}</div>
                            <div class="recording-meta">
                                <span class="recording-type">{{ ucfirst($recording->recording_type) }}</span>
                                <span class="recording-size">{{ $recording->formatted_file_size }}</span>
                                <span class="recording-date">{{ $recording->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="recording-actions">
                            @if($recording->download_url)
                                <a href="{{ $recording->download_url }}" target="_blank" class="action-btn download" title="Download">
                                    <i class="voyager-download"></i>
                                </a>
                            @endif
                            @if($recording->play_url)
                                <a href="{{ $recording->play_url }}" target="_blank" class="action-btn play" title="Play">
                                    <i class="voyager-play"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="no-recordings">
                        <i class="voyager-video"></i>
                        <span>No recordings available</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="widget-footer">
        <div class="footer-actions">
            <a href="{{ route('admin.session-recordings.index') }}" class="footer-btn">
                <i class="voyager-list"></i>
                Manage Recordings
            </a>
            <button onclick="fetchRecordings()" class="footer-btn refresh">
                <i class="voyager-refresh"></i>
                Fetch New
            </button>
        </div>
    </div>
</div>

<style>
.widget-analytics {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.widget-analytics::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    z-index: 1;
}

.widget-analytics > * {
    position: relative;
    z-index: 2;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.widget-title {
    display: flex;
    align-items: center;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.widget-title i {
    margin-right: 12px;
    font-size: 28px;
    background: linear-gradient(45deg, #ff6b6b, #ffd93d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.widget-subtitle {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    margin-bottom: 15px;
}

.widget-action-btn {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
}

.widget-action-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.widget-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: rgba(255,255,255,0.15);
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,0.25);
}

.metric-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 24px;
}

.metric-card.primary .metric-icon {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
}

.metric-card.success .metric-icon {
    background: linear-gradient(45deg, #4ecdc4, #6dd5ed);
}

.metric-card.info .metric-icon {
    background: linear-gradient(45deg, #45b7d1, #96c93d);
}

.metric-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.metric-label {
    font-size: 12px;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.widget-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
}

.chart-section h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: rgba(255,255,255,0.9);
}

.recording-types {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.type-item {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 12px;
    backdrop-filter: blur(5px);
}

.type-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
}

.type-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.type-name {
    font-weight: 600;
    font-size: 14px;
}

.type-count {
    font-weight: 700;
    font-size: 16px;
    color: #ffd93d;
}

.type-progress {
    width: 60px;
    height: 4px;
    background: rgba(255,255,255,0.2);
    border-radius: 2px;
    overflow: hidden;
    margin-left: 10px;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(45deg, #ffd93d, #ff6b6b);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.recent-recordings {
    max-height: 200px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.recording-item {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 12px;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.recording-item:hover {
    background: rgba(255,255,255,0.2);
    transform: translateX(5px);
}

.recording-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
}

.recording-info {
    flex: 1;
}

.recording-name {
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 4px;
}

.recording-meta {
    display: flex;
    gap: 8px;
    font-size: 11px;
    color: rgba(255,255,255,0.7);
}

.recording-actions {
    display: flex;
    gap: 5px;
}

.action-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: rgba(255,255,255,0.2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

.no-recordings {
    text-align: center;
    padding: 30px;
    color: rgba(255,255,255,0.6);
}

.no-recordings i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.widget-footer {
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 20px;
}

.footer-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.footer-btn {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.footer-btn.refresh {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
}

.footer-btn.refresh:hover {
    background: linear-gradient(45deg, #ff5252, #ff7979);
}

@media (max-width: 768px) {
    .widget-charts {
        grid-template-columns: 1fr;
    }
    
    .widget-metrics {
        grid-template-columns: 1fr;
    }
    
    .footer-actions {
        flex-direction: column;
    }
}
</style>

<script>
function fetchRecordings() {
    if (confirm('This will fetch new recordings from Zoom. Continue?')) {
        // You can implement AJAX call here to trigger the fetch command
        fetch('/admin/zoom/fetch-recordings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recordings fetched successfully!');
                location.reload();
            } else {
                alert('Error fetching recordings: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching recordings');
        });
    }
}
</script>
