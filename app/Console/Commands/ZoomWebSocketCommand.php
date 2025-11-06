<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZoomWebSocketService;
use Illuminate\Support\Facades\Log;

class ZoomWebSocketCommand extends Command
{
    protected $signature = 'zoom:websocket {action=status : Action to perform (connect, disconnect, status, test)}';
    protected $description = 'Manage Zoom WebSocket connection for real-time event handling';

    public function handle()
    {
        $action = $this->argument('action');
        $websocketService = new ZoomWebSocketService();

        switch ($action) {
            case 'connect':
                return $this->connect($websocketService);
            
            case 'disconnect':
                return $this->disconnect($websocketService);
            
            case 'status':
                return $this->status($websocketService);
            
            case 'test':
                return $this->test($websocketService);
            
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: connect, disconnect, status, test");
                return 1;
        }
    }

    private function connect($websocketService)
    {
        $this->info('🔌 Connecting to Zoom WebSocket...');
        
        if ($websocketService->connect()) {
            $this->info('✅ Connected to Zoom WebSocket successfully!');
            $this->info('📡 Listening for real-time Zoom events...');
            $this->info('🔄 Meeting ended events will automatically update booking status');
            $this->info('📹 Recording completed events will be processed automatically');
            
            return 0;
        } else {
            $this->error('❌ Failed to connect to Zoom WebSocket');
            $this->error('🔍 Check your subscription ID and network connection');
            
            return 1;
        }
    }

    private function disconnect($websocketService)
    {
        $this->info('🔌 Disconnecting from Zoom WebSocket...');
        
        if ($websocketService->disconnect()) {
            $this->info('✅ Disconnected from Zoom WebSocket successfully!');
            
            return 0;
        } else {
            $this->error('❌ Failed to disconnect from Zoom WebSocket');
            
            return 1;
        }
    }

    private function status($websocketService)
    {
        $this->info('📊 Zoom WebSocket Status');
        $this->info('========================');
        
        $status = $websocketService->getStatus();
        
        $this->info("Connected: " . ($status['connected'] ? '✅ Yes' : '❌ No'));
        $this->info("Subscription ID: {$status['subscription_id']}");
        $this->info("WebSocket URL: {$status['websocket_url']}");
        $this->info("Last Activity: {$status['last_activity']}");
        
        if ($status['connected']) {
            $this->info('');
            $this->info('🎯 Active Features:');
            $this->info('  • Real-time meeting ended detection');
            $this->info('  • Automatic booking status updates');
            $this->info('  • Recording processing notifications');
            $this->info('  • Student and teacher notifications');
        }
        
        return 0;
    }

    private function test($websocketService)
    {
        $this->info('🧪 Testing Zoom WebSocket Event Processing...');
        
        // Simulate a meeting ended event
        $testEvent = [
            'event' => 'meeting.ended',
            'object' => [
                'id' => 'test_meeting_123',
                'uuid' => 'test-uuid-' . time(),
                'end_time' => now()->toISOString()
            ]
        ];
        
        $this->info('📤 Simulating meeting ended event...');
        $this->info('Event: ' . json_encode($testEvent, JSON_PRETTY_PRINT));
        
        if ($websocketService->processEvent($testEvent)) {
            $this->info('✅ Event processing successful!');
            $this->info('🔄 Booking status would be updated automatically');
            $this->info('📧 Notifications would be sent to users');
        } else {
            $this->error('❌ Event processing failed');
            $this->error('🔍 Check logs for more details');
        }
        
        return 0;
    }
}
