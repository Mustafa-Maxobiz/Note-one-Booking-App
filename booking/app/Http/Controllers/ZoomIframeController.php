<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZoomIframeService;
use Illuminate\Support\Facades\Log;

class ZoomIframeController extends Controller
{
    /**
     * Test if Zoom recording can be embedded in iframe
     */
    public function testEmbedding(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'passcode' => 'nullable|string'
        ]);

        try {
            $url = $request->input('url');
            $passcode = $request->input('passcode');
            
            $result = ZoomIframeService::testIframeEmbedding($url, $passcode);
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Zoom iframe test failed', [
                'url' => $request->input('url'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to test iframe embedding: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recording metadata for iframe embedding
     */
    public function getMetadata(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'passcode' => 'nullable|string'
        ]);

        try {
            $url = $request->input('url');
            $passcode = $request->input('passcode');
            
            $metadata = ZoomIframeService::getRecordingMetadata($url, $passcode);
            
            return response()->json([
                'success' => true,
                'data' => $metadata
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get recording metadata', [
                'url' => $request->input('url'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recording metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate embed code for Zoom recording
     */
    public function generateEmbedCode(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'passcode' => 'nullable|string',
            'width' => 'nullable|integer|min:100|max:1920',
            'height' => 'nullable|integer|min:100|max:1080'
        ]);

        try {
            $url = $request->input('url');
            $passcode = $request->input('passcode');
            $width = $request->input('width', 800);
            $height = $request->input('height', 600);
            
            $embedCode = ZoomIframeService::generateEmbedCode($url, $passcode, $width, $height);
            $iframeUrl = ZoomIframeService::generateIframeUrl($url, $passcode);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'embed_code' => $embedCode,
                    'iframe_url' => $iframeUrl,
                    'width' => $width,
                    'height' => $height
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate embed code', [
                'url' => $request->input('url'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate embed code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve iframe-friendly Zoom recording page
     */
    public function iframePage(Request $request)
    {
        $url = $request->input('url');
        $passcode = $request->input('passcode');
        
        if (!$url) {
            return response()->view('errors.404', [], 404);
        }

        $iframeUrl = ZoomIframeService::generateIframeUrl($url, $passcode);
        $metadata = ZoomIframeService::getRecordingMetadata($url, $passcode);
        
        return view('zoom.iframe-player', [
            'iframeUrl' => $iframeUrl,
            'originalUrl' => $url,
            'passcode' => $passcode,
            'metadata' => $metadata
        ]);
    }
}
