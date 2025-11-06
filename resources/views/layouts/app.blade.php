<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-id" content="{{ auth()->id() }}">

    <meta name="user-role" content="{{ auth()->user()->role }}">

    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">

    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">

    <title>@yield('title', 'Online Booking System')</title>

    
    
    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (v5 for 'fas' compatibility) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="{{ route('theme.css') }}">

    <!-- Sora Font -->

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->

    <style>

        :root {

            --brand-black: #000000;

            --brand-red: #ef473e;

            --brand-orange: #fdb838;

            --brand-dark-blue: #070c39;

            --brand-gradient: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

            --brand-gradient-hover: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);

            --bg-primary: #ffffff;

            --bg-secondary: #f8f9fa;

            --text-primary: #000000;

            --text-secondary: #6c757d;

            --text-light: #ffffff;

            --font-family: 'Sora', sans-serif;

            --font-weight-bold: 700;

            --font-weight-semibold: 600;

            --font-weight-medium: 500;

        }

        
        
        body {

            font-family: var(--font-family);

            color: var(--text-primary);

            background-color: var(--bg-primary);

        }

        
        
        .main-content {

            background-color: var(--bg-secondary);

            min-height: 100vh;

            padding: 0;

            margin: 0;

        }

        
        
        .card {

            border: none;

            border-radius: 12px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

        }

        
        
        .navbar-brand {

            font-weight: var(--font-weight-bold);

            color: var(--text-primary);

        }

        
        
        .navbar-brand-custom {

            background: var(--custom-navbar-bg, var(--brand-gradient));

            color: var(--custom-navbar-text, var(--text-light));

            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

        }

        
        
        .navbar-brand-custom .navbar-toggler {

            border-color: rgba(255, 255, 255, 0.3);

        }

        
        
        .navbar-brand-custom .navbar-toggler-icon {

            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");

        }

        
        
        .navbar-brand-custom .nav-link {

            color: var(--custom-navbar-text, rgba(255, 255, 255, 0.9));

            font-weight: var(--font-weight-medium);

            transition: all 0.3s ease;

        }

        
        
        .navbar-brand-custom .nav-link:hover {

            color: var(--custom-navbar-text, var(--text-light));

            background-color: rgba(255, 255, 255, 0.1);

            border-radius: 6px;

        }

        
        
        .navbar-brand-custom .dropdown-menu {

            border: none;

            border-radius: 8px;

            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

            margin-top: 0.5rem;

        }

        
        
        .navbar-brand-custom .dropdown-item {

            color: var(--text-primary);

            font-weight: var(--font-weight-medium);

            transition: all 0.3s ease;

        }

        
        
        .navbar-brand-custom .dropdown-item:hover {

            background-color: rgba(239, 71, 62, 0.1);

            color: var(--brand-red);

        }

        
        
        .navbar-brand-custom .border-bottom {

            border-color: rgba(255, 255, 255, 0.2) !important;

        }

        
        
        .sidebar-brand {

            min-height: 100vh;

            font-family: var(--font-family);

        }
        
        /* Mobile sidebar behavior */
        @media (max-width: 767.98px) {
            .sidebar-brand {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 1050 !important;
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease-in-out !important;
                display: block !important;
                width: 250px !important;
            }
            
            .sidebar-brand.show {
                transform: translateX(0) !important;
            }
            
            /* Mobile sidebar overlay */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Desktop sidebar collapse functionality */
        .sidebar-brand.collapsed {
            flex: 0 0 70px !important;
            max-width: 70px !important;
            width: 70px !important;
            min-width: 70px !important;
            transition: all 0.3s ease;
        }
        
        .sidebar-brand.collapsed .nav-link {
            padding: 0.75rem 0.5rem;
            text-align: center;
            justify-content: center;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand.collapsed .nav-link .nav-text {
            display: none !important;
        }
        
        .sidebar-brand.collapsed .nav-link i {
            font-size: 1.2rem;
            margin-right: 0 !important;
            margin-left: 0 !important;
            width: auto;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar-brand.collapsed .dropdown-toggle::after {
            display: none !important;
        }
        
        .sidebar-brand.collapsed .nav-link .badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* App name styling */
        .app-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .app-name .nav-text {
            margin-left: 0.5rem;
        }
        
        /* Nav text styling */
        .nav-text {
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        /* Hide app name when sidebar is collapsed */
        .sidebar-brand.collapsed .app-name {
            display: none;
        }
        
        /* Show app name when sidebar is expanded */
        .sidebar-brand:not(.collapsed) .app-name {
            display: block;
        }
        
        /* Adjust main content when sidebar is collapsed */
        .main-content.sidebar-collapsed {
            margin-left: 0 !important;
            transition: all 0.3s ease;
        }
        
        /* Ensure proper flex behavior */
        .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        /* Ensure main content takes remaining space */
        .row > .col-md-9 {
            transition: all 0.3s ease;
        }
        
        /* When sidebar is collapsed, adjust main content */
        .sidebar-brand.collapsed ~ .col-md-9 {
            flex: 0 0 calc(100% - 70px) !important;
            max-width: calc(100% - 70px) !important;
            margin-left: 0 !important;
        }
        
        /* Ensure proper spacing */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Fix layout issues */
        .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        .col-md-9, .col-lg-10 {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        /* Ensure main content doesn't have red background */
        .main-content {
            background-color: #f8f9fa !important;
        }
        
        /* Fix any red background issues */
        body {
            background-color: #f8f9fa;
        }
        
        .container-fluid {
            background-color: transparent;
        }
        
        .sidebar-brand .nav-link {

            color: var(--custom-sidebar-text, rgba(255, 255, 255, 0.8));

            padding: 0.75rem 1rem;

            border-radius: 8px;

            margin: 0.25rem 0;

            font-weight: var(--font-weight-medium);

            transition: all 0.3s ease;

            display: flex;

            align-items: center;

            justify-content: flex-start;

        }

        .sidebar-brand .nav-link i {

            width: 20px;

            text-align: center;

            flex-shrink: 0;

            font-size: 1rem;

            line-height: 1;

        }

        .sidebar-brand .nav-link .nav-text {

            margin-left: 0.5rem;

            white-space: nowrap;

        }

        /* Ensure dropdown arrows are properly aligned */

        .sidebar-brand .dropdown-toggle {

            position: relative;

        }

        .sidebar-brand .dropdown-toggle::after {

            position: absolute;

            right: 0.5rem;

            top: 50%;

            transform: translateY(-50%);

        }

        
        
        .sidebar-brand .nav-link:hover,

        .sidebar-brand .nav-link.active {

            color: var(--custom-sidebar-text, var(--text-light));

            background-color: var(--custom-sidebar-hover, rgba(255, 255, 255, 0.15));

            transform: translateX(4px);

        }

        
        
        .btn-brand {

            background: var(--brand-gradient);

            border: none;

            color: var(--text-light);

            font-weight: var(--font-weight-semibold);

            border-radius: 8px;

            padding: 0.75rem 1.5rem;

            transition: all 0.3s ease;

        }

        
        
        .btn-brand:hover {

            background: var(--brand-gradient-hover);

            color: var(--text-light);

            transform: translateY(-2px);

            box-shadow: 0 4px 12px rgba(239, 71, 62, 0.3);

        }

    </style>

    @yield('styles')

</head>

<body>

    <div class="container-fluid">

        <!-- Mobile sidebar overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <div class="row">

            <!-- Sidebar -->

            <nav class="col-md-3 col-lg-2 d-none d-md-block sidebar-brand" id="sidebar-menu">

                <div class="position-sticky pt-3">

                    <div class="text-center mb-4">
                        <!-- 📚 Get Title form settings tabe -->
                        @php
                            $title = \App\Models\SystemSetting::getValue('app_name', 'MaxoBiz');
                        @endphp
                        <a href="{{ url('/') }}" class="text-decoration-none">
                        <h4 class="text-white">📚<span class="nav-text app-name">{{ $title }}</span></h4>
                        </a>

                        <p class="text-white-50 small"><span class="nav-text app-name">Welcome, </span>{{ auth()->user()->name }}</p>

                    </div>

                    
                    
                    <ul class="nav flex-column">

                        @if(auth()->user()->isAdmin())

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">

                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">

                                    <i class="fas fa-users me-2"></i> 
                                    <span class="nav-text">Users</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">

                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span class="nav-text">Bookings</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.session-recordings.*') ? 'active' : '' }}" href="{{ route('admin.session-recordings.index') }}">

                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Recordings</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" href="{{ route('lesson-notes.index') }}">

                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Notes</span>

                                </a>

                            </li>

                             <li class="nav-item">

								<a class="nav-link {{ request()->routeIs('admin.reports.*') && !request()->routeIs('admin.reports.bookings') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">

                                    <i class="fas fa-chart-bar me-2"></i> 
                                    <span class="nav-text">Reports & Analytics</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('admin.reports.bookings') ? 'active' : '' }}" href="{{ route('admin.reports.bookings') }}">

                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">Booking Reports</span>

                                </a>

                            </li>

                            <li class="nav-item dropdown">

                                <a class="nav-link dropdown-toggle" href="#" id="adminToolsDropdown" role="button" data-bs-toggle="dropdown">

                                    <i class="fas fa-tools me-2"></i> 
                                    <span class="nav-text">Tools</span>

                                </a>

                                <ul class="dropdown-menu">

                                    <li><a class="dropdown-item" href="{{ route('admin.export.index') }}">

                                        <i class="fas fa-download me-2"></i> 
                                        <span class="nav-text">Export Data</span>

                                    </a></li>

                                    <li><a class="dropdown-item" href="{{ route('admin.search.index') }}">

                                        <i class="fas fa-search me-2"></i> 
                                        <span class="nav-text">Search</span>

                                    </a></li>

                                    <li><a class="dropdown-item" href="{{ route('admin.email-settings.index') }}">

                                        <i class="fas fa-envelope me-2"></i> 
                                        <span class="nav-text">Email Settings</span>

                                    </a></li>

                                    <li><hr class="dropdown-divider"></li>

                                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">

                                        <i class="fas fa-cog me-2"></i> 
                                        <span class="nav-text">Settings</span>

                                    </a></li>

                                </ul>

                            </li>

                        @elseif(auth()->user()->isTeacher())

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">

                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>

                                </a>

                            </li>

                                                                                     <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('teacher.bookings.*') ? 'active' : '' }}" href="{{ route('teacher.bookings.index') }}">

                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">My Bookings</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('teacher.availability.*') ? 'active' : '' }}" href="{{ route('teacher.availability.index') }}">

                                        <i class="fas fa-clock me-2"></i> 
                                    <span class="nav-text">My Availability</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('teacher.session-recordings.*') ? 'active' : '' }}" href="{{ route('teacher.session-recordings.index') }}">

                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Session Recordings</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" href="{{ route('lesson-notes.index') }}">

                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Notes</span>

                                </a>

                            </li>

                            <li class="nav-item d-none">

                                <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">

                                    <i class="fas fa-star me-2"></i> 
                                    <span class="nav-text">Feedback & Ratings</span>

                                </a>

                            </li>

                            @if(auth()->user()->role !== 'admin')
                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">

                                    <i class="fas fa-bell me-2"></i> 
                                    <span class="nav-text">Notifications</span>

                                    <span class="badge bg-danger ms-1" id="notification-badge" style="display: none;">0</span>

                                </a>

                            </li>
                            @endif

                        @else

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">

                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>

                                </a>

                            </li>

                            
                            
                                                                                      <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('student.booking.*') ? 'active' : '' }}" href="{{ route('student.booking.calendar') }}">

                                    <i class="fas fa-calendar-plus me-2"></i> 
                                    <span class="nav-text">Book a Lesson</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('student.bookings.*') ? 'active' : '' }}" href="{{ route('student.bookings.index') }}">

                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">My Lessons</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('student.session-recordings.*') ? 'active' : '' }}" href="{{ route('student.session-recordings.index') }}">

                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Session Recordings</span>

                                </a>

                            </li>

                            <li class="nav-item">

                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" href="{{ route('lesson-notes.index', ['student_id' => auth()->user()->student->id]) }}">

                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Log</span>

                                </a>

                            </li>

                            <li class="nav-item d-none">

                                <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">

                                    <i class="fas fa-star me-2"></i> 
                                    <span class="nav-text">Feedback & Ratings</span>

                                </a>

                            </li>

                            @if(auth()->user()->role !== 'admin')
                            <li class="nav-item d-none">

                                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">

                                    <i class="fas fa-bell me-2"></i> 
                                    <span class="nav-text">Notifications</span>

                                    <span class="badge bg-danger ms-1" id="notification-badge" style="display: none;">0</span>

                                </a>

                            </li>
                            @endif

                        @endif

                    </ul>

                    
                    
                    <hr class="text-white-50">

                    
                    
                    <ul class="nav flex-column">

                        <li class="nav-item">

                            <form method="POST" action="{{ route('logout') }}" class="d-inline">

                                @csrf

                                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">

                                    <i class="fas fa-sign-out-alt me-2"></i> 
                                    <span class="nav-text">Logout</span>

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </nav>



            <!-- Main content -->

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">

                <!-- Top navbar -->

                <nav class="navbar navbar-expand-lg navbar-brand-custom border-bottom mb-4">

                    <div class="container-fluid">

                        <!-- Mobile sidebar toggle -->
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- Desktop sidebar collapse toggle -->
                        <button class="m-1 btn btn-outline-light d-none d-md-inline-flex align-items-center" type="button" id="sidebar-toggle" title="Toggle Sidebar">
                            <i class="fas fa-bars"></i>
                        </button>

                        
                        
                                                 <div class="navbar-nav ms-auto">

                             @if(auth()->user()->role !== 'admin')
                             <!-- Notifications -->
                             <div class="nav-item dropdown me-3">
                                 <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                                     <i class="fas fa-bell"></i>
                                     <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none;">
                                         0
                                     </span>
                                 </a>
                                 <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                     <li class="dropdown-header">
                                         <div class="d-flex justify-content-between align-items-center">
                                             <span>Notifications</span>
                                             <button class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()" id="markAllAsReadBtn">Mark all as read</button>
                                         </div>
                                     </li>
                                     <li><hr class="dropdown-divider"></li>
                                     <div id="notificationsList">
                                         <li class="dropdown-item text-center text-muted py-3">
                                             <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                             <div>No notifications</div>
                                         </li>
                                     </div>
                                     <li><hr class="dropdown-divider"></li>
                                     <li class="dropdown-item text-center d-none">
                                         <a href="{{ route('notifications.index') }}" class="text-decoration-none">View all notifications</a>
                                     </li>
                                 </ul>
                             </div>
                             @endif

                             <!-- Return to Admin Button (only show when admin is logged in as another user) -->
                             @if(session('admin_user_id'))
                             <div class="nav-item me-3">
                                 <a href="{{ route('admin.users.return-to-admin') }}" class="btn btn-warning btn-sm" title="Return to Admin Account">
                                     <i class="fas fa-arrow-left me-1"></i> 
                                     <span class="nav-text">Return to Admin</span>
                                 </a>
                             </div>
                             @endif

                             <div class="nav-item dropdown">

                                 <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">

                                     <img src="{{ auth()->user()->small_profile_picture_url }}" 

                                          alt="{{ auth()->user()->name }}" 

                                          class="rounded-circle me-2" 

                                          style="width: 32px; height: 32px; object-fit: cover;">

                                     <span>{{ auth()->user()->name }}</span>

                                 </a>

                                <ul class="dropdown-menu">

                                    @if(auth()->user()->isAdmin())

                                        <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>

                                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>

                                    @elseif(auth()->user()->isTeacher())

                                        <li><a class="dropdown-item" href="{{ route('teacher.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>

                                    @else

                                        <li><a class="dropdown-item" href="{{ route('student.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>

                                    @endif

                                    <li><hr class="dropdown-divider"></li>

                                    <li>

                                        <form method="POST" action="{{ route('logout') }}">

                                            @csrf

                                            <button type="submit" class="dropdown-item">

                                                <i class="fas fa-sign-out-alt me-2"></i> 
                                                <span class="nav-text">Logout</span>

                                            </button>

                                        </form>

                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </nav>



                <!-- Page content -->

                <div class="container-fluid">

                    {{-- Include unified alert messages component --}}
                    @include('components.alert-messages')



                    @yield('content')

                </div>

            </main>

        </div>

    </div>



    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Pusher JS -->

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
        // Warn if Font Awesome isn't loaded (helps debug missing icons)
        document.addEventListener('DOMContentLoaded', function() {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-check';
            testIcon.style.display = 'none';
            document.body.appendChild(testIcon);
            const computed = window.getComputedStyle(testIcon, '::before');
            // If computed content is empty, FA may not have loaded
            if (!computed || computed.getPropertyValue('content') === '' || computed.getPropertyValue('content') === 'none') {
                console.warn('Font Awesome may not be loaded correctly — icons might not display as expected.');
            }
            testIcon.remove();
        });
    </script>

    <!-- Real-time Notifications -->

    <script>
        // Real-time Notifications JavaScript
        let pusher = null;
        let notificationsChannel = null;
        
        // Check if Pusher is configured
        const pusherKey = '{{ config("broadcasting.connections.pusher.key") }}';
        const pusherCluster = '{{ config("broadcasting.connections.pusher.options.cluster") }}';
        
        if (pusherKey && pusherKey !== '' && pusherKey !== 'null') {
            try {
                // Initialize Pusher only if credentials are available
                pusher = new Pusher(pusherKey, {
                    cluster: pusherCluster || 'mt1',
                    encrypted: true,
                    enabledTransports: ['ws', 'wss']
                });

                // Subscribe to notifications channel
                notificationsChannel = pusher.subscribe('notifications');

                // Handle new notifications
                notificationsChannel.bind('new-notification', function(data) {
                    console.log('New notification received:', data);
                    
                    // Show browser notification if permission granted
                    if (Notification.permission === 'granted') {
                        showBrowserNotification(data);
                    }
                    
                    // Update notification count in UI
                    updateNotificationCount();
                    
                    // Show toast notification
                    showToastNotification(data);
                });

                // Handle booking updates
                notificationsChannel.bind('booking-updated', function(data) {
                    console.log('Booking updated:', data);
                    showToastNotification({
                        title: 'Booking Updated',
                        message: data.message || 'A booking has been updated',
                        type: 'info'
                    });
                });

                // Handle new bookings
                notificationsChannel.bind('new-booking', function(data) {
                    console.log('New booking:', data);
                    showToastNotification({
                        title: 'New Booking',
                        message: data.message || 'A new booking has been created',
                        type: 'success'
                    });
                });

                // Handle payment notifications
                notificationsChannel.bind('payment-received', function(data) {
                    console.log('Payment received:', data);
                    showToastNotification({
                        title: 'Payment Received',
                        message: data.message || 'A payment has been received',
                        type: 'success'
                    });
                });

                console.log('Pusher initialized successfully');
            } catch (error) {
                console.warn('Pusher initialization failed:', error);
                console.log('Real-time notifications disabled - Pusher not configured');
            }
        } else {
            console.log('Real-time notifications disabled - Pusher credentials not configured');
        }

        // Show toast notification
        function showToastNotification(data) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${data.type || 'info'}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-icon">
                        <i class="fas fa-${getIconForType(data.type || 'info')}"></i>
                    </div>
                    <div class="toast-body">
                        <div class="toast-title">${data.title || 'Notification'}</div>
                        <div class="toast-message">${data.message || 'You have a new notification'}</div>
                    </div>
                    <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            // Add to page
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        // Get icon for notification type
        function getIconForType(type) {
            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Update notification count in UI
        function updateNotificationCount() {
            const countElement = document.getElementById('notification-count');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent) || 0;
                countElement.textContent = currentCount + 1;
                countElement.style.display = currentCount === 0 ? 'inline' : 'inline';
            }
        }

        // Show browser notification
        function showBrowserNotification(data) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(data.title || 'New Notification', {
                    body: data.message || 'You have a new notification',
                    icon: '/favicon.ico',
                    badge: '/favicon.ico'
                });
                
                // Auto-close after 5 seconds
                setTimeout(() => {
                    notification.close();
                }, 5000);
                
                // Handle click
                notification.onclick = function() {
                    window.focus();
                    if (data.url) {
                        window.location.href = data.url;
                    }
                    notification.close();
                };
            }
        }

        // Request notification permission
        function requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        console.log('Notification permission granted');
                    }
                });
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Request notification permission
            requestNotificationPermission();
            
            // Add toast styles if not already present
            if (!document.getElementById('toast-styles')) {
                const style = document.createElement('style');
                style.id = 'toast-styles';
                style.textContent = `
                    .toast-container {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 9999;
                        max-width: 400px;
                    }
                    
                    .toast-notification {
                        background: white;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        margin-bottom: 10px;
                        overflow: hidden;
                        animation: slideIn 0.3s ease-out;
                    }
                    
                    .toast-content {
                        display: flex;
                        align-items: center;
                        padding: 15px;
                    }
                    
                    .toast-icon {
                        margin-right: 12px;
                        font-size: 20px;
                    }
                    
                    .toast-success .toast-icon {
                        color: #28a745;
                    }
                    
                    .toast-error .toast-icon {
                        color: #dc3545;
                    }
                    
                    .toast-warning .toast-icon {
                        color: #ffc107;
                    }
                    
                    .toast-info .toast-icon {
                        color: #17a2b8;
                    }
                    
                    .toast-body {
                        flex: 1;
                    }
                    
                    .toast-title {
                        font-weight: 600;
                        margin-bottom: 4px;
                        color: #333;
                    }
                    
                    .toast-message {
                        color: #666;
                        font-size: 14px;
                    }
                    
                    .toast-close {
                        background: none;
                        border: none;
                        color: #999;
                        cursor: pointer;
                        padding: 4px;
                        margin-left: 10px;
                    }
                    
                    .toast-close:hover {
                        color: #666;
                    }
                    
                    @keyframes slideIn {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        });
        
        // Desktop and Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const sidebarToggle = document.querySelector('#sidebar-toggle');
            const sidebar = document.querySelector('#sidebar-menu');
            const overlay = document.querySelector('#sidebar-overlay');
            const mainContent = document.querySelector('.main-content');
            
            // Restore sidebar state from localStorage
            function restoreSidebarState() {
                if (window.innerWidth > 767.98) {
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    console.log('Restoring sidebar state - collapsed:', isCollapsed);

                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        if (mainContent) mainContent.classList.add('sidebar-collapsed');
                    } else {
                        if (mainContent) mainContent.classList.remove('sidebar-collapsed');
                    }

                    // Set toggle icon according to state
                    const icon = sidebarToggle?.querySelector('i');
                    if (icon) {
                        icon.className = isCollapsed ? 'fas fa-angle-right' : 'fas fa-bars';
                        console.log('Setting sidebar toggle icon to:', icon.className);
                    }
                }
            }
            
            // Initialize sidebar state
            restoreSidebarState();
            
            // Desktop sidebar toggle functionality
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Only work on desktop
                    if (window.innerWidth > 767.98) {
                        console.log('Toggling sidebar collapse...');
                        sidebar.classList.toggle('collapsed');

                        // Save state to localStorage
                        const isCollapsed = sidebar.classList.contains('collapsed');
                        localStorage.setItem('sidebarCollapsed', isCollapsed);
                        console.log('Sidebar collapsed:', isCollapsed);

                        // Toggle main content class so CSS can adjust layout
                        if (mainContent) mainContent.classList.toggle('sidebar-collapsed');

                        // Update toggle button icon to reflect state
                        const icon = sidebarToggle.querySelector('i');
                        if (icon) {
                            icon.className = isCollapsed ? 'fas fa-angle-right' : 'fas fa-bars';
                            console.log('Setting icon to:', icon.className);
                        }
                    }
                });
            }
            
            // Mobile sidebar toggle functionality
            if (navbarToggler && sidebar) {
                navbarToggler.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (window.innerWidth <= 767.98) {
                        sidebar.classList.toggle('show');
                        if (overlay) {
                            overlay.classList.toggle('show');
                        }
                    }
                });
                
                // Close sidebar when clicking on overlay
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    });
                }
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 767.98) {
                        if (!sidebar.contains(event.target) && 
                            !navbarToggler.contains(event.target) && 
                            !overlay.contains(event.target)) {
                            sidebar.classList.remove('show');
                            if (overlay) {
                                overlay.classList.remove('show');
                            }
                        }
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 767.98) {
                        // Desktop view - remove mobile classes
                        sidebar.classList.remove('show');
                        if (overlay) {
                            overlay.classList.remove('show');
                        }
                        // Restore desktop sidebar state
                        restoreSidebarState();
                    } else {
                        // Mobile view - remove desktop classes
                        sidebar.classList.remove('collapsed');
                    }
                });
                
                // Close sidebar when clicking on nav links
                const navLinks = sidebar.querySelectorAll('.nav-link');
                navLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 767.98) {
                            sidebar.classList.remove('show');
                            if (overlay) {
                                overlay.classList.remove('show');
                            }
                        }
                    });
                });
            }
        });

        // Export functions for global use
        window.RealTimeNotifications = {
            showToast: showToastNotification,
            updateCount: updateNotificationCount,
            requestPermission: requestNotificationPermission
        };
    </script>

    
    
    <!-- Custom Styles -->

    <style>

        /* Brand Pagination Styling */

        .pagination-brand {

            margin-bottom: 0;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

            border-radius: 8px;

            overflow: hidden;

        }

        
        
        .pagination-brand .page-link {

            color: var(--text-primary);

            border: 1px solid #dee2e6;

            padding: 0.75rem 1rem;

            margin: 0;

            line-height: 1.25;

            background-color: var(--bg-primary);

            transition: all 0.3s ease;

            font-weight: var(--font-weight-medium);

        }

        
        
        .pagination-brand .page-link:hover {

            color: var(--brand-red);

            text-decoration: none;

            background-color: rgba(239, 71, 62, 0.1);

            border-color: var(--brand-red);

            transform: translateY(-1px);

        }

        
        
        .pagination-brand .page-item.active .page-link {

            background: var(--brand-gradient);

            border-color: var(--brand-red);

            color: var(--text-light);

            font-weight: var(--font-weight-semibold);

        }

        
        
        .pagination-brand .page-item.disabled .page-link {

            color: #adb5bd;

            pointer-events: none;

            background-color: var(--bg-secondary);

        }

        
        
        .pagination-brand .page-item:first-child .page-link {

            border-top-left-radius: 8px;

            border-bottom-left-radius: 8px;

        }

        
        
        .pagination-brand .page-item:last-child .page-link {

            border-top-right-radius: 8px;

            border-bottom-right-radius: 8px;

        }

        
        
        /* Pagination Container */

        .pagination-container {

            display: flex;

            flex-direction: column;

            align-items: center;

            gap: 1rem;

            margin-top: 2rem;

            margin-bottom: 1rem;

            padding: 1rem;

            background: var(--bg-secondary);

            border-radius: 12px;

            border: 1px solid #e9ecef;

        }

        
        
        .pagination-info {

            color: var(--text-secondary);

            font-size: 0.875rem;

            font-weight: var(--font-weight-medium);

        }

        
        
        .pagination-info strong {

            color: var(--text-primary);

        }

        
        
        /* Responsive pagination */

        @media (max-width: 576px) {

            .pagination-container {

                padding: 0.75rem;

            }

            
            
            .pagination-brand .page-link {

                padding: 0.5rem 0.75rem;

                font-size: 0.875rem;

            }

            
            
            .pagination-info {

                font-size: 0.8rem;

            }

        }

        /* Notification Styles */
        .notification-dropdown {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
        }

        .notification-dropdown .dropdown-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .notification-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }

        .notification-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .notification-dropdown .dropdown-header {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        #notificationCount {
            font-size: 0.75rem;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #495057;
        }

        .notification-message {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #adb5bd;
        }

    </style>

    
    
    @yield('scripts')

    <!-- Notification System JavaScript -->
    <script>
        // Initialize notification system (only for non-admin users)
        document.addEventListener('DOMContentLoaded', function() {
            @if(auth()->user()->role !== 'admin')
            initializeNotifications();
            @endif
        });

        function initializeNotifications() {
            // Load initial notifications
            loadNotifications();
            
            // Set up real-time notifications if Pusher is available
            if (typeof Pusher !== 'undefined') {
                setupPusherNotifications();
            }
            
            // Poll for new notifications every 30 seconds
            setInterval(loadNotifications, 30000);
        }

        function loadNotifications() {
            // Only load notifications if user is authenticated
            @if(auth()->check())
            // Load notification count
            fetch('{{ route("notifications.unreadCount") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                updateNotificationCount(data.count || 0);
                loadNotificationDropdown();
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                // Don't show notification count if there's an error
                updateNotificationCount(0);
            });
            @else
            // User not authenticated, hide notification count
            updateNotificationCount(0);
            @endif
        }

        function loadNotificationDropdown() {
            @if(auth()->check())
            fetch('{{ route("notifications.recent") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notificationsList');
                if (data.notifications && data.notifications.length > 0) {
                    let notificationsHtml = '';
                    data.notifications.forEach(notification => {
                        notificationsHtml += `
                            <li class="dropdown-item ${notification.is_read ? '' : 'bg-light'}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">${notification.title}</div>
                                        <div class="text-muted small">${notification.message}</div>
                                        <div class="text-muted small">${notification.created_at}</div>
                                    </div>
                                    ${!notification.is_read ? `<button class="btn btn-sm btn-outline-success ms-2" onclick="markAsRead(${notification.id})" title="Mark as Read"><i class="fas fa-check"></i></button>` : ''}
                                </div>
                            </li>
                        `;
                    });
                    notificationsList.innerHTML = notificationsHtml;
                } else {
                    notificationsList.innerHTML = `
                        <li class="dropdown-item text-center text-muted py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <div>No notifications</div>
                        </li>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notification dropdown:', error);
                const notificationsList = document.getElementById('notificationsList');
                notificationsList.innerHTML = `
                    <li class="dropdown-item text-center text-muted py-3">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <div>Error loading notifications</div>
                    </li>
                `;
            });
            @endif
        }

        function updateNotificationCount(count) {
            const countBadge = document.getElementById('notificationCount');
            if (count > 0) {
                countBadge.textContent = count;
                countBadge.style.display = 'inline-block';
            } else {
                countBadge.style.display = 'none';
            }
        }

        function setupPusherNotifications() {
            // Only setup Pusher if user is authenticated
            @if(auth()->check())
            // Get Pusher settings from server
            fetch('{{ route("notifications.pusherConfig") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(config => {
                if (config.key && config.cluster) {
                    const pusher = new Pusher(config.key, {
                        cluster: config.cluster,
                        encrypted: true
                    });

                    const channel = pusher.subscribe('user-' + {{ auth()->id() }});
                    
                    channel.bind('notification', function(data) {
                        // Show browser notification
                        if (Notification.permission === 'granted') {
                            new Notification(data.title, {
                                body: data.message,
                                icon: '/favicon.ico'
                            });
                        }
                        
                        // Update notification count
                        loadNotifications();
                    });
                }
            })
            .catch(error => {
                console.error('Error setting up Pusher:', error);
                // Continue without Pusher if there's an error
            });
            @else
            console.log('User not authenticated, skipping Pusher setup');
            @endif
        }

        function markAsRead(notificationId) {
            fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Reload notifications to update the dropdown
                    loadNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Show user-friendly error message
                alert('Failed to mark notification as read. Please try again.');
            });
        }

        function markAllAsRead() {
            console.log('markAllAsRead function called');
            
            // Show loading state
            const btn = document.getElementById('markAllAsReadBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Processing...';
            }
            
            fetch('{{ route("notifications.markAllRead") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    updateNotificationCount(0);
                    loadNotifications();
                    console.log('All notifications marked as read successfully');
                    
                    // Show success message
                    if (btn) {
                        btn.innerHTML = '✓ Done';
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = 'Mark all as read';
                        }, 2000);
                    }
                } else {
                    console.error('Failed to mark all notifications as read:', data);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Mark all as read';
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notifications as read:', error);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Mark all as read';
                }
            });
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Add event listener for mark all as read button
        document.addEventListener('DOMContentLoaded', function() {
            const markAllBtn = document.getElementById('markAllAsReadBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    console.log('Mark all as read button clicked');
                    e.preventDefault();
                    markAllAsRead();
                });
            }
        });
        
        // Handle 419 Page Expired errors globally
        document.addEventListener('DOMContentLoaded', function() {
            // Check for session expired message
            @if(session('expired'))
                showSessionExpiredModal();
            @endif
            
            // Intercept fetch requests to handle 419 errors
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 419) {
                            handleSessionExpired();
                            return Promise.reject(new Error('Session expired'));
                        }
                        return response;
                    })
                    .catch(error => {
                        if (error.message === 'Session expired') {
                            handleSessionExpired();
                        }
                        throw error;
                    });
            };
        });
        
        function handleSessionExpired() {
            showSessionExpiredModal();
        }
        
        function showSessionExpiredModal() {
            // Create modal if it doesn't exist
            let modal = document.getElementById('sessionExpiredModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'sessionExpiredModal';
                modal.className = 'modal fade';
                modal.setAttribute('data-bs-backdrop', 'static');
                modal.setAttribute('data-bs-keyboard', 'false');
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Session Expired
                                </h5>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                </div>
                                <h6>Your session has expired</h6>
                                <p class="text-muted">For security reasons, you need to log in again to continue.</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-primary" onclick="redirectToLogin()">
                                    <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            
            // Show modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
        
        function redirectToLogin() {
            window.location.href = '{{ route("login") }}';
        }
        
        // Global AJAX error handler for jQuery requests
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 419) {
                handleSessionExpired();
            }
        });
    </script>

</body>

</html>

