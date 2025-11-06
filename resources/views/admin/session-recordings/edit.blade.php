@extends('layouts.app')

@section('title', 'Edit Session Recording')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-edit me-3"></i>Edit Session Recording
            </h1>
            <p class="page-subtitle">Update recording details and information</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.session-recordings.show', $session_recording) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Recording
                </a>
                <a href="{{ route('admin.session-recordings.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-list me-2"></i>All Recordings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modern-card-title">Edit Recording Information</h5>
                        <p class="modern-card-subtitle">Update recording details and metadata</p>
                    </div>
                </div>
            </div>
            <div class="modern-card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error!</strong> Please fix the following issues:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.session-recordings.update', $session_recording) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="file_name" class="form-label">File Name</label>
                            <input type="text" class="form-control @error('file_name') is-invalid @enderror" 
                                   id="file_name" name="file_name" 
                                   value="{{ old('file_name', $session_recording->file_name) }}" required>
                            @error('file_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="recording_type" class="form-label">Recording Type</label>
                            <select class="form-select @error('recording_type') is-invalid @enderror" 
                                    id="recording_type" name="recording_type" required>
                                <option value="">Select Type</option>
                                <option value="video" {{ old('recording_type', $session_recording->recording_type) === 'video' ? 'selected' : '' }}>Video</option>
                                <option value="audio" {{ old('recording_type', $session_recording->recording_type) === 'audio' ? 'selected' : '' }}>Audio</option>
                                <option value="chat" {{ old('recording_type', $session_recording->recording_type) === 'chat' ? 'selected' : '' }}>Chat</option>
                                <option value="transcript" {{ old('recording_type', $session_recording->recording_type) === 'transcript' ? 'selected' : '' }}>Transcript</option>
                            </select>
                            @error('recording_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duration_seconds" class="form-label">Duration (seconds)</label>
                            <input type="number" class="form-control @error('duration_seconds') is-invalid @enderror" 
                                   id="duration_seconds" name="duration_seconds" 
                                   value="{{ old('duration_seconds', $session_recording->duration_seconds) }}" min="0" required>
                            @error('duration_seconds')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="file_size_bytes" class="form-label">File Size (bytes)</label>
                            <input type="number" class="form-control @error('file_size_bytes') is-invalid @enderror" 
                                   id="file_size_bytes" name="file_size_bytes" 
                                   value="{{ old('file_size_bytes', $session_recording->file_size_bytes) }}" min="0" required>
                            @error('file_size_bytes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="play_url" class="form-label">Play URL</label>
                            <input type="url" class="form-control @error('play_url') is-invalid @enderror" 
                                   id="play_url" name="play_url" 
                                   value="{{ old('play_url', $session_recording->play_url) }}" required>
                            @error('play_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="download_url" class="form-label">Download URL</label>
                            <input type="url" class="form-control @error('download_url') is-invalid @enderror" 
                                   id="download_url" name="download_url" 
                                   value="{{ old('download_url', $session_recording->download_url) }}" required>
                            @error('download_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zoom_meeting_id" class="form-label">Zoom Meeting ID</label>
                            <input type="text" class="form-control @error('zoom_meeting_id') is-invalid @enderror" 
                                   id="zoom_meeting_id" name="zoom_meeting_id" 
                                   value="{{ old('zoom_meeting_id', $session_recording->zoom_meeting_id) }}">
                            @error('zoom_meeting_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="zoom_recording_id" class="form-label">Zoom Recording ID</label>
                            <input type="text" class="form-control @error('zoom_recording_id') is-invalid @enderror" 
                                   id="zoom_recording_id" name="zoom_recording_id" 
                                   value="{{ old('zoom_recording_id', $session_recording->zoom_recording_id) }}">
                            @error('zoom_recording_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.session-recordings.show', $session_recording) }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Recording
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Recording Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Current Recording Info
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>{{ $session_recording->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Session:</strong></td>
                        <td>#{{ $session_recording->session_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
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
                        <td><strong>Size:</strong></td>
                        <td>{{ $session_recording->formatted_file_size }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $session_recording->created_at->format('M d, Y g:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated:</strong></td>
                        <td>{{ $session_recording->updated_at->format('M d, Y g:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Session Information -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar me-2"></i>Session Information
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Teacher:</strong>
                    <div class="d-flex align-items-center mt-1">
                        <img src="{{ $session_recording->booking->teacher->user->small_profile_picture_url }}"
                             alt="{{ $session_recording->booking->teacher->user->name }}"
                             class="rounded-circle me-2"
                             style="width: 32px; height: 32px; object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ $session_recording->booking->teacher->user->name }}</div>
                            <small class="text-muted">{{ $session_recording->booking->teacher->qualifications }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Student:</strong>
                    <div class="d-flex align-items-center mt-1">
                        <img src="{{ $session_recording->booking->student->user->small_profile_picture_url }}"
                             alt="{{ $session_recording->booking->student->user->name }}"
                             class="rounded-circle me-2"
                             style="width: 32px; height: 32px; object-fit: cover;">
                        <div>
                            <div class="fw-bold">{{ $session_recording->booking->student->user->name }}</div>
                            <small class="text-muted">{{ $session_recording->booking->student->level }} level</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Session Date:</strong><br>
                    <span class="text-muted">{{ $session_recording->booking->start_time->format('M d, Y g:i A') }}</span>
                </div>

                <div class="mb-3">
                    <strong>Status:</strong><br>
                    @if($session_recording->booking->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @elseif($session_recording->booking->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-warning">{{ ucfirst($session_recording->booking->status) }}</span>
                    @endif
                </div>

                <a href="{{ route('admin.bookings.show', $session_recording->session) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>View Session Details
                </a>
            </div>
        </div>
    </div>
</div>
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
    display: flex;
    align-items: center;
}

.page-subtitle {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

.header-actions .btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.header-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
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
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.modern-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.modern-card-subtitle {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0.25rem 0 0 0;
}

.modern-card-body {
    padding: 2rem;
}

/* Form Elements */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    display: block;
    font-size: 0.95rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #ef473e;
    box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
    outline: none;
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
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
}

/* Table Styles */
.table td {
    padding: 0.5rem 0;
    border: none;
    vertical-align: middle;
}

.table td:first-child {
    font-weight: 600;
    color: #495057;
    width: 40%;
}

.badge {
    font-size: 0.75em;
    border-radius: 8px;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .header-actions {
        margin-top: 1rem;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
}
</style>
@endsection

