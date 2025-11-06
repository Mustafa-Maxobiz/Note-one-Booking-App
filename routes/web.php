<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SessionRecordingController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Teacher\TeacherBookingController;
use App\Http\Controllers\Student\StudentBookingController;
use App\Http\Controllers\ZoomWebhookController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WordPressAuthController;

// Test route for storage
Route::get('/test-storage', function() {
    return 'Storage route is working!';
});

// Zoom iframe routes
Route::get('/zoom/iframe', [\App\Http\Controllers\ZoomIframeController::class, 'iframePage'])->name('zoom.iframe');
Route::post('/zoom/test-embedding', [\App\Http\Controllers\ZoomIframeController::class, 'testEmbedding'])->name('zoom.test-embedding');
Route::post('/zoom/metadata', [\App\Http\Controllers\ZoomIframeController::class, 'getMetadata'])->name('zoom.metadata');
Route::post('/zoom/embed-code', [\App\Http\Controllers\ZoomIframeController::class, 'generateEmbedCode'])->name('zoom.embed-code');

// Storage file serving route (using different path to avoid Apache conflicts)
Route::get('/files/{path}', [StorageController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve');

// JavaScript file serving route
Route::get('js/{filename}', [\App\Http\Controllers\AssetController::class, 'serveJs'])
    ->where('filename', '.*')
    ->name('js.serve');

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ WordPress OAuth2 Routes
Route::get('/login/wordpress', [WordPressAuthController::class, 'redirect'])->name('login.wordpress');
Route::get('/callback', [WordPressAuthController::class, 'callback'])->name('oauth.wordpress.callback');
Route::post('/login/wordpress/rest', [WordPressAuthController::class, 'restApiAuth'])->name('login.wordpress.rest');
Route::get('/oauth/test/wordpress', [WordPressAuthController::class, 'testConnection'])->name('oauth.test.wordpress');

// Aliases for easier usage
Route::get('/auth/wordpress', [WordPressAuthController::class, 'redirect'])->name('auth.wordpress');
Route::get('/wordpress/login', [WordPressAuthController::class, 'redirect'])->name('wordpress.login');

// Endpoint to verify OAuth setup
Route::get('/oauth/status', function () {
    return response()->json([
        'status' => 'active',
        'wordpress_oauth' => [
            'enabled' => !empty(env('WP_OAUTH_CLIENT_ID')),
            'server_url' => env('WP_OAUTH_SERVER'),
            'redirect_uri' => env('WP_OAUTH_REDIRECT_URI'),
            'routes' => [
                'login' => route('login.wordpress'),
                'callback' => route('oauth.wordpress.callback'),
                'test' => route('oauth.test.wordpress'),
            ]
        ],
        'timestamp' => now()->toISOString()
    ]);
})->name('oauth.status');

// Debug endpoint to check current user role (requires authentication)
Route::middleware(['auth'])->get('/debug/user-role', function () {
    $user = auth()->user();
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'name' => $user->name,
        'role' => $user->role,
        'wordpress_id' => $user->wordpress_id,
        'is_active' => $user->is_active,
        'has_teacher_profile' => $user->teacher ? true : false,
        'has_student_profile' => $user->student ? true : false,
        'is_admin' => $user->isAdmin(),
        'is_teacher' => $user->isTeacher(),
        'is_student' => $user->isStudent(),
    ]);
})->name('debug.user-role');

// Public Package Routes (for student registration)
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Return to admin route (accessible to any authenticated user with admin session)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/return-to-admin', [AdminUser::class, 'returnToAdmin'])->name('admin.users.return-to-admin');
});

// Theme CSS route (public)
Route::get('/theme-custom.css', function () {
    return \App\Services\ThemeService::getThemeCSSFile();
})->name('theme.css');

