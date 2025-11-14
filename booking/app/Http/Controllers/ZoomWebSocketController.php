<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZoomWebSocketService;
use Illuminate\Support\Facades\Log;

class ZoomWebSocketController extends Controller
{
    private $websocketService;

    public function __construct()
    {
        $this->websocketService = new ZoomWebSocketService();
    }

    /**
     * Handle incoming Zoom WebSocket events
     */
    public function handleEvent(Request $request)
    {
        try {
            Log::info('Zoom WebSocket event received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            $eventData = $request->all();
            
            if (empty($eventData)) {
                Log::warning('Empty event data received');
                return response()->json(['status' => 'error', 'message' => 'Empty event data'], 400);
            }

            // Process the event
            $result = $this->websocketService->processEvent($eventData);
            
            if ($result) {
                $this->websocketService->updateLastActivity();
                
                Log::info('Zoom WebSocket event processed successfully', [
                    'event' => $eventData['event'] ?? 'unknown'
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Event processed successfully'
                ], 200);
            } else {
                Log::error('Failed to process Zoom WebSocket event', [
                    'event' => $eventData
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process event'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error handling Zoom WebSocket event', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get WebSocket status
     */
    public function getStatus()
    {
        try {
            $status = $this->websocketService->getStatus();
            
            return response()->json([
                'status' => 'success',
                'data' => $status
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error getting WebSocket status', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get status'
            ], 500);
        }
    }

    /**
     * Test WebSocket event processing
     */
    public function testEvent(Request $request)
    {
        try {
            $testEvent = [
                'event' => 'meeting.ended',
                'object' => [
                    'id' => 'test_meeting_' . time(),
                    'uuid' => 'test-uuid-' . time(),
                    'end_time' => now()->toISOString()
                ]
            ];

            Log::info('Testing Zoom WebSocket event processing', [
                'test_event' => $testEvent
            ]);

            $result = $this->websocketService->processEvent($testEvent);
            
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Test event processed successfully',
                    'test_event' => $testEvent
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Test event processing failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error testing WebSocket event', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
