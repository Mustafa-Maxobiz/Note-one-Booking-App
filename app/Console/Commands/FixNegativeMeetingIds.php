<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class FixNegativeMeetingIds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:fix-negative-meeting-ids {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Fix negative Zoom meeting IDs by extracting correct IDs from join URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Find bookings with negative meeting IDs
        $bookings = Booking::where('zoom_meeting_id', 'like', '-%')->get();

        if ($bookings->isEmpty()) {
            $this->info('✅ No bookings with negative meeting IDs found');
            return 0;
        }

        $this->info("Found {$bookings->count()} booking(s) with negative meeting IDs:");

        $fixedCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            $this->line("📅 Booking ID: {$booking->id}");
            $this->line("   Current Meeting ID: {$booking->zoom_meeting_id}");
            $this->line("   Join URL: " . ($booking->zoom_join_url ? 'Yes' : 'No'));
            
            if (!$booking->zoom_join_url) {
                $this->error("   ❌ No join URL available - cannot fix");
                $errorCount++;
                continue;
            }

            // Extract correct ID from join URL
            if (preg_match('/\/j\/(\d+)/', $booking->zoom_join_url, $matches)) {
                $correctId = $matches[1];
                $this->line("   Correct ID from URL: {$correctId}");
                
                if (!$isDryRun) {
                    $booking->update(['zoom_meeting_id' => $correctId]);
                    $fixedCount++;
                    $this->line("   ✅ Fixed: Updated to {$correctId}");
                    
                    Log::info('Fixed negative meeting ID', [
                        'booking_id' => $booking->id,
                        'old_id' => $booking->zoom_meeting_id,
                        'new_id' => $correctId
                    ]);
                } else {
                    $this->line("   🔍 Would fix: Update to {$correctId}");
                }
            } else {
                $this->error("   ❌ Could not extract ID from join URL");
                $errorCount++;
            }
            
            $this->line("");
        }

        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETE - {$bookings->count()} booking(s) would be fixed");
        } else {
            $this->info("✅ COMPLETE - Fixed {$fixedCount} booking(s), {$errorCount} error(s)");
        }

        return 0;
    }
}