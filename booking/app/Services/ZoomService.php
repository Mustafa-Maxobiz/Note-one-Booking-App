<?php



namespace App\Services;



use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;



class ZoomService

{

    protected $apiKey;

    protected $apiSecret;

    protected $accountId;

    protected $baseUrl = 'https://api.zoom.us/v2';

    protected $tokenCacheKey = 'zoom_access_token';

    protected $tokenExpiry = 3600; // 1 hour



    public function __construct()

    {

        // Get Zoom settings from database

        $this->apiKey = \App\Models\SystemSetting::getValue('zoom_api_key');

        $this->apiSecret = \App\Models\SystemSetting::getValue('zoom_api_secret');

        $this->accountId = \App\Models\SystemSetting::getValue('zoom_account_id');

    }



    /**

     * Check if Zoom credentials are properly configured

     */

    public function isConfigured(): bool

    {

        return !empty($this->apiKey) && !empty($this->apiSecret) && !empty($this->accountId);

    }



    /**

     * Create a new Zoom meeting

     */

     public function createMeeting($topic, $startTime, $duration = 60, $options = [])
     {
         if (!$this->isConfigured()) {
             Log::error('Zoom credentials not configured');
             return null;
         }
     
         try {
             // Default meeting settings
             $defaultSettings = [
                 'host_video'        => true,
                 'participant_video' => true,
                 'join_before_host'  => (bool) \App\Models\SystemSetting::getValue('zoom_join_before_host', '1'),
                 'mute_upon_entry'   => (bool) \App\Models\SystemSetting::getValue('zoom_mute_upon_entry', '0'),
                 'watermark'         => false,
                 'use_pmi'           => false,
                 'approval_type'     => 0,
                 'audio'             => 'both',
                 'auto_recording'    => 'cloud', // ✅ Force cloud recording
                 'cloud_recording'   => true,
                 'waiting_room'      => (bool) \App\Models\SystemSetting::getValue('zoom_waiting_room', '0'),
             ];
     
             // Merge DB/override options (but never allow "none" to slip in silently)
             $settings = array_merge($defaultSettings, $options);
     
             if (empty($settings['auto_recording']) || $settings['auto_recording'] === 'none') {
                 $settings['auto_recording'] = 'cloud';
             }
     
             $meetingData = [
                 'topic'      => $topic,
                 'type'       => 2, // scheduled meeting
                 'start_time' => $this->formatDateTime($startTime),
                 'duration'   => $duration,
                 'timezone'   => config('app.timezone', 'UTC'),
                 'settings'   => $settings
             ];
     
             $response = Http::timeout(30)->withHeaders([
                 'Authorization' => 'Bearer ' . $this->getAccessToken(),
                 'Content-Type'  => 'application/json',
             ])->post($this->baseUrl . '/users/me/meetings', $meetingData);
     
             if ($response->successful()) {
                 $meeting = $response->json();
                // Log::info('Created meeting response', $meeting);

                // ALWAYS extract meeting ID from join URL for consistency
                $meeting = $this->extractCorrectMeetingId($meeting);
     
                Log::info('Zoom meeting created successfully', [
                    'meeting_id'     => $meeting['id'] ?? null,
                    'topic'          => $topic,
                    'auto_recording' => $meeting['settings']['auto_recording'] ?? 'not_returned',
                    'meeting'        => $meeting
                ]);
     
                 return $meeting;
             }
     
             // Special handling for missing scopes
             if ($response->status() === 400) {
                 $errorBody = $response->json();
                 if (isset($errorBody['code']) && $errorBody['code'] === 4711) {
                     Log::warning('Zoom API scope error - creating mock meeting instead', [
                         'error' => $errorBody['message'],
                         'topic' => $topic
                     ]);
     
                     return $this->createMockMeeting($topic, $startTime, $duration);
                 }
             }
     
             $this->logApiError('Create Meeting', $response);
             return null;
         } catch (\Exception $e) {
             Log::error('Zoom Service Error: ' . $e->getMessage());
             return null;
         }
     }
     


    /**

     * Get meeting recordings

     */

    public function getMeetingRecordings($meetingId)

    {

        if (!$this->isConfigured()) {

            return null;

        }



        try {

            $response = Http::timeout(30)->withHeaders([

                'Authorization' => 'Bearer ' . $this->getAccessToken(),

                'Content-Type' => 'application/json',

            ])->get($this->baseUrl . "/meetings/{$meetingId}/recordings");



            if ($response->successful()) {

                return $response->json();

            }



            $this->logApiError('Get Recordings', $response);

            return null;

        } catch (\Exception $e) {

            Log::error('Zoom Recording Error: ' . $e->getMessage());

            return null;

        }

    }

