<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class ExtractPasscodeFromURL extends Command
{
    protected $signature = 'extract:passcode-from-url {meeting_id?}';
    protected $description = 'Extract passcode from Zoom join URLs';

    public function handle()
    {
        $meetingId = $this->argument('meeting_id') ?? '83312278098';
        
        $this->info("🔍 Extracting passcode from Zoom URLs for meeting: {$meetingId}");
        $this->line("");
        
        // Find booking with this meeting ID
        $booking = Booking::where('zoom_meeting_id', $meetingId)->first();
        
        if (!$booking) {
            $this->error("❌ No booking found for meeting ID: {$meetingId}");
            return;
        }
        
        $this->info("✅ Found booking: {$booking->id}");
        $this->line("  - Teacher: {$booking->teacher->user->name}");
        $this->line("  - Student: {$booking->student->user->name}");
        $this->line("");
        
        // Check if there's a stored password
        if ($booking->zoom_password) {
            $this->info("🎉 Stored password: {$booking->zoom_password}");
        } else {
            $this->warn("⚠️  No stored password found");
        }
        
        // Check join URL for passcode
        if ($booking->zoom_join_url) {
            $this->info("📋 Join URL: {$booking->zoom_join_url}");
            
            // Extract passcode from URL
            if (preg_match('/[?&]pwd=([^&]+)/', $booking->zoom_join_url, $matches)) {
                $this->info("🔑 Passcode from URL: {$matches[1]}");
            } else {
                $this->warn("⚠️  No passcode found in join URL");
            }
        } else {
            $this->warn("⚠️  No join URL found");
        }
        
        // Check start URL
        if ($booking->zoom_start_url) {
            $this->line("📋 Start URL: {$booking->zoom_start_url}");
        }
        
        $this->line("");
        $this->info("🧪 Try these passcodes:");
        
        // Try different passcode formats
        $passcodes = [];
        
        // 1. Stored password
        if ($booking->zoom_password) {
            $passcodes[] = $booking->zoom_password;
        }
        
        // 2. Passcode from URL
        if ($booking->zoom_join_url && preg_match('/[?&]pwd=([^&]+)/', $booking->zoom_join_url, $matches)) {
            $passcodes[] = $matches[1];
        }
        
        // 3. Meeting ID itself
        $passcodes[] = $meetingId;
        
        // 4. Common formats
        $passcodes[] = '123456';
        $passcodes[] = '12345678';
        $passcodes[] = '920266';
        $passcodes[] = '476056';
        
        $passcodes = array_unique($passcodes);
        
        foreach ($passcodes as $i => $passcode) {
            $this->line("  " . ($i + 1) . ". {$passcode}");
        }
        
        $this->line("");
        $this->info("💡 The passcode might be:");
        $this->line("  - The stored password (if any)");
        $this->line("  - The 'pwd' parameter from the join URL");
        $this->line("  - The meeting ID itself");
        $this->line("  - A common format like 123456");
    }
}