// Meeting start/join routes with notifications
Route::get('/meeting/start/{booking}', [\App\Http\Controllers\MeetingController::class, 'startMeeting'])->name('meeting.start');
Route::get('/meeting/join/{booking}', [\App\Http\Controllers\MeetingController::class, 'joinMeeting'])->name('meeting.join');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Custom user routes (must be before resource route)
    Route::post('/users/delete', [AdminUser::class, 'delete'])->name('users.delete');
    Route::post('/users/restore', [AdminUser::class, 'restore'])->name('users.restore');
    Route::get('/users/trashed', [AdminUser::class, 'trashed'])->name('users.trashed');
    
    Route::resource('users', AdminUser::class);
    Route::patch('/users/{user}/toggle-verification', [AdminUser::class, 'toggleVerification'])->name('users.toggle-verification');
    Route::patch('/users/{user}/toggle-availability', [AdminUser::class, 'toggleAvailability'])->name('users.toggle-availability');
    Route::post('/users/{user}/auto-login', [AdminUser::class, 'autoLogin'])->name('users.auto-login');
    Route::post('/users/{user}/resend-password', [AdminUser::class, 'resendPasswordEmail'])->name('users.resend-password');
    
    // Custom booking routes (must be before resource route)
    Route::post('/bookings/delete', [AdminBooking::class, 'delete'])->name('bookings.delete');
    Route::post('/bookings/restore', [AdminBooking::class, 'restore'])->name('bookings.restore');
    Route::get('/bookings/trashed', [AdminBooking::class, 'trashed'])->name('bookings.trashed');
    Route::get('/bookings/time-slots', [AdminBooking::class, 'getAvailableTimeSlots'])->name('bookings.time-slots');
    
    Route::resource('bookings', AdminBooking::class);
    Route::post('/bookings/{booking}/reassign', [AdminBooking::class, 'reassign'])->name('bookings.reassign');
    Route::post('/bookings/bulk-actions', [AdminBooking::class, 'bulkActions'])->name('bookings.bulk-actions');
    
    // Handle GET requests to action routes by redirecting to booking details
    Route::get('/bookings/{booking}/reassign', function($booking) {
        return redirect()->route('admin.bookings.show', $booking)
            ->with('info', 'Please use the Reassign button on the booking details page.');
    });
    Route::post('/bookings/cleanup-orphaned', [AdminBooking::class, 'cleanupOrphaned'])->name('bookings.cleanup-orphaned');
    Route::get('/settings', [AdminSettings::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettings::class, 'update'])->name('settings.update');
    Route::post('/settings/test-zoom', [AdminSettings::class, 'testZoomConnection'])->name('settings.test-zoom');
    Route::post('/settings/test-wordpress', [AdminSettings::class, 'testWordPressConnection'])->name('settings.test-wordpress');
    Route::post('/settings/test-pusher', [AdminSettings::class, 'testPusherConnection'])->name('settings.test-pusher');
    Route::post('/settings/execute-command', [AdminSettings::class, 'executeCommand'])->name('settings.execute-command');
    
    // Admin Commands
    Route::get('/commands', [\App\Http\Controllers\Admin\CommandsController::class, 'index'])->name('commands.index');
    
    // Admin Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Reports & Analytics Routes
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [\App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/teachers', [\App\Http\Controllers\Admin\ReportController::class, 'teachers'])->name('reports.teachers');
    Route::get('/reports/students', [\App\Http\Controllers\Admin\ReportController::class, 'students'])->name('reports.students');
    Route::get('/reports/bookings', [\App\Http\Controllers\Admin\ReportController::class, 'bookings'])->name('reports.bookings');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    Route::post('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export.post');
    Route::post('/reports/clear-cache', [\App\Http\Controllers\Admin\ReportController::class, 'clearCache'])->name('reports.clear-cache');
    
    // Export functionality
    Route::get('/export', [\App\Http\Controllers\Admin\ExportController::class, 'index'])->name('export.index');
    Route::get('/export/bookings', [\App\Http\Controllers\Admin\ExportController::class, 'exportBookings'])->name('export.bookings');
    Route::get('/export/users', [\App\Http\Controllers\Admin\ExportController::class, 'exportUsers'])->name('export.users');
    Route::get('/export/payments', [\App\Http\Controllers\Admin\ExportController::class, 'exportPayments'])->name('export.payments');
    Route::get('/export/feedback', [\App\Http\Controllers\Admin\ExportController::class, 'exportFeedback'])->name('export.feedback');
    
    // Search functionality
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search.index');
    Route::post('/search', [\App\Http\Controllers\Admin\SearchController::class, 'search'])->name('search');
    Route::post('/search/advanced', [\App\Http\Controllers\Admin\SearchController::class, 'advancedSearch'])->name('search.advanced');
    
    // Email settings
    Route::get('/email-settings', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('email-settings.index');
    Route::post('/email-settings', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])->name('email-settings.update');
    Route::post('/email-settings/test', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'testEmail'])->name('email-settings.test');
    Route::get('/email-settings/templates', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'templates'])->name('email-settings.templates');
    Route::post('/email-settings/templates', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'updateEmailTemplate'])->name('email-settings.templates.update');
    Route::get('/email-settings/templates/data', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'getTemplateData'])->name('email-settings.templates.data');
    Route::post('/email-settings/templates/save', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'saveTemplate'])->name('email-settings.templates.save');
    Route::post('/email-settings/templates/toggle-status', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'toggleTemplateStatus'])->name('email-settings.templates.toggle-status');
    Route::get('/email-settings/logs', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'emailLogs'])->name('email-settings.logs');
    Route::post('/email-settings/clear-cache', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'clearEmailCache'])->name('email-settings.clear-cache');
    
    // Session Recordings
    Route::resource('session-recordings', \App\Http\Controllers\Admin\SessionRecordingController::class);
    Route::post('/session-recordings/bulk-delete', [\App\Http\Controllers\Admin\SessionRecordingController::class, 'bulkDelete'])->name('session-recordings.bulk-delete');
    Route::post('/zoom/fetch-recordings', [\App\Http\Controllers\Admin\SessionRecordingController::class, 'fetchRecordings'])->name('zoom.fetch-recordings');
    Route::get('/session-recordings/meeting/{meetingId}/files', [\App\Http\Controllers\Admin\SessionRecordingController::class, 'getMeetingFiles'])->name('session-recordings.meeting-files');
    Route::get('/session-recordings/files/{meetingId}', [\App\Http\Controllers\Admin\SessionRecordingController::class, 'getMeetingFiles'])->name('session-recordings.files');
});

