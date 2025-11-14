@extends('layouts.app')



@section('title', 'Feedback & Ratings')



@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-star me-3"></i>Feedback & Ratings
            </h1>
            <p class="page-subtitle">View and manage feedback from your sessions</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <span class="feedback-count">
                    <i class="fas fa-comments me-2"></i>
                    {{ $feedbacks->count() }} Feedback
                </span>
            </div>
        </div>
    </div>
</div>



<div class="modern-card">

    <div class="modern-card-header">

        <h5 class="modern-card-title">

            <i class="fas fa-list me-2"></i>All Feedback

        </h5>

        <p class="modern-card-subtitle">Browse and review all feedback and ratings</p>

    </div>

    <div class="modern-card-body">

        @if($feedbacks->count() > 0)

            <div class="modern-table-container">

                <table class="modern-table">

                    <thead>

                        <tr>

                            <th>Booking</th>

                            <th>Details</th>

                            <th>Rating</th>

                            <th>Comment</th>

                            <th>Type</th>

                            <th>Date</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($feedbacks as $feedback)

                            <tr>

                                <td>

                                    @if(auth()->user()->isStudent())

                                        <div class="d-flex align-items-center">

                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">

                                                <span class="text-white fw-bold">{{ substr($feedback->booking->teacher->user->name, 0, 1) }}</span>

                                            </div>

                                            <div>

                                                <div class="fw-bold">{{ $feedback->booking->teacher->user->name }}</div>

                                                <small class="text-muted">{{ $feedback->booking->start_time->format('M d, Y g:i A') }}</small>

                                            </div>

                                        </div>

                                    @elseif(auth()->user()->isTeacher())

                                        <div class="d-flex align-items-center">

                                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">

                                                <span class="text-white fw-bold">{{ substr($feedback->booking->student->user->name, 0, 1) }}</span>

                                            </div>

                                            <div>

                                                <div class="fw-bold">{{ $feedback->booking->student->user->name }}</div>

                                                <small class="text-muted">{{ $feedback->booking->start_time->format('M d, Y g:i A') }}</small>

                                            </div>

                                        </div>

                                    @else

                                        <div class="d-flex align-items-center">

                                            <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">

                                                <span class="text-white fw-bold">{{ substr($feedback->booking->teacher->user->name, 0, 1) }}</span>

                                            </div>

                                            <div>

                                                <div class="fw-bold">{{ $feedback->booking->teacher->user->name }} → {{ $feedback->booking->student->user->name }}</div>

                                                <small class="text-muted">{{ $feedback->booking->start_time->format('M d, Y g:i A') }}</small>

                                            </div>

                                        </div>

                                    @endif

                                </td>

                                <td>

                                    <span class="badge bg-light text-dark">{{ $feedback->booking->duration_minutes }} min</span>

                                </td>

                                <td>

                                    <div class="d-flex align-items-center">

                                        @for($i = 1; $i <= 5; $i++)

                                            @if($i <= $feedback->rating)

                                                <i class="fas fa-star text-warning me-1"></i>

                                            @else

                                                <i class="far fa-star text-muted me-1"></i>

                                            @endif

                                        @endfor

                                        <span class="ms-2 fw-bold">{{ $feedback->rating }}/5</span>

                                    </div>

                                </td>

                                <td>

                                    @if($feedback->comment)

                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $feedback->comment }}">

                                            {{ $feedback->comment }}

                                        </div>

                                    @else

                                        <span class="text-muted">No comment</span>

                                    @endif

                                </td>

                                <td>

                                    @if($feedback->type === 'student_to_teacher')

                                        <span class="badge bg-info">Student → Teacher</span>

                                    @else

                                        <span class="badge bg-primary">Teacher → Student</span>

                                    @endif

                                </td>

                                <td>{{ $feedback->created_at->format('M d, Y') }}</td>

                                <td>

                                    <div class="btn-group" role="group">

                                        <a href="{{ route('feedback.show', $feedback) }}" class="btn btn-sm btn-outline-primary">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                        @if(auth()->user()->isStudent() && $feedback->student_id == auth()->user()->student->id && $feedback->type === 'student_to_teacher')
                                            <a href="{{ route('feedback.edit', $feedback) }}" class="btn btn-sm btn-outline-warning">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                            <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" class="d-inline">

                                                @csrf

                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>
                                        @elseif(auth()->user()->isTeacher() && $feedback->teacher_id == auth()->user()->teacher->id && $feedback->type === 'teacher_to_student')
                                            <a href="{{ route('feedback.edit', $feedback) }}" class="btn btn-sm btn-outline-warning">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                            <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" class="d-inline">

                                                @csrf

                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>
                                        @elseif(auth()->user()->isAdmin())
                                            <a href="{{ route('feedback.edit', $feedback) }}" class="btn btn-sm btn-outline-warning">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                            <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" class="d-inline">

                                                @csrf

                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            

            <div class="pagination-container">

                {{ $feedbacks->links() }}

            </div>

        @else

            <div class="text-center py-4">

                <i class="fas fa-star fa-3x text-muted mb-3"></i>

                <h5 class="text-muted">No feedback found</h5>

                <p class="text-muted">You haven't provided any feedback yet.</p>

            </div>

        @endif

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
    
    .feedback-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
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
        border-bottom: 1px solid #f8f9fa;
        vertical-align: middle;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Enhanced Star Rating Display */
    .modern-table .fas.fa-star {
        color: #ffc107 !important;
        font-size: 1.1rem;
    }
    
    .modern-table .far.fa-star {
        color: #dee2e6 !important;
        font-size: 1.1rem;
    }
    
    /* Modern Badge Styles */
    .modern-table .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .modern-table .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }
    
    .modern-table .badge.bg-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%) !important;
    }
    
    .modern-table .badge.bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        color: #495057 !important;
        border: 1px solid #dee2e6;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
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

