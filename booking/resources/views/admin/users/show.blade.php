@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-user me-3"></i>User Details
            </h1>
            <p class="page-subtitle">{{ $user->name }} - User Information</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="header-actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-edit me-2"></i>Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-info-circle me-2"></i>User Information
                </h5>
                <p class="modern-card-subtitle">Complete user profile details</p>
            </div>
            <div class="modern-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
                        @if($user->wordpress_id)
                            <p><strong>WordPress ID:</strong> {{ $user->wordpress_id }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p><strong>Role:</strong> 
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @elseif($user->role === 'teacher')
                                <span class="badge bg-primary">Teacher</span>
                            @else
                                <span class="badge bg-success">Student</span>
                            @endif
                        </p>
                        @if($user->membership_level)
                            <p><strong>Membership Level:</strong> 
                                <span class="badge bg-info">{{ $user->membership_level }}</span>
                            </p>
                        @endif
                        <p><strong>Status:</strong> 
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="modern-card">
            <div class="modern-card-header">
                <h5 class="modern-card-title">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <p class="modern-card-subtitle">Manage this user account</p>
            </div>
            <div class="modern-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    @if($user->canBeDeleted())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash me-2"></i>Delete User
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary w-100" disabled title="{{ $user->getDeletionBlockReason() }}">
                            <i class="fas fa-lock me-2"></i>Cannot Delete User
                        </button>
                    @endif
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
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        border: none;
        color: white;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(255, 193, 7, 0.3);
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
