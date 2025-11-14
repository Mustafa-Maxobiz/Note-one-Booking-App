@extends('layouts.app')

@section('title', 'Lesson Note Details')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-book me-3"></i>{{ $lessonNote->title }}
            </h1>
            <p class="dashboard-subtitle">Lesson details and content</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="action-button">
                <a href="{{ route('lesson-notes.index', ['student_id' => $lessonNote->student_id]) }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Lesson Log
                </a>
                @can('update', $lessonNote)
                <a href="{{ route('lesson-notes.edit', $lessonNote) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-edit me-2"></i>Edit Note
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-graduation-cap me-2"></i>Lesson Content
                                    </h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-{{ $lessonNote->visibility === 'student_and_teacher' ? 'success' : 'warning' }}">
                                        {{ $lessonNote->visibility === 'student_and_teacher' ? 'Visible to Student' : 'Teacher Only' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($lessonNote->content)
                                <div class="lesson-content">
                                    {!! nl2br(e($lessonNote->content)) !!}
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No additional content provided for this lesson.
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($lessonNote->attachments && is_array($lessonNote->attachments) && count($lessonNote->attachments) > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-paperclip me-2"></i>Attachments
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="attachments-grid">
                                @foreach($lessonNote->attachments as $attachment)
                                @if(is_array($attachment) && isset($attachment['url']))
                                <div class="attachment-item">
                                    <div class="attachment-icon">
                                        <i class="fas fa-file-{{ $attachment['type'] ?? 'alt' }}"></i>
                                    </div>
                                    <div class="attachment-info">
                                        <h6 class="attachment-name">{{ $attachment['name'] ?? 'Attachment' }}</h6>
                                        <p class="attachment-size">{{ $attachment['size'] ?? 'Unknown size' }}</p>
                                    </div>
                                    <div class="attachment-actions">
                                        <a href="{{ $attachment['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                                @elseif(is_string($attachment))
                                <div class="attachment-item">
                                    <div class="attachment-icon">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </div>
                                    <div class="attachment-info">
                                        <h6 class="attachment-name">{{ $attachment }}</h6>
                                        <p class="attachment-size">PDF Document</p>
                                    </div>
                                    <div class="attachment-actions">
                                        <a href="{{ asset('storage/app/public/attachments/' . $attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                        @can('update', $lessonNote)
                                        <a href="{{ route('lesson-notes.edit', $lessonNote) }}" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-info-circle me-2"></i>Lesson Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <label class="info-label">Students:</label>
                                <div class="info-value">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-user me-2 text-primary"></i>
                                        <span class="fw-bold">{{ $lessonNote->student->user->name }}</span>
                                        <span class="badge bg-primary ms-2">Primary</span>
                                    </div>
                                    
                                    @if($lessonNote->additional_students && count($lessonNote->additional_students) > 0)
                                        @foreach($lessonNote->additional_students as $additionalStudentId)
                                            @php
                                                $additionalStudent = \App\Models\Student::with('user')->find($additionalStudentId);
                                            @endphp
                                            @if($additionalStudent)
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-user me-2 text-secondary"></i>
                                                <span>{{ $additionalStudent->user->name }}</span>
                                                <span class="badge bg-secondary ms-2">Additional</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                    
                                    @if($lessonNote->additional_students && count($lessonNote->additional_students) > 0)
                                    <small class="text-muted">
                                        Total: {{ count($lessonNote->additional_students) + 1 }} students
                                    </small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <label class="info-label">Teacher:</label>
                                <div class="info-value">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>
                                    {{ $lessonNote->teacher->user->name }}
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <label class="info-label">Lesson Date:</label>
                                <div class="info-value">
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ $lessonNote->lesson_date->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            
                            @if($lessonNote->booking)
                            <div class="info-item">
                                <label class="info-label">Session:</label>
                                <div class="info-value">
                                    <i class="fas fa-video me-2"></i>
                                    <a href="{{ route('bookings.show', $lessonNote->booking) }}" class="text-decoration-none">
                                        Session {{ $lessonNote->booking->id }}
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <div class="info-item">
                                <label class="info-label">Created:</label>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ $lessonNote->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            
                            @if($lessonNote->updated_at != $lessonNote->created_at)
                            <div class="info-item">
                                <label class="info-label">Last Updated:</label>
                                <div class="info-value">
                                    <i class="fas fa-edit me-2"></i>
                                    {{ $lessonNote->updated_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @can('delete', $lessonNote)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Once deleted, this lesson note cannot be recovered.</p>
                            <form action="{{ route('lesson-notes.destroy', $lessonNote) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this lesson note?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash me-2"></i>Delete Lesson Note
                                </button>
                            </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
.dashboard-header {
    background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.dashboard-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.dashboard-date {
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.action-section {
    margin-bottom: 2rem;
}

.action-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    border-radius: 20px;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.action-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.action-icon {
    font-size: 3rem;
    opacity: 0.8;
    animation: pulse 2s infinite;
}

.action-text {
    flex: 1;
}

.action-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.action-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.action-button {
    flex-shrink: 0;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.lesson-content {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #333;
}

.attachments-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.attachment-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.2s;
}

.attachment-item:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.attachment-icon {
    font-size: 2rem;
    color: #007bff;
    margin-right: 1rem;
}

.attachment-info {
    flex: 1;
}

.attachment-name {
    margin: 0 0 0.25rem 0;
    color: #333;
    font-size: 1rem;
}

.attachment-size {
    margin: 0;
    color: #6c757d;
    font-size: 0.85rem;
}

.attachment-actions {
    margin-left: 1rem;
}

.info-item {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    color: #333;
    font-size: 0.95rem;
}

.info-value .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endsection
