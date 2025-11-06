<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\ZoomService;

class AuditZoomMeetingIds extends Command
{
    protected $signature = "zoom:audit-ids {--fix : Automatically fix invalid IDs}";
    protected $description = "Audit all Zoom meeting IDs for validity";

    public function handle()
    {
        $this->info("🔍 Auditing Zoom Meeting IDs...");
        
        $bookings = Booking::whereNotNull("zoom_meeting_id")->get();
        $invalid = 0;
        $fixed = 0;
        
        foreach($bookings as $booking) {
            $zoomId = $booking->zoom_meeting_id;
            
            // Basic validation
            if (!is_numeric($zoomId) || $zoomId < 0 || strlen($zoomId) < 8) {
                $this->warn("❌ Booking {$booking->id}: Invalid ID format ({$zoomId})");
                $invalid++;
                
                if ($this->option("fix")) {
                    $booking->update(["zoom_meeting_id" => null]);
                    $this->info("  ✅ Fixed: Set to NULL");
                    $fixed++;
                }
            } else {
                // Test against Zoom API
                $zoomService = new ZoomService();
                $recordings = $zoomService->getMeetingRecordings($zoomId);
                
                if ($recordings === null || (isset($recordings["code"]) && $recordings["code"] == 3301)) {
                    $this->warn("❌ Booking {$booking->id}: ID {$zoomId} not found on Zoom");
                    $invalid++;
                    
                    if ($this->option("fix")) {
                        $booking->update(["zoom_meeting_id" => null]);
                        $this->info("  ✅ Fixed: Set to NULL");
                        $fixed++;
                    }
                } else {
                    $this->info("✅ Booking {$booking->id}: ID {$zoomId} is valid");
                }
            }
        }
        
        $this->info("📊 Audit complete: {$invalid} invalid IDs found");
        if ($this->option("fix")) {
            $this->info("🔧 Fixed {$fixed} invalid IDs");
        } else {
            $this->info("💡 Run with --fix to automatically fix invalid IDs");
        }
    }
}