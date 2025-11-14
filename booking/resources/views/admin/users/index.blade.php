@extends('layouts.app')



@section('title', 'User Management')



@section('content')

<!-- Page Header -->

<div class="page-header mb-4">

    <div class="row align-items-center">

        <div class="col-md-8">

            <h1 class="page-title">

                <i class="fas fa-users me-3"></i>User Management

            </h1>

            <p class="page-subtitle">Manage teachers, students, and administrators</p>

        </div>

        <div class="col-md-4 text-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Add New
            </a>
            <a href="{{ route('admin.users.trashed') }}" class="btn btn-danger btn-lg">
                <i class="fas fa-trash me-2"></i>Trashed
            </a>
        </div>

    </div>

</div>



<!-- Filters Section -->

<div class="modern-card mb-4">

    <div class="modern-card-header">

        <h5 class="modern-card-title">

            <i class="fas fa-filter me-2"></i>Advanced Filters

        </h5>

        <p class="modern-card-subtitle">Filter users by role, status, and other criteria</p>

    </div>

    <div class="modern-card-body">

        <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm" class="filter-form">

            <div class="filter-grid">

                <div class="filter-group">

                    <label for="role" class="form-label">

                        <i class="fas fa-user-tag me-2"></i>Role

                    </label>

                    <select class="form-select modern-select" id="role" name="role">

                        <option value="">All Roles</option>

                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>

                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>

                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>

                    </select>

                </div>

                
                
                <div class="filter-group">

                    <label for="status" class="form-label">

                        <i class="fas fa-toggle-on me-2"></i>Status

                    </label>

                    <select class="form-select modern-select" id="status" name="status">

                        <option value="">All Status</option>

                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>

                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>

                    </select>

                </div>

                
                
                <div class="filter-group">

                    <label for="teacher_verification" class="form-label">

                        <i class="fas fa-certificate me-2"></i>Teacher Verification

                    </label>

                    <select class="form-select modern-select" id="teacher_verification" name="teacher_verification">

                        <option value="">All Teachers</option>

                        <option value="verified" {{ request('teacher_verification') === 'verified' ? 'selected' : '' }}>Verified</option>

                        <option value="unverified" {{ request('teacher_verification') === 'unverified' ? 'selected' : '' }}>Unverified</option>

                    </select>

                </div>

                
                
                <div class="filter-group">

                    <label for="teacher_availability" class="form-label">

                        <i class="fas fa-clock me-2"></i>Teacher Availability

                    </label>

                    <select class="form-select modern-select" id="teacher_availability" name="teacher_availability">

                        <option value="">All Teachers</option>

                        <option value="available" {{ request('teacher_availability') === 'available' ? 'selected' : '' }}>Available</option>

                        <option value="unavailable" {{ request('teacher_availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>

                    </select>

                </div>

            </div>

            
            
            <div class="search-section">

                <div class="search-input-group">

                    <i class="fas fa-search search-icon"></i>

                    <input type="text" class="search-input" id="search" name="search" 

                           value="{{ request('search') }}" placeholder="Search by name, email, or phone...">

                    <button type="submit" class="btn btn-primary search-btn">

                        <i class="fas fa-search me-1"></i>Search

                    </button>

                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary clear-btn">

                        <i class="fas fa-times me-1"></i>Clear

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>



<div class="modern-card">

    <div class="modern-card-header">

        <div class="row align-items-center">

            <div class="col-md-6">

                <h5 class="modern-card-title">

                    <i class="fas fa-users me-2"></i>Users

                    @if(request()->hasAny(['role', 'status', 'teacher_verification', 'teacher_availability', 'search']))

                        <span class="filter-badge">Filtered</span>

                    @endif

                </h5>

                <p class="modern-card-subtitle">Manage all system users and their permissions</p>

            </div>

            <div class="col-md-6 text-end">

                <div class="results-info">

                    <i class="fas fa-info-circle me-2"></i>

                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results

                </div>

            </div>

        </div>

    </div>

    <div class="modern-card-body">

        @if($users->count() > 0)

            <div class="modern-table-container">

                <table class="modern-table">

                    <thead>

                        <tr>

                            <th>User</th>

                            <th>Contact</th>

                            <th>Role</th>

                            <th>Status</th>

                            <th>Created</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($users as $user)

                            <tr class="user-row">

                                <td>

                                    <div class="user-info">

                                        <div class="user-avatar">

                                            <img src="{{ $user->small_profile_picture_url }}" 

                                                 alt="{{ $user->name }}"

                                                 class="avatar-img">

                                        </div>

                                        <div class="user-details">

                                            <div class="user-name">{{ $user->name }}</div>

                                            @if($user->phone)

                                                <div class="user-phone">{{ $user->phone }}</div>

                                            @endif

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="contact-info">

                                        <div class="contact-email">{{ $user->email }}</div>

                                    </div>

                                </td>

                                <td>

                                    @if($user->role === 'admin')

                                        <span class="role-badge role-admin">

                                            <i class="fas fa-crown me-1"></i>Admin

                                        </span>

                                    @elseif($user->role === 'teacher')

                                        <span class="role-badge role-teacher">

                                            <i class="fas fa-chalkboard-teacher me-1"></i>Teacher

                                        </span>

                                    @else

                                        <span class="role-badge role-student">

                                            <i class="fas fa-user-graduate me-1"></i>Student

                                        </span>

                                    @endif
                                    
                                    @if($user->membership_level)
                                        <div class="mt-1">
                                            <span class="badge bg-info" style="font-size: 0.75rem;">
                                                <i class="fas fa-star me-1"></i>{{ $user->membership_level }}
                                            </span>
                                        </div>
                                    @endif

                                </td>

                                <td>

                                    <div class="status-badges">

                                        @if($user->is_active)

                                            <span class="status-badge status-active">

                                                <i class="fas fa-check-circle me-1"></i>Active

                                            </span>

                                        @else

                                            <span class="status-badge status-inactive">

                                                <i class="fas fa-times-circle me-1"></i>Inactive

                                            </span>

                                        @endif

                                        
                                        
                                        @if($user->role === 'teacher' && $user->teacher)

                                            @if($user->teacher->is_verified)

                                                <span class="status-badge status-verified">

                                                    <i class="fas fa-certificate me-1"></i>Verified

                                                </span>

                                            @else

                                                <span class="status-badge status-unverified">

                                                    <i class="fas fa-exclamation-triangle me-1"></i>Unverified

                                                </span>

                                            @endif

                                            
                                            
                                            @if($user->teacher->is_available)

                                                <span class="status-badge status-available">

                                                    <i class="fas fa-clock me-1"></i>Available

                                                </span>

                                            @else

                                                <span class="status-badge status-unavailable">

                                                    <i class="fas fa-pause me-1"></i>Unavailable

                                                </span>

                                            @endif

                                        @endif

                                    </div>

                                </td>

                                <td>

                                    <div class="date-info">

                                        <div class="date-primary">{{ $user->created_at->format('M d, Y') }}</div>

                                        <div class="date-secondary">{{ $user->created_at->format('g:i A') }}</div>

                                    </div>

                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary action-btn" title="View Details">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning action-btn" title="Edit User">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                        <!-- Resend Password Email Button -->
                                        <form method="POST" action="{{ route('admin.users.resend-password', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary action-btn resend-password-btn" 
                                                    title="Resend Password Setup Email" 
                                                    onclick="return confirm('Send a new password to {{ $user->email }}? The current password will be replaced.')">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </form>

                                        
                                        
                                        @if($user->role === 'teacher' && $user->teacher)

                                            <form method="POST" action="{{ route('admin.users.toggle-verification', $user) }}" class="d-inline">

                                                @csrf

                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm {{ $user->teacher->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }} action-btn" 

                                                        title="{{ $user->teacher->is_verified ? 'Unverify Teacher' : 'Verify Teacher' }}">

                                                    <i class="fas {{ $user->teacher->is_verified ? 'fa-times-circle' : 'fa-check-circle' }}"></i>

                                                </button>

                                            </form>

                                            
                                            
                                            <form method="POST" action="{{ route('admin.users.toggle-availability', $user) }}" class="d-inline">

                                                @csrf

                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm {{ $user->teacher->is_available ? 'btn-outline-danger' : 'btn-outline-primary' }} action-btn" 

                                                        title="{{ $user->teacher->is_available ? 'Make Unavailable' : 'Make Available' }}">

                                                    <i class="fas {{ $user->teacher->is_available ? 'fa-pause' : 'fa-play' }}"></i>

                                                </button>

                                            </form>

                                        @endif

                                        

                                        <!-- Auto-login button (always show for non-admin users) -->
                                        @if($user->role !== 'admin')
                                            <form method="POST" action="{{ route('admin.users.auto-login', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info action-btn auto-login-btn" title="Login as {{ $user->name }}" onclick="return confirm('Are you sure you want to login as {{ $user->name }}?')">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($user->canBeDeleted())
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger action-btn" 
                                                title="Delete User" 
                                                onclick="showDeleteConfirmation(
                                                    '{{ $user->name }} ({{ ucfirst($user->role) }})',
                                                    'User',
                                                    '{{ route('admin.users.delete') }}',
                                                    [],
                                                    { id: {{ $user->id }} }
                                                )">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        @else

                                            <button type="button" class="btn btn-sm btn-outline-secondary action-btn" title="{{ $user->getDeletionBlockReason() }}" disabled>

                                                <i class="fas fa-lock"></i>

                                            </button>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            
            
            <div class="pagination-container">

                {{ $users->links() }}

            </div>

        @else

            <div class="empty-state">

                <div class="empty-state-icon">

                    <i class="fas fa-users"></i>

                </div>

                <h5 class="empty-state-title">No users found</h5>

                <p class="empty-state-description">Start by adding your first user to the system.</p>

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">

                    <i class="fas fa-plus me-2"></i>Add User

                </a>

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

    
    
    .filter-badge {

        background: linear-gradient(135deg, #ef473e, #fdb838);

        color: white;

        padding: 0.25rem 0.75rem;

        border-radius: 20px;

        font-size: 0.75rem;

        font-weight: 600;

        margin-left: 0.5rem;

    }

    
    
    .results-info {

        background: rgba(255, 255, 255, 0.1);

        padding: 0.5rem 1rem;

        border-radius: 8px;

        font-size: 0.875rem;

        color: #6c757d;

    }

    
    
    /* Filter Form */

    .filter-form {

        max-width: 100%;

    }

    
    
    .filter-grid {

        display: grid;

        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));

        gap: 1.5rem;

        margin-bottom: 2rem;

    }

    
    
    .filter-group {

        display: flex;

        flex-direction: column;

    }

    
    
    .form-label {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.75rem;

        display: block;

        font-size: 0.95rem;

    }

    
    
    .form-label i {

        color: #ef473e;

    }

    
    
    .modern-select {

        border: 2px solid #e9ecef;

        border-radius: 12px;

        padding: 0.875rem 1rem;

        font-size: 0.95rem;

        transition: all 0.3s ease;

        background: #fafafa;

    }

    
    
    .modern-select:focus {

        border-color: #ef473e;

        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);

        background: white;

        outline: none;

    }

    
    
    /* Search Section */

    .search-section {

        margin-top: 1.5rem;

    }

    
    
    .search-input-group {

        position: relative;

        display: flex;

        align-items: center;

        background: white;

        border-radius: 12px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

        overflow: hidden;

        border: 2px solid transparent;

        transition: all 0.3s ease;

    }

    
    
    .search-input-group:focus-within {

        border-color: #ef473e;

        box-shadow: 0 4px 16px rgba(239, 71, 62, 0.2);

    }

    
    
    .search-icon {

        position: absolute;

        left: 1rem;

        color: #6c757d;

        z-index: 2;

    }

    
    
    .search-input {

        border: none;

        padding: 0.75rem 1rem 0.75rem 3rem;

        font-size: 0.95rem;

        background: transparent;

        flex: 1;

        outline: none;

    }

    
    
    .search-btn, .clear-btn {

        border: none;

        padding: 0.75rem 1.5rem;

        font-weight: 600;

        transition: all 0.3s ease;

    }

    
    
    .search-btn {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        color: white;

    }

    
    
    .search-btn:hover {

        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);

        color: white;

    }

    
    
    .clear-btn {

        background: #6c757d;

        color: white;

    }

    
    
    .clear-btn:hover {

        background: #5a6268;

        color: white;

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

        border-bottom: 1px solid #f1f3f4;

        vertical-align: middle;

    }

    
    
    .modern-table tbody tr {

        transition: all 0.3s ease;

    }

    
    
    .modern-table tbody tr:hover {

        background: #f8f9fa;

        transform: translateY(-1px);

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    }

    
    
    /* User Info */

    .user-info {

        display: flex;

        align-items: center;

        gap: 1rem;

    }

    
    
    .user-avatar {

        width: 50px;

        height: 50px;

        border-radius: 50%;

        overflow: hidden;

        border: 3px solid white;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    }

    
    
    .avatar-img {

        width: 100%;

        height: 100%;

        object-fit: cover;

    }

    
    
    .user-details {

        flex: 1;

    }

    
    
    .user-name {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.25rem;

    }

    
    
    .user-phone {

        font-size: 0.875rem;

        color: #6c757d;

    }

    
    
    /* Contact Info */

    .contact-info {

        display: flex;

        flex-direction: column;

    }

    
    
    .contact-email {

        font-weight: 500;

        color: #2c3e50;

    }

    
    
    /* Role Badges */

    .role-badge {

        display: inline-flex;

        align-items: center;

        padding: 0.5rem 1rem;

        border-radius: 20px;

        font-size: 0.75rem;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.5px;

    }

    
    
    .role-admin {

        background: linear-gradient(135deg, #dc3545, #c82333);

        color: white;

    }

    
    
    .role-teacher {

        background: linear-gradient(135deg, #007bff, #0056b3);

        color: white;

    }

    
    
    .role-student {

        background: linear-gradient(135deg, #28a745, #1e7e34);

        color: white;

    }

    
    
    /* Status Badges */

    .status-badges {

        display: flex;

        flex-direction: column;

        gap: 0.5rem;

    }

    
    
    .status-badge {

        display: inline-flex;

        align-items: center;

        padding: 0.375rem 0.75rem;

        border-radius: 15px;

        font-size: 0.7rem;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        width: fit-content;

    }

    
    
    .status-active {

        background: #d4edda;

        color: #155724;

    }

    
    
    .status-inactive {

        background: #e2e3e5;

        color: #383d41;

    }

    
    
    .status-verified {

        background: #d1ecf1;

        color: #0c5460;

    }

    
    
    .status-unverified {

        background: #fff3cd;

        color: #856404;

    }

    
    
    .status-available {

        background: #d4edda;

        color: #155724;

    }

    
    
    .status-unavailable {

        background: #f8d7da;

        color: #721c24;

    }

    
    
    /* Date Info */

    .date-info {

        display: flex;

        flex-direction: column;

    }

    
    
    .date-primary {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.25rem;

    }

    
    
    .date-secondary {

        font-size: 0.875rem;

        color: #6c757d;

    }

    
    
    /* Action Buttons */

    .action-buttons {

        display: flex;

        gap: 0.5rem;

        flex-wrap: wrap;

    }

    
    
    .action-btn {

        border-radius: 8px;

        font-size: 0.875rem;

        font-weight: 500;

        transition: all 0.3s ease;

        padding: 0.5rem;

        width: 36px;

        height: 36px;

        display: flex;

        align-items: center;

        justify-content: center;

    }

    
    
    .action-btn:hover {

        transform: translateY(-1px);

        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    }

    
    
    /* Empty State */

    .empty-state {

        text-align: center;

        padding: 4rem 2rem;

    }

    
    
    .empty-state-icon {

        font-size: 4rem;

        color: #6c757d;

        margin-bottom: 1.5rem;

    }

    
    
    .empty-state-title {

        font-size: 1.5rem;

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 1rem;

    }

    
    
    .empty-state-description {

        color: #6c757d;

        margin-bottom: 2rem;

        font-size: 1.1rem;

    }

    
    
    /* Responsive */

    @media (max-width: 768px) {

        .page-title {

            font-size: 2rem;

        }

        
        
        .filter-grid {

            grid-template-columns: 1fr;

        }

        
        
        .modern-card-body {

            padding: 1rem;

        }

        
        
        .search-input-group {

            flex-direction: column;

        }

        
        
        .search-input {

            padding: 0.75rem 1rem;

        }

        
        
        .search-btn, .clear-btn {

            width: 100%;

            margin-top: 0.5rem;

        }

        
        
        .modern-table {

            font-size: 0.875rem;

        }

        
        
        .modern-table th,

        .modern-table td {

            padding: 0.75rem 0.5rem;

        }

        
        
        .user-info {

            flex-direction: column;

            align-items: flex-start;

            gap: 0.5rem;

        }

        
        
        .action-buttons {

            flex-direction: column;

        }

        
        
        .status-badges {

            gap: 0.25rem;

        }

    }

    /* Auto-login button styling */
    .auto-login-btn {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
    }

    .auto-login-btn:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.4);
    }

    .auto-login-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
    }

    .auto-login-btn i {
        animation: pulse 2s infinite;
    }

    /* Resend Password button styling */
    .resend-password-btn {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
    }

    .resend-password-btn:hover {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    }

    .resend-password-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