// Feedback Routes (for all users)
Route::middleware(['auth'])->group(function () {
    Route::resource('feedback', \App\Http\Controllers\FeedbackController::class);
    Route::get('/bookings/{booking}/feedback/create', [\App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create.booking');
    Route::post('/bookings/{booking}/feedback', [\App\Http\Controllers\FeedbackController::class, 'storeForBooking'])->name('feedback.store.booking');
    
    // Handle GET requests to feedback route by redirecting to booking details
    Route::get('/bookings/{booking}/feedback', function($booking) {
        return redirect()->route('feedback.create.booking', $booking)
            ->with('info', 'Please use the Feedback form on the booking details page.');
    });
});

// Lesson Notes Routes (for all users)
Route::middleware(['auth'])->group(function () {
    Route::resource('lesson-notes', \App\Http\Controllers\LessonNoteController::class);
    Route::get('/students/{student}/lesson-notes', [\App\Http\Controllers\LessonNoteController::class, 'getForStudent'])->name('lesson-notes.student');
    Route::post('/lesson-notes/{lessonNote}/remove-attachment', [\App\Http\Controllers\LessonNoteController::class, 'removeAttachment'])->name('lesson-notes.remove-attachment');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    Route::resource('bookings', \App\Http\Controllers\Teacher\BookingController::class);
    Route::post('/bookings/{booking}/accept', [\App\Http\Controllers\Teacher\BookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking}/decline', [\App\Http\Controllers\Teacher\BookingController::class, 'decline'])->name('bookings.decline');
    Route::post('/bookings/{booking}/complete', [\App\Http\Controllers\Teacher\BookingController::class, 'complete'])->name('bookings.complete');
    
    // Handle GET requests to action routes by redirecting to booking details
    Route::get('/bookings/{booking}/accept', function($booking) {
        return redirect()->route('teacher.bookings.show', $booking)
            ->with('info', 'Please use the Accept button on the booking details page.');
    });
    Route::get('/bookings/{booking}/decline', function($booking) {
        return redirect()->route('teacher.bookings.show', $booking)
            ->with('info', 'Please use the Decline button on the booking details page.');
    });
    Route::get('/bookings/{booking}/complete', function($booking) {
        return redirect()->route('teacher.bookings.show', $booking)
            ->with('info', 'Please use the Complete button on the booking details page.');
    });
    Route::post('/bookings/bulk-actions', [\App\Http\Controllers\Teacher\BookingController::class, 'bulkActions'])->name('bookings.bulk-actions');
    Route::resource('availability', \App\Http\Controllers\Teacher\AvailabilityController::class)->except(['show', 'edit', 'update']);
    Route::get('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/status-check', [\App\Http\Controllers\Teacher\ProfileController::class, 'statusCheck'])->name('profile.status-check');
    
    // Session Recordings
    Route::get('/session-recordings', [\App\Http\Controllers\Teacher\SessionRecordingController::class, 'index'])->name('session-recordings.index');
    Route::get('/session-recordings/{recording}', [\App\Http\Controllers\Teacher\SessionRecordingController::class, 'show'])->name('session-recordings.show');
    Route::get('/bookings/{session}/recordings', [\App\Http\Controllers\Teacher\SessionRecordingController::class, 'bySession'])->name('session-recordings.by-session');
    
    // Notification routes are handled in the generic auth group below
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    
    // Booking System
    Route::resource('bookings', \App\Http\Controllers\Student\BookingController::class);
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Student\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/bulk-actions', [\App\Http\Controllers\Student\BookingController::class, 'bulkActions'])->name('bookings.bulk-actions');
    
    // Handle GET requests to action routes by redirecting to booking details
    Route::get('/bookings/{booking}/cancel', function($booking) {
        return redirect()->route('student.bookings.show', $booking)
            ->with('info', 'Please use the Cancel button on the booking details page.');
    });
    
    // New Calendar-based Booking (DEFAULT)
    Route::get('/book-new', [\App\Http\Controllers\Student\BookingController::class, 'calendar'])->name('booking.create');
    Route::get('/book-calendar', [\App\Http\Controllers\Student\BookingController::class, 'calendar'])->name('booking.calendar');
    Route::get('/book-new/availability', [\App\Http\Controllers\Student\BookingController::class, 'getTeacherAvailability'])->name('booking.availability');
    Route::get('/book-new/time-slots', [\App\Http\Controllers\Student\BookingController::class, 'getAvailableTimeSlots'])->name('booking.time-slots');
    Route::post('/book-new', [\App\Http\Controllers\Student\BookingController::class, 'store'])->name('booking.store');
    
    // Legacy search-based booking (alternative)
    Route::get('/book-search', [\App\Http\Controllers\Student\BookingController::class, 'createSearch'])->name('booking.search');
    Route::post('/book-search', [\App\Http\Controllers\Student\BookingController::class, 'search'])->name('booking.search.post');
    
    // Redirect old booking routes
    Route::redirect('/booking', '/book-new');
    Route::redirect('/booking/search', '/book-new');
    
    // Student Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Session Recordings
    Route::get('/session-recordings', [\App\Http\Controllers\Student\SessionRecordingController::class, 'index'])->name('session-recordings.index');
    Route::get('/session-recordings/{recording}', [\App\Http\Controllers\Student\SessionRecordingController::class, 'show'])->name('session-recordings.show');
    Route::get('/bookings/{session}/recordings', [\App\Http\Controllers\Student\SessionRecordingController::class, 'bySession'])->name('session-recordings.by-session');
    Route::get('/recordings/{recording}/download', [\App\Http\Controllers\Student\RecordingController::class, 'download'])->name('recordings.download');
    Route::get('/recordings/{recording}/play', [\App\Http\Controllers\Student\RecordingController::class, 'play'])->name('recordings.play');
    
    // Subscription Routes
    Route::resource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    
    // Notification routes are handled in the generic auth group below
});

// Generic Notification Routes (for authenticated users) - Essential routes for navigation and JavaScript
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
    Route::get('/notifications/pusher-config', [\App\Http\Controllers\NotificationController::class, 'getPusherConfig'])->name('notifications.pusherConfig');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Zoom WebSocket Routes (No authentication required)
Route::post('/websocket/zoom/event', [\App\Http\Controllers\ZoomWebSocketController::class, 'handleEvent'])->name('websocket.zoom.event');
Route::get('/websocket/zoom/status', [\App\Http\Controllers\ZoomWebSocketController::class, 'getStatus'])->name('websocket.zoom.status');
Route::post('/websocket/zoom/test', [\App\Http\Controllers\ZoomWebSocketController::class, 'testEvent'])->name('websocket.zoom.test');

// Zoom Webhook Routes (No authentication required) - Keep for backup
Route::get('/webhook/zoom/meeting-ended', function(\Illuminate\Http\Request $request) {
    // Handle Zoom webhook validation
    $challenge = $request->query('challenge');
    if ($challenge) {
        return response($challenge, 200)->header('Content-Type', 'text/plain');
    }
    return response('Webhook endpoint is accessible', 200)->header('Content-Type', 'text/plain');
})->name('webhook.zoom.validate');

Route::post('/webhook/zoom/meeting-ended', [\App\Http\Controllers\ZoomWebhookController::class, 'handleMeetingEnded'])->name('webhook.zoom.meeting');
Route::post('/webhook/zoom/recording-completed', [\App\Http\Controllers\ZoomWebhookController::class, 'handleRecordingCompleted'])->name('webhook.zoom.recording');

