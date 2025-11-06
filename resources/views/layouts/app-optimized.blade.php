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
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ route('theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-optimized.css') }}">
    
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
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('admin.dashboard') }}" 
                                   title="Admin Dashboard" 
                                   aria-label="Go to Admin Dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.users.index') }}" 
                                   title="User Management" 
                                   aria-label="Go to User Management">
                                    <i class="fas fa-users me-2"></i> 
                                    <span class="nav-text">Users</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.bookings.index') }}" 
                                   title="Booking Management" 
                                   aria-label="Go to Booking Management">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span class="nav-text">Bookings</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.session-recordings.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.session-recordings.index') }}" 
                                   title="Session Recordings" 
                                   aria-label="Go to Session Recordings">
                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Recordings</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" 
                                   href="{{ route('lesson-notes.index') }}" 
                                   title="Lesson Notes" 
                                   aria-label="Go to Lesson Notes">
                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Notes</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.*') && !request()->routeIs('admin.reports.bookings') ? 'active' : '' }}" 
                                   href="{{ route('admin.reports.index') }}" 
                                   title="Reports & Analytics" 
                                   aria-label="Go to Reports & Analytics">
                                    <i class="fas fa-chart-bar me-2"></i> 
                                    <span class="nav-text">Reports & Analytics</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.bookings') ? 'active' : '' }}" 
                                   href="{{ route('admin.reports.bookings') }}" 
                                   title="Booking Reports" 
                                   aria-label="Go to Booking Reports">
                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">Booking Reports</span>
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" 
                                   href="#" 
                                   id="adminToolsDropdown" 
                                   role="button" 
                                   data-bs-toggle="dropdown" 
                                   title="Admin Tools" 
                                   aria-label="Open Admin Tools Menu">
                                    <i class="fas fa-tools me-2"></i> 
                                    <span class="nav-text">Tools</span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.export.index') }}" 
                                           title="Export Data" 
                                           aria-label="Go to Export Data">
                                            <i class="fas fa-download me-2"></i> 
                                            <span class="nav-text">Export Data</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.search.index') }}" 
                                           title="Search" 
                                           aria-label="Go to Search">
                                            <i class="fas fa-search me-2"></i> 
                                            <span class="nav-text">Search</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.email-settings.index') }}" 
                                           title="Email Settings" 
                                           aria-label="Go to Email Settings">
                                            <i class="fas fa-envelope me-2"></i> 
                                            <span class="nav-text">Email Settings</span>
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    <li>
                                        <a class="dropdown-item" 
                                           href="{{ route('admin.settings.index') }}" 
                                           title="Settings" 
                                           aria-label="Go to Settings">
                                            <i class="fas fa-cog me-2"></i> 
                                            <span class="nav-text">Settings</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        @elseif(auth()->user()->isTeacher())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('teacher.dashboard') }}" 
                                   title="Teacher Dashboard" 
                                   aria-label="Go to Teacher Dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.bookings.*') ? 'active' : '' }}" 
                                   href="{{ route('teacher.bookings.index') }}" 
                                   title="My Bookings" 
                                   aria-label="Go to My Bookings">
                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">My Bookings</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.availability.*') ? 'active' : '' }}" 
                                   href="{{ route('teacher.availability.index') }}" 
                                   title="My Availability" 
                                   aria-label="Go to My Availability">
                                    <i class="fas fa-clock me-2"></i> 
                                    <span class="nav-text">My Availability</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teacher.session-recordings.*') ? 'active' : '' }}" 
                                   href="{{ route('teacher.session-recordings.index') }}" 
                                   title="Session Recordings" 
                                   aria-label="Go to Session Recordings">
                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Session Recordings</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" 
                                   href="{{ route('lesson-notes.index') }}" 
                                   title="Lesson Notes" 
                                   aria-label="Go to Lesson Notes">
                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Notes</span>
                                </a>
                            </li>

                            @if(auth()->user()->role !== 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" 
                                   href="{{ route('notifications.index') }}">
                                    <i class="fas fa-bell me-2"></i> 
                                    <span class="nav-text">Notifications</span>
                                    <span class="badge bg-danger ms-1" id="notification-badge" style="display: none;">0</span>
                                </a>
                            </li>
                            @endif

                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('student.dashboard') }}" 
                                   title="Student Dashboard" 
                                   aria-label="Go to Student Dashboard">
                                    <i class="fas fa-tachometer-alt me-2"></i> 
                                    <span class="nav-text">Dashboard</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.booking.*') ? 'active' : '' }}" 
                                   href="{{ route('student.booking.calendar') }}" 
                                   title="Book a Lesson" 
                                   aria-label="Go to Book a Lesson">
                                    <i class="fas fa-calendar-plus me-2"></i> 
                                    <span class="nav-text">Book a Lesson</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.bookings.*') ? 'active' : '' }}" 
                                   href="{{ route('student.bookings.index') }}" 
                                   title="My Lessons" 
                                   aria-label="Go to My Lessons">
                                    <i class="fas fa-calendar-alt me-2"></i> 
                                    <span class="nav-text">My Lessons</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.session-recordings.*') ? 'active' : '' }}" 
                                   href="{{ route('student.session-recordings.index') }}" 
                                   title="Session Recordings" 
                                   aria-label="Go to Session Recordings">
                                    <i class="fas fa-video me-2"></i> 
                                    <span class="nav-text">Session Recordings</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lesson-notes.*') ? 'active' : '' }}" 
                                   href="{{ route('lesson-notes.index', ['student_id' => auth()->user()->student->id]) }}" 
                                   title="Lesson Log" 
                                   aria-label="Go to Lesson Log">
                                    <i class="fas fa-book me-2"></i> 
                                    <span class="nav-text">Lesson Log</span>
                                </a>
                            </li>

                            @if(auth()->user()->role !== 'admin')
                            <li class="nav-item d-none">
                                <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" 
                                   href="{{ route('notifications.index') }}">
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
                                <button type="submit" 
                                        class="nav-link border-0 bg-transparent w-100 text-start" 
                                        title="Logout" 
                                        aria-label="Logout from the system">
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
                        <button class="m-1 btn btn-outline-light d-none d-md-inline-flex align-items-center" 
                                type="button" 
                                id="sidebar-toggle" 
                                title="Toggle Sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            @if(auth()->user()->role !== 'admin')
                            <!-- Notifications -->
                            <div class="nav-item dropdown me-3">
                                <a class="nav-link position-relative" 
                                   href="#" 
                                   role="button" 
                                   data-bs-toggle="dropdown" 
                                   id="notificationDropdown">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                          id="notificationCount" 
                                          style="display: none;">0</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" 
                                    style="width: 350px; max-height: 400px; overflow-y: auto;">
                                    <li class="dropdown-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Notifications</span>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="markAllAsRead()" 
                                                    id="markAllAsReadBtn">Mark all as read</button>
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

                            <!-- Return to Admin Button -->
                            @if(session('admin_user_id'))
                            <div class="nav-item me-3">
                                <a href="{{ route('admin.users.return-to-admin') }}" 
                                   class="btn btn-warning btn-sm" 
                                   title="Return to Admin Account">
                                    <i class="fas fa-arrow-left me-1"></i> 
                                    <span class="nav-text">Return to Admin</span>
                                </a>
                            </div>
                            @endif

                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" 
                                   href="#" 
                                   role="button" 
                                   data-bs-toggle="dropdown">
                                    <img src="{{ auth()->user()->small_profile_picture_url }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    <span>{{ auth()->user()->name }}</span>
                                </a>

                                <ul class="dropdown-menu">
                                    @if(auth()->user()->isAdmin())
                                        <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}" title="Settings" aria-label="Go to Settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
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

    <!-- External JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/app-optimized.js') }}"></script>

    @yield('scripts')
</body>
</html>