</style>

@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Auto-submit form when filter values change

    const filterForm = document.getElementById('filterForm');

    const filterSelects = filterForm.querySelectorAll('select');

    
    
    filterSelects.forEach(select => {

        select.addEventListener('change', function() {

            // Add a small delay to allow multiple selections to be made

            setTimeout(() => {

                filterForm.submit();

            }, 100);

        });

    });

    
    
    // Handle Enter key in search input

    const searchInput = document.getElementById('search');

    if (searchInput) {

        searchInput.addEventListener('keypress', function(e) {

            if (e.key === 'Enter') {

                e.preventDefault();

                filterForm.submit();

            }

        });

    }

    
    
    // Show active filters

    const activeFilters = [];

    const urlParams = new URLSearchParams(window.location.search);

    
    
    if (urlParams.get('role')) {

        activeFilters.push(`Role: ${urlParams.get('role')}`);

    }

    if (urlParams.get('status')) {

        activeFilters.push(`Status: ${urlParams.get('status')}`);

    }

    if (urlParams.get('teacher_verification')) {

        activeFilters.push(`Verification: ${urlParams.get('teacher_verification')}`);

    }

    if (urlParams.get('teacher_availability')) {

        activeFilters.push(`Availability: ${urlParams.get('teacher_availability')}`);

    }

    if (urlParams.get('search')) {

        activeFilters.push(`Search: "${urlParams.get('search')}"`);

    }

    
    
    // Display active filters if any

    if (activeFilters.length > 0) {

        const filterInfo = document.createElement('div');

        filterInfo.className = 'alert alert-info mb-3';

        filterInfo.innerHTML = `

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <strong>Active Filters:</strong> ${activeFilters.join(', ')}

                </div>

                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">

                    <i class="fas fa-times me-1"></i>Clear All

                </a>

            </div>

        `;

        
        
        const cardBody = document.querySelector('.card-body');

        if (cardBody) {

            cardBody.insertBefore(filterInfo, cardBody.firstChild);

        }

    }

});

