@extends('layouts.app')

@section('title', 'Lesson Log')

@section('content')
<!-- Welcome Header -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="dashboard-title">
                <i class="fas fa-book me-3"></i>Lesson Log
                @if(request('student_id'))
                    @php
                        $selectedStudent = \App\Models\Student::with('user')->find(request('student_id'));
                    @endphp
                    @if($selectedStudent)
                        <small class="d-block fs-6 mt-2 opacity-75">
                            <i class="fas fa-user me-2"></i>{{ $selectedStudent->user->name }}
                        </small>
                    @endif
                @endif
            </h1>
            <p class="dashboard-subtitle">
                @if(request('student_id'))
                    Viewing lesson notes for {{ $selectedStudent->user->name ?? 'selected student' }}.
                @else
                    Track your learning journey and review what you've covered in each session.
                @endif
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="action-button">
                @can('create', \App\Models\LessonNote::class)
                <a href="{{ route('lesson-notes.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Add Lesson Note
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Student Filter Section -->
@if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-filter me-2"></i>Filter by Student
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('lesson-notes.index') }}" class="row g-3">
                        <div class="col-md-8">
                            <select name="student_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Students</option>
                                @if(Auth::user()->isTeacher())
                                    @php
                                        $teacherId = Auth::user()->teacher->id;
                                        $studentIds = \App\Models\Booking::where('teacher_id', $teacherId)
                                            ->where('status', 'confirmed')
                                            ->distinct()
                                            ->pluck('student_id')
                                            ->toArray();
                                        $students = \App\Models\Student::with('user')->whereIn('id', $studentIds)->get();
                                    @endphp
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name }}
                                        </option>
                                    @endforeach
                                @elseif(Auth::user()->isAdmin())
                                    @foreach(\App\Models\Student::with('user')->get() as $student)
                                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            @if(request('student_id'))
                                <a href="{{ route('lesson-notes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            @if($lessonNotes->count() > 0)
                <div class="lesson-log-timeline">
                    @foreach($lessonNotes as $note)
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h4 class="timeline-title">{{ $note->title }}</h4>
                                <div class="timeline-meta">
                                    <span class="lesson-date">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $note->lesson_date->format('M d, Y g:i A') }}
                                    </span>
                                    <span class="teacher-info ms-3">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $note->teacher->user->name }}
                                    </span>
                                    @if($note->additional_students && count($note->additional_students) > 0)
                                    <span class="students-info ms-3">
                                        <i class="fas fa-users me-1"></i>
                                        {{ count($note->additional_students) + 1 }} students
                                    </span>
                                    @endif
                                    @if($note->booking)
                                    <span class="booking-info ms-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        Session {{ $note->booking->id }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($note->content)
                            <div class="timeline-body">
                                <p class="lesson-content">{{ $note->content }}</p>
                            </div>
                            @endif

                            @if($note->attachments && is_array($note->attachments) && count($note->attachments) > 0)
                            <div class="timeline-attachments">
                                <h6 class="attachments-title">Attachments:</h6>
                                <div class="attachments-list">
                                    @foreach($note->attachments as $attachment)
                                    @if(is_array($attachment) && isset($attachment['url']))
                                    <a href="{{ $attachment['url'] }}" target="_blank" class="attachment-link">
                                        <i class="fas fa-paperclip me-1"></i>
                                        {{ $attachment['name'] ?? 'Attachment' }}
                                    </a>
                                    @elseif(is_string($attachment))
                                    <a href="{{ asset('storage/app/public/attachments/' . $attachment) }}" target="_blank" class="attachment-link">
                                        <i class="fas fa-file-pdf me-1 text-danger"></i>
                                        {{ $attachment }}
                                    </a>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="timeline-actions">
                                <a href="{{ route('lesson-notes.show', $note) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                @can('update', $note)
                                <a href="{{ route('lesson-notes.edit', $note) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $lessonNotes->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="empty-title">No Lesson Notes Yet</h3>
                    <p class="empty-description">
                        @if(Auth::user()->isStudent())
                            Your teachers haven't added any lesson notes yet. Once they do, you'll see your learning journey here.
                        @else
                            Start documenting your students' progress by adding lesson notes.
                        @endif
                    </p>
                    @can('create', \App\Models\LessonNote::class)
                    <a href="{{ route('lesson-notes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Your First Lesson Note
                    </a>
                    @endcan
                </div>
            @endif
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
    background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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

.lesson-log-timeline {
    position: relative;
    padding-left: 30px;
}

.lesson-log-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #28a745);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    z-index: 1;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-left: 1rem;
}

.timeline-header {
    margin-bottom: 1rem;
}

.timeline-title {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.timeline-meta {
    font-size: 0.9rem;
    color: #6c757d;
}

.teacher-info, .booking-info {
    display: inline-block;
}

.timeline-body {
    margin-bottom: 1rem;
}

.lesson-content {
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.timeline-attachments {
    margin-bottom: 1rem;
}

.attachments-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.attachments-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.attachment-link {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #495057;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.attachment-link:hover {
    background: #e9ecef;
    color: #007bff;
    text-decoration: none;
}

.timeline-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-title {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: #6c757d;
    margin-bottom: 2rem;
}
</style>
@endsection
