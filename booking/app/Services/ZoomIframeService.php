<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomIframeService
{
    /**
     * Generate iframe-friendly URL for Zoom recordings
     */
    public static function generateIframeUrl($playUrl, $passcode = null, $options = [])
    {
        if (!$playUrl) {
            return null;
        }

        // Default options
        $defaultOptions = [
            'autoplay' => true,
            'controls' => true,
            'modestbranding' => true,
            'rel' => 0,
            'showinfo' => false,
            'iv_load_policy' => 3,
            'fs' => true,
            'cc_load_policy' => 1,
            'enablejsapi' => true,
            'origin' => request()->getSchemeAndHttpHost()
        ];

        $options = array_merge($defaultOptions, $options);

        // Parse the original URL
        $parsedUrl = parse_url($playUrl);
        $queryParams = [];
        
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }

        // Add passcode if provided
        if ($passcode) {
            $queryParams['passcode'] = $passcode;
        }

        // Add iframe-friendly parameters
        foreach ($options as $key => $value) {
            if (is_bool($value)) {
                $queryParams[$key] = $value ? '1' : '0';
            } else {
                $queryParams[$key] = $value;
            }
        }

        // Reconstruct URL
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
        $newQuery = http_build_query($queryParams);
        
        return $baseUrl . '?' . $newQuery;
    }

    /**
     * Check if URL is a Zoom recording
     */
    public static function isZoomRecording($url)
    {
        if (!$url) {
            return false;
        }

        $zoomPatterns = [
            'zoom.us/rec/play/',
            'zoom.us/rec/share/',
            'zoom.us/rec/',
            'zoom.us/j/',
            'zoom.us/meeting/'
        ];

        foreach ($zoomPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Zoom recording type from URL
     */
    public static function getRecordingType($url)
    {
        if (!$url) {
            return 'unknown';
        }

        if (strpos($url, '/rec/play/') !== false) {
            return 'cloud_recording';
        }
        if (strpos($url, '/rec/share/') !== false) {
            return 'shared_recording';
        }
        if (strpos($url, '/j/') !== false) {
            return 'meeting_join';
        }
        if (strpos($url, '/meeting/') !== false) {
            return 'meeting_link';
        }

        return 'zoom_recording';
    }

    /**
     * Test if recording can be embedded in iframe
     */
    public static function testIframeEmbedding($url, $passcode = null)
    {
        try {
            $iframeUrl = self::generateIframeUrl($url, $passcode);
            
            // Test with HEAD request to check if URL is accessible
            $response = Http::timeout(10)->head($iframeUrl);
            
            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'iframe_url' => $iframeUrl,
                'headers' => $response->headers()
            ];
            
        } catch (\Exception $e) {
            Log::error('Zoom iframe embedding test failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'iframe_url' => self::generateIframeUrl($url, $passcode)
            ];
        }
    }

    /**
     * Generate embed code for Zoom recordings
     */
    public static function generateEmbedCode($url, $passcode = null, $width = 800, $height = 600)
    {
        $iframeUrl = self::generateIframeUrl($url, $passcode);
        
        return sprintf(
            '<iframe src="%s" width="%d" height="%d" frameborder="0" allowfullscreen></iframe>',
            htmlspecialchars($iframeUrl),
            $width,
            $height
        );
    }

    /**
     * Get recording metadata for iframe embedding
     */
    public static function getRecordingMetadata($url, $passcode = null)
    {
        $metadata = [
            'url' => $url,
            'iframe_url' => self::generateIframeUrl($url, $passcode),
            'is_zoom' => self::isZoomRecording($url),
            'recording_type' => self::getRecordingType($url),
            'supports_iframe' => false,
            'requires_passcode' => false,
            'test_result' => null
        ];

        if ($metadata['is_zoom']) {
            // Test iframe embedding
            $testResult = self::testIframeEmbedding($url, $passcode);
            $metadata['test_result'] = $testResult;
            $metadata['supports_iframe'] = $testResult['success'] ?? false;
            
            // Check if passcode is required
            $metadata['requires_passcode'] = !empty($passcode) || strpos($url, 'passcode=') !== false;
        }

        return $metadata;
    }
}
