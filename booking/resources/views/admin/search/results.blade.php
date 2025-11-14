@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="page-title">
                            <i class="fas fa-search me-3"></i>Search Results
                        </h1>
                        <p class="page-subtitle">Search query: "{{ $query ?? 'No query' }}"</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="header-actions">
                            <a href="{{ route('admin.search.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-2"></i>Back to Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card-body">
                    @if(isset($error))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $error }}
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Search Query: <strong>"{{ $query }}"</strong></h5>
                                <p class="text-muted">Search Type: {{ ucfirst($type) }}</p>
                            </div>
                        </div>

                        @if(isset($results['users']) && $results['users']->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary">
                                        <i class="fas fa-users me-2"></i>
                                        Users ({{ $results['users']->count() }})
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($results['users'] as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                    <span class="text-white fw-bold">{{ substr($user->name, 0, 1) }}</span>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                                    @if($user->phone)
                                                                        <small class="text-muted">{{ $user->phone }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            @if($user->role === 'admin')
                                                                <span class="badge bg-danger">Admin</span>
                                                            @elseif($user->role === 'teacher')
                                                                <span class="badge bg-primary">Teacher</span>
                                                            @else
                                                                <span class="badge bg-success">Student</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-1">
                                                                @if($user->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                @endif
                                                                
                                                                @if($user->role === 'teacher' && $user->teacher)
                                                                    @if($user->teacher->is_verified)
                                                                        <span class="badge bg-info">Verified</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Unverified</span>
                                                                    @endif
                                                                    
                                                                    @if($user->teacher->is_available)
                                                                        <span class="badge bg-primary">Available</span>
                                                                    @else
                                                                        <span class="badge bg-danger">Unavailable</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit User">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                
                                                                @if($user->role === 'teacher' && $user->teacher)
                                                                    <form method="POST" action="{{ route('admin.users.toggle-verification', $user) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-sm {{ $user->teacher->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                                                title="{{ $user->teacher->is_verified ? 'Unverify Teacher' : 'Verify Teacher' }}">
                                                                            <i class="fas {{ $user->teacher->is_verified ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                                                        </button>
                                                                    </form>
                                                                    
                                                                    <form method="POST" action="{{ route('admin.users.toggle-availability', $user) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-sm {{ $user->teacher->is_available ? 'btn-outline-danger' : 'btn-outline-primary' }}" 
                                                                                title="{{ $user->teacher->is_available ? 'Make Unavailable' : 'Make Available' }}">
                                                                            <i class="fas {{ $user->teacher->is_available ? 'fa-pause' : 'fa-play' }}"></i>
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
                                    {{ $results['users']->links() }}
                                </div>
                            </div>
                        @endif

                        @if(isset($results['bookings']) && $results['bookings']->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-success">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        Bookings ({{ $results['bookings']->count() }})
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Teacher</th>
                                                    <th>Student</th>
                                                    <th>Date & Time</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($results['bookings'] as $booking)
                                                    <tr>
                                                        <td>{{ $booking->teacher->user->name }}</td>
                                                        <td>{{ $booking->student->user->name }}</td>
                                                        <td>{{ $booking->start_time->format('M d, Y H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'info') }}">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{ $results['bookings']->links() }}
                                </div>
                            </div>
                        @endif

                        @if(isset($results['payments']) && $results['payments']->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-warning">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Payments ({{ $results['payments']->count() }})
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Teacher</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Method</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($results['payments'] as $payment)
                                                    <tr>
                                                        <td>{{ $payment->student->user->name }}</td>
                                                        <td>{{ $payment->teacher->user->name }}</td>
                                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($payment->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{ $results['payments']->links() }}
                                </div>
                            </div>
                        @endif

                        @if(isset($results['feedback']) && $results['feedback']->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-info">
                                        <i class="fas fa-comments me-2"></i>
                                        Feedback ({{ $results['feedback']->count() }})
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Teacher</th>
                                                    <th>Student</th>
                                                    <th>Rating</th>
                                                    <th>Comment</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($results['feedback'] as $feedback)
                                                    <tr>
                                                        <td>{{ $feedback->booking->teacher->user->name }}</td>
                                                        <td>{{ $feedback->booking->student->user->name }}</td>
                                                        <td>
                                                            <div class="rating">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                @endfor
                                                            </div>
                                                        </td>
                                                        <td>{{ Str::limit($feedback->comment, 50) }}</td>
                                                        <td>{{ $feedback->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{ $results['feedback']->links() }}
                                </div>
                            </div>
                        @endif

                        @if(empty($results) || (isset($results['users']) && $results['users']->count() === 0 && 
                            isset($results['bookings']) && $results['bookings']->count() === 0 && 
                            isset($results['payments']) && $results['payments']->count() === 0 && 
                            isset($results['feedback']) && $results['feedback']->count() === 0))
                            <div class="text-center py-4">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No results found</h5>
                                <p class="text-muted">Try adjusting your search criteria or use different keywords.</p>
                                <a href="{{ route('admin.search.index') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>New Search
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .rating {
        display: inline-block;
    }
    
    .rating .fas.fa-star {
        font-size: 14px;
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

.modern-card-body {
    padding: 2rem;
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