    /**
     * Extract correct meeting ID from join URL to prevent negative IDs
     */
    private function extractCorrectMeetingId($meeting)
    {
        if (!isset($meeting['join_url'])) {
            Log::warning('Zoom meeting response missing join_url', ['meeting' => $meeting]);
            return $meeting;
        }

        // Extract meeting ID from join URL using regex
        if (preg_match('/\/j\/(\d+)/', $meeting['join_url'], $matches)) {
            $correctMeetingId = $matches[1];
            
            // Always use the ID from URL, even if API returned a different one
            if (isset($meeting['id']) && $meeting['id'] != $correctMeetingId) {
                Log::info('Zoom meeting ID corrected from join URL', [
                    'original_id' => $meeting['id'],
                    'corrected_id' => $correctMeetingId,
                    'join_url' => $meeting['join_url']
                ]);
            }
            
            $meeting['id'] = $correctMeetingId;
            
            // Validate that the ID is positive
            if ($correctMeetingId < 0) {
                Log::error('Extracted meeting ID is negative', [
                    'meeting_id' => $correctMeetingId,
                    'join_url' => $meeting['join_url']
                ]);
            }
        } else {
            Log::error('Could not extract meeting ID from join URL', [
                'join_url' => $meeting['join_url']
            ]);
        }

        return $meeting;
    }

    /**

     * Delete a Zoom meeting

     */

    public function deleteMeeting($meetingId)

    {

        if (!$this->isConfigured()) {

            return false;

        }



        try {

            $response = Http::timeout(30)->withHeaders([

                'Authorization' => 'Bearer ' . $this->getAccessToken(),

            ])->delete($this->baseUrl . "/meetings/{$meetingId}");



            if ($response->successful()) {

                Log::info('Zoom meeting deleted successfully', ['meeting_id' => $meetingId]);

                return true;

            }

            // Check for scope errors
            if ($response->status() === 400) {
                $errorBody = $response->json();
                if (isset($errorBody['code']) && $errorBody['code'] === 4711) {
                    Log::warning('Zoom meeting deletion failed - insufficient scopes', [
                        'meeting_id' => $meetingId,
                        'error' => $errorBody['message'],
                        'solution' => 'Add meeting:delete:meeting scope to Zoom app'
                    ]);
                    return false;
                }
            }

            $this->logApiError('Delete Meeting', $response);

            return false;

        } catch (\Exception $e) {

            Log::error('Zoom Delete Error: ' . $e->getMessage());

            return false;

        }

    }



    /**

     * Get access token with caching

     */

    public function getAccessToken()

    {

        if (!$this->isConfigured()) {

            Log::error('Zoom credentials not configured');

            return null;

        }



        // Try to get cached token first

        $cachedToken = Cache::get($this->tokenCacheKey);

        if ($cachedToken) {

            return $cachedToken;

        }



        // Try OAuth first (for Server-to-Server OAuth apps)

        $oauthToken = $this->getOAuthToken();

        if ($oauthToken) {

            Log::info('Using OAuth token for Zoom API');

            Cache::put($this->tokenCacheKey, $oauthToken, $this->tokenExpiry);

            return $oauthToken;

        }

        

        // If OAuth fails, try JWT (for JWT apps)

        Log::info('OAuth failed, trying JWT token');

        $jwtToken = $this->generateJWTToken();

        if ($jwtToken) {

            Log::info('Using JWT token for Zoom API');

            Cache::put($this->tokenCacheKey, $jwtToken, $this->tokenExpiry);

            return $jwtToken;

        }

        

        Log::error('Both OAuth and JWT authentication failed');

        return null;

    }



    protected function generateJWTToken()

    {

        try {

            $header = [

                'alg' => 'HS256',

                'typ' => 'JWT'

            ];

            

            $payload = [

                'iss' => $this->apiKey, // Client ID as issuer

                'exp' => time() + 3600, // 1 hour expiration

            ];



            return $this->generateJWT($header, $payload, $this->apiSecret);

        } catch (\Exception $e) {

            Log::error('JWT token generation failed: ' . $e->getMessage());

            return null;

        }

    }



    protected function getOAuthToken()

