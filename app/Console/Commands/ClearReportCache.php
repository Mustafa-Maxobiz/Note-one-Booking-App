<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearReportCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all report caches to refresh data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing all report caches...');
        
        // Get all possible cache keys for reports
        $cacheKeys = [
            'admin_dashboard_data_' . date('Y-m-d-H'),
            'basic_stats_' . date('Y-m-d-H'),
            'monthly_stats_' . date('Y-m'),
            'weekly_stats_' . date('Y'),
            'daily_stats_' . date('Y-m-d'),
            'top_teachers_' . date('Y-m-d'),
            'teacher_utilization_' . date('Y-m-d'),
            'teacher_acceptance_' . date('Y-m-d'),
            'teacher_response_times_' . date('Y-m-d'),
            'booking_trends_' . date('Y-m-d'),
            'booking_completion_' . date('Y-m-d'),
            'booking_feedback_' . date('Y-m-d'),
        ];

        // Also clear caches for previous days/hours to be thorough
        for ($i = 1; $i <= 7; $i++) {
            $cacheKeys[] = 'admin_dashboard_data_' . date('Y-m-d-H', strtotime("-{$i} hours"));
            $cacheKeys[] = 'basic_stats_' . date('Y-m-d-H', strtotime("-{$i} hours"));
            $cacheKeys[] = 'daily_stats_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'top_teachers_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'teacher_utilization_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'teacher_acceptance_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'teacher_response_times_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'booking_trends_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'booking_completion_' . date('Y-m-d', strtotime("-{$i} days"));
            $cacheKeys[] = 'booking_feedback_' . date('Y-m-d', strtotime("-{$i} days"));
        }

        $clearedCount = 0;
        foreach ($cacheKeys as $key) {
            if (Cache::forget($key)) {
                $clearedCount++;
            }
        }

        // Also clear any cache keys that start with report-related prefixes
        $this->info('Clearing cache keys with report prefixes...');
        
        // This is a more aggressive approach - clear all caches that might be report-related
        $this->call('cache:clear');
        
        $this->info("Successfully cleared {$clearedCount} report cache entries.");
        $this->info('All report data will be refreshed on next access.');
        
        return 0;
    }
}
