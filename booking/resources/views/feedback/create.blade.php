@extends('layouts.app')



@section('title', 'Provide Feedback')



@section('content')

<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-star me-3"></i>Provide Feedback
            </h1>
            <p class="page-subtitle">Share your experience and help improve our service</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <span class="feedback-badge">
                    <i class="fas fa-heart me-2"></i>Your Voice Matters
                </span>
            </div>
        </div>
    </div>
</div>



<div class="row">

    <div class="col-md-8">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-star me-2"></i>Rate Your Experience

                </h5>

                <p class="modern-card-subtitle">Tell us how your session went</p>

            </div>

            <div class="modern-card-body">

                <form method="POST" action="{{ route('feedback.store.booking', $booking) }}">

                    @csrf

                    

                    <!-- Booking Info -->

                    <div class="mb-4">

                        <h5>Booking Details</h5>

                        <div class="row">

                            <div class="col-md-6">

                                <p><strong>Date:</strong> {{ $booking->start_time->format('M d, Y g:i A') }}</p>

                                <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>

                            </div>

                            <div class="col-md-6">

                                @if(auth()->user()->isStudent())

                                    <p><strong>Teacher:</strong> {{ $booking->teacher->user->name }}</p>

                                @else

                                    <p><strong>Student:</strong> {{ $booking->student->user->name }}</p>

                                @endif

                                <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($booking->status) }}</span></p>

                            </div>

                        </div>

                    </div>



                    <!-- Rating -->

                    <div class="mb-4">

                        <label class="form-label">Rating</label>

                        <div class="rating-stars">

                            @for($i = 1; $i <= 5; $i++)

                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="rating-input" required>

                                <label for="star{{ $i }}" class="rating-star">

                                    <i class="far fa-star"></i>

                                </label>

                            @endfor

                        </div>

                        <div class="rating-labels mt-2">

                            <small class="text-muted">1 = Poor, 2 = Fair, 3 = Good, 4 = Very Good, 5 = Excellent</small>

                        </div>

                    </div>



                    <!-- Comment -->

                    <div class="mb-4">

                        <label for="comment" class="form-label">Comment (Optional)</label>

                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Share your experience with this booking..."></textarea>

                        <div class="form-text">Your feedback helps improve the learning experience.</div>

                    </div>



                    <!-- Public/Private -->

                    <div class="mb-4">

                        <div class="form-check">

                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1">

                            <label class="form-check-label" for="is_public">

                                Make this feedback public

                            </label>

                        </div>

                        <div class="form-text">Public feedback may be visible to other users.</div>

                    </div>



                    @if($errors->any())

                        <div class="alert alert-danger">

                            <ul class="mb-0">

                                @foreach($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif



                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-primary">

                            <i class="fas fa-paper-plane me-2"></i>Submit Feedback

                        </button>

                        <a href="{{ route('feedback.index') }}" class="btn btn-secondary">

                            <i class="fas fa-arrow-left me-2"></i>Back to Feedback

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    

    <div class="col-md-4">

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title"><i class="fas fa-lightbulb me-2"></i>Feedback Guidelines</h5>
                <p class="modern-card-subtitle">Be clear and constructive</p>

            </div>

            <div class="modern-card-body">

                <ul class="list-unstyled mb-0">

                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Be honest and constructive</li>

                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Focus on the learning experience</li>

                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Provide specific examples</li>

                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Maintain a respectful tone</li>

                </ul>

            </div>

        </div>

        

        <div class="modern-card mt-3">

            <div class="modern-card-header">

                <h5 class="modern-card-title"><i class="fas fa-star-half-alt me-2"></i>Rating Guide</h5>
                <p class="modern-card-subtitle">What each rating means</p>

            </div>

            <div class="modern-card-body">

                <div class="mb-2">

                    <div class="d-flex align-items-center">

                        <div class="me-2">

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                        </div>

                        <span class="small">Excellent</span>

                    </div>

                </div>

                <div class="mb-2">

                    <div class="d-flex align-items-center">

                        <div class="me-2">

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="far fa-star text-muted"></i>

                        </div>

                        <span class="small">Very Good</span>

                    </div>

                </div>

                <div class="mb-2">

                    <div class="d-flex align-items-center">

                        <div class="me-2">

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                        </div>

                        <span class="small">Good</span>

                    </div>

                </div>

                <div class="mb-2">

                    <div class="d-flex align-items-center">

                        <div class="me-2">

                            <i class="fas fa-star text-warning"></i>

                            <i class="fas fa-star text-warning"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                        </div>

                        <span class="small">Fair</span>

                    </div>

                </div>

                <div class="mb-2">

                    <div class="d-flex align-items-center">

                        <div class="me-2">

                            <i class="fas fa-star text-warning"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                            <i class="far fa-star text-muted"></i>

                        </div>

                        <span class="small">Poor</span>

                    </div>

                </div>

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

    .page-title { font-size: 2.5rem; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .page-subtitle { font-size: 1.1rem; opacity: 0.9; margin: 0.5rem 0 0 0; }
    .header-actions { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
    .feedback-badge { background: rgba(255,255,255,0.15); padding: .5rem 1rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.25); }

    /* Modern Card */
    .modern-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.08); border: none; overflow: hidden; margin-bottom: 1.5rem; }
    .modern-card-header { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem; border-bottom: 1px solid #e9ecef; }
    .modern-card-title { font-size: 1.25rem; font-weight: 600; color: #2c3e50; margin: 0; }
    .modern-card-subtitle { font-size: .875rem; color: #6c757d; margin: .25rem 0 0 0; }
    .modern-card-body { padding: 1.5rem; }

    .rating-stars {

        display: flex;

        flex-direction: row-reverse;

        justify-content: flex-end;

    }

    

    .rating-input {

        display: none;

    }

    

    .rating-star {

        cursor: pointer;

        font-size: 2rem;

        color: #ddd;

        transition: color 0.2s ease;

    }

    

    .rating-star:hover,

    .rating-star:hover ~ .rating-star,

    .rating-input:checked ~ .rating-star {

        color: #ffc107;

    }

    

    .rating-star i {

        transition: transform 0.2s ease;

    }

    

    .rating-star:hover i,

    .rating-star:hover ~ .rating-star i,

    .rating-input:checked ~ .rating-star i {

        transform: scale(1.1);

    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title { font-size: 2rem; }
        .modern-card-body { padding: 1rem; }
    }

</style>

@endsection



@section('scripts')

<script>

    // Auto-submit form when rating is selected

    document.querySelectorAll('.rating-input').forEach(input => {

        input.addEventListener('change', function() {

            // Optional: Auto-submit after rating selection

            // this.closest('form').submit();

        });

    });

</script>

@endsection

