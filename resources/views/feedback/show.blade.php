@extends('layouts.app')

@section('title', 'Feedback Details')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-star me-3"></i>Feedback Details
            </h1>
            <p class="page-subtitle">View feedback details and information</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('feedback.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Feedback
                </a>
                <a href="{{ route('feedback.edit', $feedback) }}" class="btn btn-outline-light">
                    <i class="fas fa-edit me-2"></i>Edit Feedback
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-star me-2"></i>Feedback Information
                </h5>
                <p class="modern-card-subtitle">Rating and comment details</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Rating</h6>
                        <div class="d-flex align-items-center mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $feedback->rating)
                                    <i class="fas fa-star text-warning me-1" style="font-size: 1.5rem;"></i>
                                @else
                                    <i class="far fa-star text-muted me-1" style="font-size: 1.5rem;"></i>
                                @endif
                            @endfor
                            <span class="ms-3 h5 mb-0 fw-bold">{{ $feedback->rating }}/5</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Feedback Type</h6>
                        <div class="mb-4">
                            @if($feedback->type === 'student_to_teacher')
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-user-graduate me-1"></i>Student → Teacher
                                </span>
                            @else
                                <span class="badge bg-primary fs-6">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Teacher → Student
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($feedback->comment)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Comment</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $feedback->comment }}</p>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Visibility</h6>
                        <div class="mb-4">
                            @if($feedback->is_public)
                                <span class="badge bg-success">
                                    <i class="fas fa-globe me-1"></i>Public
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-lock me-1"></i>Private
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Created</h6>
                        <div class="mb-4">
                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                            {{ $feedback->created_at->format('M d, Y g:i A') }}
                        </div>
                    </div>
                </div>

                @if($feedback->updated_at != $feedback->created_at)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Last Updated</h6>
                            <div class="mb-4">
                                <i class="fas fa-edit me-1 text-muted"></i>
                                {{ $feedback->updated_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-info-circle me-2"></i>Session Information
                </h5>
                <p class="modern-card-subtitle">Details about the session</p>
            </div>
            <div class="modern-card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Session Date & Time</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        <span>{{ $feedback->booking->start_time->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center mt-1">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <span>{{ $feedback->booking->start_time->format('g:i A') }} - {{ $feedback->booking->end_time->format('g:i A') }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Duration</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-hourglass-half me-2 text-primary"></i>
                        <span>{{ $feedback->booking->duration_minutes }} minutes</span>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Session Status</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        <span class="badge bg-success">{{ ucfirst($feedback->booking->status) }}</span>
                    </div>
                </div>

                @if($feedback->type === 'student_to_teacher')
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Teacher</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                <span class="text-white fw-bold">{{ substr($feedback->booking->teacher->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $feedback->booking->teacher->user->name }}</div>
                                <small class="text-muted">{{ $feedback->booking->teacher->user->email }}</small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Student</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">
                                <span class="text-white fw-bold">{{ substr($feedback->booking->student->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $feedback->booking->student->user->name }}</div>
                                <small class="text-muted">{{ $feedback->booking->student->user->email }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
                <p class="modern-card-subtitle">Available actions for this feedback</p>
            </div>
            <div class="modern-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('feedback.edit', $feedback) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit Feedback
                    </a>
                    <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this feedback? This action cannot be undone.')">
                            <i class="fas fa-trash me-1"></i>Delete Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --brand-black: #000000;
        --brand-red: #ef473e;
        --brand-orange: #fdb838;
        --brand-dark-blue: #070c39;
        --brand-gradient: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);
        --font-family: 'Sora', sans-serif;
        --font-weight-semibold: 600;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background: var(--brand-gradient);
        color: white;
        border-radius: 12px 12px 0 0 !important;
        border-bottom: none;
        font-weight: var(--font-weight-semibold);
    }
    
    .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .btn-primary {
        background: var(--brand-gradient);
        border: none;
        border-radius: 8px;
        font-weight: var(--font-weight-semibold);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 71, 62, 0.3);
    }
    
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
    
    /* Button Styling */
    .btn {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
    }
    
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        background: transparent;
    }
    
    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
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
@endsection