    {

        try {

            Log::info('Attempting OAuth token request using account_credentials', [

                'api_key' => $this->apiKey,

                'account_id' => $this->accountId,

                'oauth_url' => 'https://zoom.us/oauth/token'

            ]);

            

            // Use the exact approach from the documentation

            $token = base64_encode($this->apiKey . ':' . $this->apiSecret);

            $url = "https://zoom.us/oauth/token?grant_type=account_credentials&account_id=" . $this->accountId;

            

            $response = Http::withHeaders([

                'Authorization' => 'Basic ' . $token,

                'Content-Type' => 'application/x-www-form-urlencoded',

            ])->post($url);



            Log::info('OAuth response received', [

                'status' => $response->status(),

                'successful' => $response->successful(),

                'body_preview' => substr($response->body(), 0, 200)

            ]);



            if ($response->successful()) {

                $data = $response->json();

                $accessToken = $data['access_token'] ?? null;

                

                if ($accessToken) {

                    Log::info('OAuth token obtained successfully', [

                        'token_length' => strlen($accessToken),

                        'token_preview' => substr($accessToken, 0, 20) . '...'

                    ]);

                    return $accessToken;

                } else {

                    Log::error('OAuth response missing access_token', [

                        'response_data' => $data

                    ]);

                }

            } else {

                Log::error('OAuth token request failed', [

                    'status' => $response->status(),

                    'body' => $response->body(),

                    'headers' => $response->headers()

                ]);

            }

            

            return null;

        } catch (\Exception $e) {

            Log::error('OAuth token error: ' . $e->getMessage(), [

                'trace' => $e->getTraceAsString()

            ]);

            return null;

        }

    }



    // Alternative method for testing - using direct API key as token

    protected function getAccessTokenDirect()

    {

        // For some Zoom API endpoints, you might need to use the API key directly

        return $this->apiKey;

    }



    protected function generateJWT($header, $payload, $secret)

    {

        $headerEncoded = $this->base64UrlEncode(json_encode($header));

        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $secret, true);