// Global message display function
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enhanced delete confirmation function
function showDeleteConfirmation(itemName, itemType, deleteUrl, warnings = [], data = {}) {
    // Update modal content
    document.getElementById('deleteItemName').textContent = itemName;
    document.getElementById('deleteItemType').textContent = itemType;
    
    // Show warnings if any
    const warningsDiv = document.getElementById('deleteWarnings');
    if (warnings.length > 0) {
        warningsDiv.innerHTML = '<div class="alert alert-danger"><strong>Related Records:</strong><ul>' + 
            warnings.map(warning => `<li>${warning}</li>`).join('') + 
            '</ul></div>';
    } else {
        warningsDiv.innerHTML = '';
    }
    
    // Reset checkboxes
    document.getElementById('confirmDeleteCheckbox').checked = false;
    document.getElementById('forceDeleteCheckbox').checked = false;
    document.getElementById('confirmDeleteBtn').disabled = true;
    
    // Store current delete data
    window.currentDeleteUrl = deleteUrl;
    window.currentDeleteData = data;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    modal.show();
}

// Enhanced restore confirmation function
function showRestoreConfirmation(itemName, itemType, restoreUrl, deletedAt, data = {}) {
    // Update modal content
    document.getElementById('restoreItemName').textContent = itemName;
    document.getElementById('restoreItemType').textContent = itemType;
    document.getElementById('restoreDeletedAt').textContent = deletedAt;
    
    // Store current restore data
    window.currentRestoreUrl = restoreUrl;
    window.currentRestoreData = data;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('restoreConfirmationModal'));
    modal.show();
}

</script>

<!-- Include Delete Confirmation Modal -->
@include('components.delete-confirmation-modal')

@endsection

