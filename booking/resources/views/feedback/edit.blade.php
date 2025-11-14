@extends('layouts.app')

@section('title', 'Edit Feedback')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-edit me-3"></i>Edit Feedback
            </h1>
            <p class="page-subtitle">Update your feedback and rating</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('feedback.show', $feedback) }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Details
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
                    <i class="fas fa-star me-2"></i>Update Feedback
                </h5>
                <p class="modern-card-subtitle">Modify your rating and comments</p>
            </div>
            <div class="modern-card-body">
                <form method="POST" action="{{ route('feedback.update', $feedback) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="rating" class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                        <div class="rating-input modern-rating">
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" 
                                               value="{{ $i }}" {{ old('rating', $feedback->rating) == $i ? 'checked' : '' }}>
                                        <label class="form-check-label modern-star-label" for="rating{{ $i }}">
                                            <i class="fas fa-star modern-star" style="font-size: 1.8rem;"></i>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            <div class="mt-3">
                                <div class="rating-display">
                                    <span class="current-rating">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Current rating: <strong>{{ $feedback->rating }}/5</strong>
                                    </span>
                                    @if(old('rating'))
                                        <span class="new-rating ms-3">
                                            <i class="fas fa-arrow-right me-1"></i>
                                            New rating: <strong>{{ old('rating') }}/5</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @error('rating')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="form-label fw-bold">Comment</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  id="comment" name="comment" rows="4" 
                                  placeholder="Share your thoughts about the session...">{{ old('comment', $feedback->comment) }}</textarea>
                        <div class="form-text">
                            Maximum 500 characters. This comment will be visible to the other party.
                        </div>
                        @error('comment')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" 
                                   value="1" {{ old('is_public', $feedback->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_public">
                                Make this feedback public
                            </label>
                        </div>
                        <div class="form-text">
                            Public feedback may be visible to other users and can help build trust in the community.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('feedback.show', $feedback) }}" class="btn btn-outline-secondary modern-btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary modern-btn-primary">
                            <i class="fas fa-save me-1"></i>Update Feedback
                        </button>
                    </div>
                </form>
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
                    <h6 class="text-muted mb-2">Feedback Type</h6>
                    <div class="d-flex align-items-center">
                        @if($feedback->type === 'student_to_teacher')
                            <i class="fas fa-user-graduate me-2 text-info"></i>
                            <span class="badge bg-info">Student → Teacher</span>
                        @else
                            <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>
                            <span class="badge bg-primary">Teacher → Student</span>
                        @endif
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
                    <i class="fas fa-eye me-2"></i>Current Feedback
                </h5>
                <p class="modern-card-subtitle">Your existing feedback</p>
            </div>
            <div class="modern-card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Current Rating</h6>
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
                </div>

                @if($feedback->comment)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Current Comment</h6>
                        <div class="bg-light p-2 rounded">
                            <small>{{ $feedback->comment }}</small>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Visibility</h6>
                    <div class="d-flex align-items-center">
                        @if($feedback->is_public)
                            <i class="fas fa-globe me-2 text-success"></i>
                            <span class="badge bg-success">Public</span>
                        @else
                            <i class="fas fa-lock me-2 text-secondary"></i>
                            <span class="badge bg-secondary">Private</span>
                        @endif
                    </div>
                </div>

                <div class="text-muted">
                    <small>
                        <i class="fas fa-calendar-alt me-1"></i>
                        Created: {{ $feedback->created_at->format('M d, Y') }}
                    </small>
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
    
    .header-actions .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .header-actions .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        color: white;
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
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .modern-card-subtitle {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0.5rem 0 0 0;
        font-weight: 500;
    }
    
    .modern-card-body {
        padding: 1.5rem;
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
    
    .rating-input .form-check-input {
        display: none;
    }
    
    .rating-input .form-check-label {
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .rating-input .form-check-label:hover {
        transform: scale(1.1);
    }
    
    .rating-input .form-check-input:checked + .form-check-label i {
        color: var(--brand-orange) !important;
    }
    
    /* Modern Rating Styles */
    .modern-rating .modern-star-label {
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0.5rem;
        border-radius: 8px;
        margin: 0 0.25rem;
    }
    
    .modern-rating .modern-star-label:hover {
        transform: scale(1.15);
        background: rgba(239, 71, 62, 0.1);
    }
    
    .modern-rating .modern-star {
        color: #dee2e6;
        transition: all 0.3s ease;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .modern-rating .form-check-input:checked + .modern-star-label .modern-star {
        color: #ffc107 !important;
        filter: drop-shadow(0 4px 8px rgba(255, 193, 7, 0.4));
    }
    
    .modern-rating .modern-star-label:hover .modern-star {
        color: #ffc107;
        filter: drop-shadow(0 4px 8px rgba(255, 193, 7, 0.3));
    }
    
    /* Force star colors for current rating */
    .modern-rating .modern-star-label:nth-child(1) .modern-star,
    .modern-rating .modern-star-label:nth-child(2) .modern-star,
    .modern-rating .modern-star-label:nth-child(3) .modern-star,
    .modern-rating .modern-star-label:nth-child(4) .modern-star,
    .modern-rating .modern-star-label:nth-child(5) .modern-star {
        color: #ffc107 !important;
        filter: drop-shadow(0 4px 8px rgba(255, 193, 7, 0.4));
    }
    
    .rating-display {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #dee2e6;
    }
    
    .current-rating {
        color: #495057;
        font-size: 0.9rem;
    }
    
    .new-rating {
        color: #ef473e;
        font-size: 0.9rem;
    }
    
    .form-control:focus {
        border-color: var(--brand-red);
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
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
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for rating selection
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingLabels = document.querySelectorAll('label[for^="rating"]');
    
    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            // Update visual feedback with modern styling
            ratingLabels.forEach((label, labelIndex) => {
                const star = label.querySelector('i');
                if (labelIndex <= index) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                    star.style.color = '#ffc107';
                    star.style.filter = 'drop-shadow(0 4px 8px rgba(255, 193, 7, 0.4))';
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                    star.style.color = '#dee2e6';
                    star.style.filter = 'drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1))';
                }
            });
            
            // Update rating display
            updateRatingDisplay();
        });
    });
    
    // Add hover effects
    ratingLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            const star = label.querySelector('i');
            star.style.color = '#ffc107';
            star.style.filter = 'drop-shadow(0 4px 8px rgba(255, 193, 7, 0.3))';
        });
        
        label.addEventListener('mouseleave', function() {
            const input = document.getElementById('rating' + (index + 1));
            if (!input.checked) {
                const star = label.querySelector('i');
                star.style.color = '#dee2e6';
                star.style.filter = 'drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1))';
            }
        });
    });
    
    function updateRatingDisplay() {
        const selectedRating = document.querySelector('input[name="rating"]:checked');
        if (selectedRating) {
            const newRatingSpan = document.querySelector('.new-rating');
            if (newRatingSpan) {
                newRatingSpan.innerHTML = `<i class="fas fa-arrow-right me-1"></i>New rating: <strong>${selectedRating.value}/5</strong>`;
            }
        }
    }
    
    // Character counter for comment
    const commentTextarea = document.getElementById('comment');
    const maxLength = 500;
    
    if (commentTextarea) {
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        commentTextarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - commentTextarea.value.length;
            counter.textContent = `${commentTextarea.value.length}/${maxLength} characters`;
            counter.style.color = remaining < 50 ? '#dc3545' : '#6c757d';
        }
        
        commentTextarea.addEventListener('input', updateCounter);
        updateCounter();
    }
});
</script>
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
    
    /* Form Styling */
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.95rem;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
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
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.3);
        color: white;
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
    
    /* Modern Button Styles */
    .modern-btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(239, 71, 62, 0.2);
    }
    
    .modern-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(239, 71, 62, 0.4);
        color: white !important;
    }
    
    .modern-btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
    }
    
    .modern-btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        color: white !important;
    }
    
    /* Override Bootstrap button styles */
    .btn.modern-btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%) !important;
        border: none !important;
        color: white !important;
    }
    
    .btn.modern-btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        border: none !important;
        color: white !important;
    }
    
    /* Enhanced Form Styling */
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafbfc;
    }
    
    .form-control:focus {
        border-color: #ef473e;
        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.25);
        outline: none;
        background: white;
    }
    
    /* Enhanced Badge Styling */
    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        color: white;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%) !important;
        color: white;
    }
    
    /* Enhanced Icon Styling */
    .text-primary {
        color: #ef473e !important;
    }
    
    .text-info {
        color: #17a2b8 !important;
    }
    
    /* Enhanced Section Headers */
    .modern-card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .modern-card-subtitle {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0.5rem 0 0 0;
        font-weight: 500;
    }
    
    /* Force modern styling overrides */
    .modern-card {
        background: white !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        border: none !important;
        overflow: hidden !important;
        margin-bottom: 1.5rem !important;
    }
    
    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        padding: 1.5rem !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    
    .modern-card-body {
        padding: 1.5rem !important;
    }
    
    /* Force button styling */
    .btn.modern-btn-primary,
    .modern-btn-primary {
        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.75rem 2rem !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 12px rgba(239, 71, 62, 0.2) !important;
    }
    
    .btn.modern-btn-secondary,
    .modern-btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.75rem 2rem !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2) !important;
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