        $signatureEncoded = $this->base64UrlEncode($signature);

        

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;

    }



    protected function base64UrlEncode($data)

    {

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));

    }



    public function getMeetingDetails($meetingId)

    {

        try {

            $response = Http::withHeaders([

                'Authorization' => 'Bearer ' . $this->getAccessToken(),

                'Content-Type' => 'application/json',

            ])->get($this->baseUrl . "/meetings/{$meetingId}");



            if ($response->successful()) {

                return $response->json();

            }



            Log::error('Zoom API Error: ' . $response->body());

            return null;

        } catch (\Exception $e) {

            Log::error('Zoom Service Error: ' . $e->getMessage());

            return null;

        }

    }



    public function getUserInfo()

    {

        try {

            $token = $this->getAccessToken();

            Log::info('Generated JWT token for Zoom API', [

                'token_length' => strlen($token),

                'api_key' => $this->apiKey,

                'account_id' => $this->accountId

            ]);

            

            $response = Http::withHeaders([

                'Authorization' => 'Bearer ' . $token,

                'Content-Type' => 'application/json',

            ])->get($this->baseUrl . '/users/me');



            if ($response->successful()) {

                return $response->json();

            }



            Log::error('Zoom API Error', [

                'status' => $response->status(),

                'body' => $response->body(),

                'headers' => $response->headers(),

                'token_preview' => substr($token, 0, 50) . '...'

            ]);

            

            // Try alternative authentication method if JWT fails

            if ($response->status() === 401) {

                Log::info('Trying alternative authentication method...');

                return $this->getUserInfoAlternative();

            }

            

            return null;

        } catch (\Exception $e) {

            Log::error('Zoom Service Error: ' . $e->getMessage(), [

                'trace' => $e->getTraceAsString()

            ]);

            return null;

        }

    }



    protected function getUserInfoAlternative()

    {

        try {

            // Try using the API key directly as a fallback

            $response = Http::withHeaders([

                'Authorization' => 'Bearer ' . $this->apiKey,

                'Content-Type' => 'application/json',

            ])->get($this->baseUrl . '/users/me');



            if ($response->successful()) {

                Log::info('Alternative authentication method successful');

                return $response->json();

            }



            Log::error('Alternative authentication also failed', [

                'status' => $response->status(),

                'body' => $response->body()

            ]);

            

            return null;

        } catch (\Exception $e) {

            Log::error('Alternative authentication error: ' . $e->getMessage());

            return null;

        }

    }



    public function testConnection()

    {

        try {

            $token = $this->getAccessToken();

            $tokenType = $this->detectTokenType($token);

            

            return [

                'success' => true,

                'token_generated' => !empty($token),

                'token_length' => strlen($token),

                'token_type' => $tokenType,

                'credentials_configured' => !empty($this->apiKey) && !empty($this->apiSecret) && !empty($this->accountId),

                'api_key' => $this->apiKey,

                'account_id' => $this->accountId

            ];

        } catch (\Exception $e) {

            return [

                'success' => false,

                'error' => $e->getMessage()

            ];

        }

    }



    protected function detectTokenType($token)

    {

        if (empty($token)) {

            return 'none';

        }

        

        // Check if it's a JWT token (has 3 parts separated by dots)

        $parts = explode('.', $token);

        if (count($parts) === 3) {

            return 'jwt';

        }

        

        // Check if it's an OAuth token (usually longer and different format)

        if (strlen($token) > 100) {

            return 'oauth';

        }

        

        return 'unknown';

    }



    /**

     * Format datetime for Zoom API

     */

    protected function formatDateTime($datetime)

    {

        if (is_string($datetime)) {

            $datetime = new \DateTime($datetime);

        }

        // Convert to UTC for Zoom API
        $datetime->setTimezone(new \DateTimeZone('UTC'));

        return $datetime->format('Y-m-d\TH:i:s\Z');

    }



    /**

     * Log API errors consistently

     */

    protected function logApiError($operation, $response)

    {

        Log::error("Zoom API Error - {$operation}", [

            'status' => $response->status(),

            'body' => $response->body(),

            'headers' => $response->headers()

        ]);

    }



    /**

     * Update meeting details

     */

    public function updateMeeting($meetingId, $data)

    {

        if (!$this->isConfigured()) {

            return false;

        }



        try {

            $response = Http::timeout(30)->withHeaders([

                'Authorization' => 'Bearer ' . $this->getAccessToken(),

                'Content-Type' => 'application/json',

            ])->patch($this->baseUrl . "/meetings/{$meetingId}", $data);



            if ($response->successful()) {

                Log::info('Zoom meeting updated successfully', ['meeting_id' => $meetingId]);

                return true;

            }



            $this->logApiError('Update Meeting', $response);

            return false;

        } catch (\Exception $e) {

            Log::error('Zoom Update Error: ' . $e->getMessage());

            return false;

        }

    }



    /**

     * Get meeting participants

     */

    public function getMeetingParticipants($meetingId)

    {

        if (!$this->isConfigured()) {

            return null;

        }



        try {

            $response = Http::timeout(30)->withHeaders([

                'Authorization' => 'Bearer ' . $this->getAccessToken(),

                'Content-Type' => 'application/json',

            ])->get($this->baseUrl . "/meetings/{$meetingId}/participants");



            if ($response->successful()) {

                return $response->json();

            }



            $this->logApiError('Get Participants', $response);

            return null;

        } catch (\Exception $e) {

            Log::error('Zoom Participants Error: ' . $e->getMessage());

            return null;

        }

    }



    /**

     * Clear cached token

     */

    public function clearTokenCache()

    {

        Cache::forget($this->tokenCacheKey);

        Log::info('Zoom token cache cleared');

    }



    /**

     * Mock method for development/testing

     */

    public function createMockMeeting($topic, $startTime, $duration = 60)

    {

        return [

            'id' => rand(100000000, 999999999),

            'join_url' => 'https://zoom.us/j/' . rand(100000000, 999999999),

            'start_url' => 'https://zoom.us/s/' . rand(100000000, 999999999),

            'password' => strtoupper(substr(md5(rand()), 0, 6)),

            'topic' => $topic,

            'start_time' => $startTime,

            'duration' => $duration,

            'settings' => [

                'host_video' => true,

                'participant_video' => true,

                'join_before_host' => true,

                'auto_recording' => 'cloud',

                'cloud_recording' => true,

            ]

        ];

    }

    /**
     * Get Zoom scope configuration guidance
     */
    public function getScopeGuidance()
    {
        return [
            'issue' => 'Zoom API access token missing required scopes',
            'required_scopes' => [
                'meeting:write:meeting',
                'meeting:write:meeting:admin'
            ],
            'solution' => [
                '1. Go to Zoom Marketplace (https://marketplace.zoom.us/)',
                '2. Find your app in "Manage" > "Created Apps"',
                '3. Click on your app to edit it',
                '4. Go to "Scopes" tab',
                '5. Add the following scopes:',
                '   - meeting:write:meeting',
                '   - meeting:write:meeting:admin',
                '6. Save the changes',
                '7. The app will need to be re-authorized by users'
            ],
            'alternative' => 'The system will create mock meetings until proper scopes are configured'
        ];
    }

    /**
     * End a Zoom meeting
     */
    public function endMeeting($meetingId)
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->put($this->baseUrl . "/meetings/{$meetingId}/status", [
                'action' => 'end'
            ]);

            if ($response->successful()) {
                Log::info('Zoom meeting ended successfully', ['meeting_id' => $meetingId]);
                return true;
            }

            // Check for scope errors
            if ($response->status() === 400) {
                $errorBody = $response->json();
                if (isset($errorBody['code']) && $errorBody['code'] === 4711) {
                    Log::warning('Zoom meeting ending failed - insufficient scopes', [
                        'meeting_id' => $meetingId,
                        'error' => $errorBody['message'],
                        'solution' => 'Add meeting:update:status scope to Zoom app'
                    ]);
                    return false;
                }
            }

            $this->logApiError('End Meeting', $response);
            return false;

        } catch (\Exception $e) {
            Log::error('Zoom End Meeting Error: ' . $e->getMessage());
            return false;
        }
    }

}

