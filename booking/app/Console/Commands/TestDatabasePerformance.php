<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestDatabasePerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:test-performance';

    /**
     * The console command description.
     */
    protected $description = 'Test database performance with various queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database performance tests...');

        // Test 1: Simple queries
        $this->testSimpleQueries();

        // Test 2: Complex joins
        $this->testComplexJoins();

        // Test 3: Aggregation queries
        $this->testAggregationQueries();

        // Test 4: Index effectiveness
        $this->testIndexEffectiveness();

        // Test 5: Soft deletes performance
        $this->testSoftDeletesPerformance();

        // Test 6: Audit logging performance
        $this->testAuditLoggingPerformance();

        $this->info('Database performance tests completed!');
    }

    /**
     * Test simple queries performance.
     */
    private function testSimpleQueries()
    {
        $this->info('Testing simple queries...');

        $start = microtime(true);
        
        // Test user queries
        $users = User::where('role', 'teacher')->get();
        $this->line("Found {$users->count()} teachers");

        // Test booking queries
        $bookings = Booking::where('status', 'confirmed')->get();
        $this->line("Found {$bookings->count()} confirmed bookings");

        // Test notification queries
        $notifications = Notification::where('is_read', false)->get();
        $this->line("Found {$notifications->count()} unread notifications");

        $end = microtime(true);
        $this->line("Simple queries took: " . round(($end - $start) * 1000, 2) . "ms");
    }

    /**
     * Test complex joins performance.
     */
    private function testComplexJoins()
    {
        $this->info('Testing complex joins...');

        $start = microtime(true);

        // Test booking with relationships
        $bookingsWithDetails = Booking::with(['teacher.user', 'student.user', 'payments'])
            ->where('start_time', '>=', Carbon::now())
            ->get();

        $this->line("Found {$bookingsWithDetails->count()} future bookings with details");

        // Test user with all relationships
        $usersWithProfiles = User::with(['teacher', 'student', 'notifications'])
            ->where('is_active', true)
            ->get();

        $this->line("Found {$usersWithProfiles->count()} active users with profiles");

        $end = microtime(true);
        $this->line("Complex joins took: " . round(($end - $start) * 1000, 2) . "ms");
    }

    /**
     * Test aggregation queries performance.
     */
    private function testAggregationQueries()
    {
        $this->info('Testing aggregation queries...');

        $start = microtime(true);

        // Test booking statistics
        $bookingStats = DB::table('bookings')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $this->line("Booking status distribution:");
        foreach ($bookingStats as $stat) {
            $this->line("  {$stat->status}: {$stat->count}");
        }

        // Test payment statistics
        $paymentStats = DB::table('payments')
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get();

        $this->line("Payment statistics:");
        foreach ($paymentStats as $stat) {
            $this->line("  {$stat->status}: {$stat->count} payments, Total: $" . number_format($stat->total, 2));
        }

        $end = microtime(true);
        $this->line("Aggregation queries took: " . round(($end - $start) * 1000, 2) . "ms");
    }

    /**
     * Test index effectiveness.
     */
    private function testIndexEffectiveness()
    {
        $this->info('Testing index effectiveness...');

        $start = microtime(true);

        // Test indexed queries
        $bookingsByStatus = Booking::where('status', 'confirmed')->get();
        $this->line("Indexed status query: {$bookingsByStatus->count()} results");

        $bookingsByTime = Booking::where('start_time', '>=', Carbon::now())->get();
        $this->line("Indexed time query: {$bookingsByTime->count()} results");

        $notificationsByUser = Notification::where('user_id', 1)->where('is_read', false)->get();
        $this->line("Indexed user/read query: {$notificationsByUser->count()} results");

        $end = microtime(true);
        $this->line("Indexed queries took: " . round(($end - $start) * 1000, 2) . "ms");
    }

    /**
     * Test soft deletes performance.
     */
    private function testSoftDeletesPerformance()
    {
        $this->info('Testing soft deletes performance...');

        $start = microtime(true);

        // Test soft delete operations
        $user = User::first();
        if ($user) {
            $user->delete(); // Soft delete
            $this->line("Soft deleted user: {$user->name}");

            // Test restoring
            $user->restore();
            $this->line("Restored user: {$user->name}");

            // Test querying with trashed
            $trashedUsers = User::onlyTrashed()->get();
            $this->line("Found {$trashedUsers->count()} trashed users");
        }

        $end = microtime(true);
        $this->line("Soft deletes operations took: " . round(($end - $start) * 1000, 2) . "ms");
    }

    /**
     * Test audit logging performance.
     */
    private function testAuditLoggingPerformance()
    {
        $this->info('Testing audit logging performance...');

        $start = microtime(true);

        // Test audit log queries
        $auditLogs = AuditLog::with('user')->latest()->take(10)->get();
        $this->line("Found {$auditLogs->count()} recent audit logs");

        // Test audit log by event type
        $eventTypes = AuditLog::select('event', DB::raw('COUNT(*) as count'))
            ->groupBy('event')
            ->get();

        $this->line("Audit log event types:");
        foreach ($eventTypes as $type) {
            $this->line("  {$type->event}: {$type->count}");
        }

        $end = microtime(true);
        $this->line("Audit logging queries took: " . round(($end - $start) * 1000, 2) . "ms");
    }
}
