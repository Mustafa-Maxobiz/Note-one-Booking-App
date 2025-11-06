<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SystemSetting;
use App\Services\NotificationService;

class WordPressAuthController extends Controller
{
    protected function getProvider()
    {
        $clientId = SystemSetting::getValue('wp_oauth_client_id');
        $clientSecret = SystemSetting::getValue('wp_oauth_client_secret');
        $redirectUri = SystemSetting::getValue('wp_oauth_redirect_uri');
        $serverUrl = SystemSetting::getValue('wp_oauth_server');

        if (!$clientId || !$clientSecret || !$redirectUri || !$serverUrl) {
            throw new \Exception('WordPress OAuth configuration is incomplete');
        }

        return new GenericProvider([
            'clientId'                => $clientId,
            'clientSecret'            => $clientSecret,
            'redirectUri'             => $redirectUri,
            'urlAuthorize'            => $serverUrl . '/oauth/authorize',
            'urlAccessToken'          => $serverUrl . '/oauth/token',
            'urlResourceOwnerDetails' => $serverUrl . '/oauth/me',
        ]);
    }

    public function redirect()
    {
        try {
            // Check if OAuth is enabled
            $enabled = SystemSetting::getValue('wp_oauth_enabled');
            if ($enabled != '1') {
                return redirect()->route('login')->with('error', 'WordPress OAuth login is currently disabled.');
            }

            $provider = $this->getProvider();
            session()->forget('oauth2state');

            $authorizationUrl = $provider->getAuthorizationUrl();
            session(['oauth2state' => $provider->getState()]);

            // Log::info('WordPress OAuth redirect initiated', [
            //     'authorization_url' => $authorizationUrl,
            //     'state' => $provider->getState()
            // ]);

            return redirect($authorizationUrl);
        } catch (\Exception $e) {
            Log::error('WordPress OAuth2 redirect error: ' . $e->getMessage());
            
            // Provide more specific error messages
            if (strpos($e->getMessage(), 'incomplete') !== false) {
                return redirect()->route('login')->with('error', 'WordPress OAuth configuration is incomplete. Please contact administrator.');
            }
            
            return redirect()->route('login')->with('error', 'Failed to initiate WordPress login. Please try again or contact support.');
        }
    }

    public function callback(Request $request)
    {
        try {
            $provider = $this->getProvider();

            //Log::info('OAuth2 Callback Request', $request->all());

            if ($request->has('error')) {
                return redirect()->route('login')->with('error', 'WordPress login failed: ' . $request->get('error'));
            }

            if (!$request->has('code')) {
                return redirect()->route('login')->with('error', 'Authorization code missing from WordPress response.');
            }

            if ($request->state !== session('oauth2state')) {
                session()->forget('oauth2state');
                return redirect()->route('login')->with('error', 'Invalid OAuth state parameter. Please try again.');
            }
            session()->forget('oauth2state');

            // Exchange authorization code for access token
            try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);
            } catch (\Exception $e) {
                Log::error('Token exchange failed: ' . $e->getMessage(), [
                    'code' => $request->get('code'),
                    'state' => $request->get('state')
                ]);
                throw new \Exception('Failed to exchange authorization code for access token: ' . $e->getMessage());
            }

            // Log::info('WordPress OAuth Token Received', [
            //     'token_type' => $token->getToken() ? 'Bearer' : 'Unknown',
            //     'expires' => $token->getExpires(),
            //     'has_refresh_token' => $token->getRefreshToken() ? 'yes' : 'no'
            // ]);

            // Get user information from WordPress
            try {
                $resourceOwner = $provider->getResourceOwner($token);
                $wpUser = $resourceOwner->toArray();
                ////Log::info('WordPress User Data', $wpUser);
            } catch (\Exception $e) {
                Log::error('Failed to get WordPress user data: ' . $e->getMessage());
                
                // Try alternative method to get user data
                try {
                    $wpUser = $this->getUserDataFromToken($token, $provider);
                    //Log::info('Got user data from alternative method', $wpUser);
                } catch (\Exception $e2) {
                    Log::error('Alternative method also failed: ' . $e2->getMessage());
                    throw new \Exception('Unable to retrieve user information from WordPress. Please try the REST API login method instead.');
                }
            }

            $user = $this->findOrCreateUser($wpUser);

            // Log final user role for debugging
            Log::info('User login completed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'final_role' => $user->role,
                'wordpress_id' => $user->wordpress_id,
            ]);

            // Verify user role is valid
            $this->verifyUserRole($user);

            Auth::login($user);

            NotificationService::sendNotification(
                $user->id,
                'welcome',
                'Welcome!',
                'You have successfully logged in with WordPress.'
            );

            return $this->redirectAfterLogin($user);

        } catch (\Exception $e) {
            Log::error('WordPress OAuth2 callback error: ' . $e->getMessage(), [
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            
            // Provide more specific error messages based on the error
            $errorMessage = 'WordPress login failed. Please try again.';
            
            if (strpos($e->getMessage(), 'invalid_request') !== false) {
                $errorMessage = 'WordPress OAuth configuration error. Please contact administrator.';
            } elseif (strpos($e->getMessage(), 'invalid_client') !== false) {
                $errorMessage = 'WordPress OAuth client configuration is invalid.';
            } elseif (strpos($e->getMessage(), 'invalid_grant') !== false) {
                $errorMessage = 'WordPress OAuth authorization code is invalid or expired.';
            } elseif (strpos($e->getMessage(), 'unauthorized_client') !== false) {
                $errorMessage = 'WordPress OAuth client is not authorized.';
            } elseif (strpos($e->getMessage(), 'Unable to retrieve user information') !== false) {
                $errorMessage = 'Unable to retrieve user information from WordPress. Please try the REST API login method instead.';
            }
            
            return redirect()->route('login')->with('error', $errorMessage);
        }
    }

    protected function findOrCreateUser($wpUser)
    {
        // Log the complete WordPress user data for debugging
        //Log::info('Processing WordPress user data', $wpUser);
        
        $email = $wpUser['email'] ?? $wpUser['user_email'] ?? null;
        if (!$email) {
            // Try to construct email from username or use a fallback
            $username = $wpUser['username'] ?? $wpUser['user_login'] ?? $wpUser['slug'] ?? null;
            if ($username) {
                $email = $username . '@wordpress.local';
                //Log::info('Constructed email from username: ' . $email);
            } else {
                throw new \Exception('Email not provided by WordPress and no username available');
            }
        }

        $role = $this->determineUserRole($wpUser);

        Log::info('User role determined from WordPress', [
            'email' => $email,
            'determined_role' => $role,
            'wordpress_roles' => $wpUser['user_roles'] ?? $wpUser['roles'] ?? $wpUser['capabilities'] ?? null,
            'wordpress_data_keys' => array_keys($wpUser),
        ]);

        // Prepare user data with maximum information
        $userData = [
                'name'          => $wpUser['name'] ?? $wpUser['display_name'] ?? 'WordPress User',
                'password'      => bcrypt(Str::random(32)),
                'role'          => $role,
                'wordpress_id'  => $wpUser['ID'] ?? null,
                'is_active'     => true,
                'email_verified_at' => now(),
        ];

        // Add additional fields if available
        if (isset($wpUser['phone'])) {
            $userData['phone'] = $wpUser['phone'];
        }
        if (isset($wpUser['mobile'])) {
            $userData['mobile'] = $wpUser['mobile'];
        }
        if (isset($wpUser['telephone'])) {
            $userData['telephone'] = $wpUser['telephone'];
        }
        if (isset($wpUser['contact_phone'])) {
            $userData['contact_phone'] = $wpUser['contact_phone'];
        }
        if (isset($wpUser['user_phone'])) {
            $userData['user_phone'] = $wpUser['user_phone'];
        }
        if (isset($wpUser['billing_phone'])) {
            $userData['billing_phone'] = $wpUser['billing_phone'];
        }
        if (isset($wpUser['shipping_phone'])) {
            $userData['shipping_phone'] = $wpUser['shipping_phone'];
        }
        if (isset($wpUser['url'])) {
            $userData['website'] = $wpUser['url'];
        }
        if (isset($wpUser['description'])) {
            $userData['bio'] = $wpUser['description'];
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            $userData
        );

        // Always sync role from WordPress on each login
        if (!$user->wasRecentlyCreated) {
            // Existing user - update role if changed in WordPress
            $newRole = $this->determineUserRole($wpUser);
            if ($user->role !== $newRole) {
                Log::info('User role changed in WordPress, updating local role', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'old_role' => $user->role,
                    'new_role' => $newRole
                ]);
                $user->update(['role' => $newRole]);
                
                // Ensure profile exists for new role
                $this->ensureProfileExists($user, $wpUser, $newRole);
            }
        } else {
            // New user - create profile
            $this->createUserProfile($user, $wpUser, $role);
        }

        return $user;
    }

    protected function createUserProfile(User $user, array $wpUser, string $role)
    {
        Log::info('Creating user profile', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $role,
            'wordpress_data' => [
                'ID' => $wpUser['ID'] ?? null,
                'email' => $wpUser['email'] ?? $wpUser['user_email'] ?? null,
                'roles' => $wpUser['roles'] ?? $wpUser['user_roles'] ?? null,
            ]
        ]);
        
        if ($role === 'teacher' && !Teacher::where('user_id', $user->id)->exists()) {
            Teacher::create([
                'user_id' => $user->id,
                'bio'     => $wpUser['description'] ?? '',
            ]);
        } elseif ($role === 'student' && !Student::where('user_id', $user->id)->exists()) {
            Student::create([
                'user_id' => $user->id,
                'grade'   => $wpUser['grade'] ?? null,
            ]);
        }
    }

    /**
     * Ensure user profile exists for their current role
     */
    protected function ensureProfileExists(User $user, array $wpUser, string $role)
    {
        Log::info('Ensuring profile exists for user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'current_role' => $user->role,
            'new_role' => $role,
            'has_teacher_profile' => Teacher::where('user_id', $user->id)->exists(),
            'has_student_profile' => Student::where('user_id', $user->id)->exists(),
        ]);
        
        // Remove profiles for other roles
        if ($role !== 'teacher') {
            Teacher::where('user_id', $user->id)->delete();
        }
        if ($role !== 'student') {
            Student::where('user_id', $user->id)->delete();
        }

        // Create profile for current role if doesn't exist
        if ($role === 'teacher' && !Teacher::where('user_id', $user->id)->exists()) {
            Teacher::create([
                'user_id' => $user->id,
                'bio'     => $wpUser['description'] ?? '',
            ]);
        } elseif ($role === 'student' && !Student::where('user_id', $user->id)->exists()) {
            Student::create([
                'user_id' => $user->id,
                'grade'   => $wpUser['grade'] ?? null,
            ]);
        }
    }

    /**
     * Verify user role is valid
     */
    protected function verifyUserRole(User $user)
    {
        $validRoles = ['admin', 'teacher', 'student'];
        
        if (!in_array($user->role, $validRoles)) {
            Log::warning('Invalid user role detected, resetting to student', [
                'user_id' => $user->id,
                'invalid_role' => $user->role
            ]);
            
            $user->update(['role' => 'student']);
            $user->refresh();
        }
        
        // Verify user is active
        if (!$user->is_active) {
            abort(403, 'Your account has been deactivated. Please contact administrator.');
        }
    }

    protected function redirectAfterLogin(User $user)
    {
        return match ($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }

    public function testConnection()
    {
        try {
            $config = [
                'clientId' => SystemSetting::getValue('wp_oauth_client_id'),
                'clientSecret' => SystemSetting::getValue('wp_oauth_client_secret'),
                'redirectUri' => SystemSetting::getValue('wp_oauth_redirect_uri'),
                'serverUrl' => SystemSetting::getValue('wp_oauth_server'),
            ];

            foreach ($config as $k => $v) {
                if (empty($v)) {
                    return response()->json(['success' => false, 'message' => "Missing: $k"]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'WordPress OAuth2 config is valid',
                'config' => [
                    'server_url' => $config['serverUrl'],
                    'redirect_uri' => $config['redirectUri'],
                    'client_id' => substr($config['clientId'], 0, 10) . '...'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    protected function determineUserRole($wpUser)
    {
        // Debug: Log the WordPress user data
        Log::info('Determining user role from WordPress data', [
            'available_keys' => array_keys($wpUser),
            'user_roles' => $wpUser['user_roles'] ?? 'NOT_FOUND',
            'roles' => $wpUser['roles'] ?? 'NOT_FOUND',
            'capabilities' => $wpUser['capabilities'] ?? 'NOT_FOUND',
            'username' => $wpUser['username'] ?? $wpUser['user_login'] ?? $wpUser['slug'] ?? 'NOT_FOUND',
            'display_name' => $wpUser['display_name'] ?? $wpUser['name'] ?? 'NOT_FOUND',
        ]);
        
        $roles = $wpUser['user_roles'] ?? $wpUser['roles'] ?? $wpUser['capabilities'] ?? [];
        if (!is_array($roles)) {
            Log::info('No roles found, defaulting to student');
            return 'student';
        }

        // Map WP roles → Laravel roles
        // Priority order: Check more specific roles first
        $roleMap = [
            'administrator' => 'admin',
            'tutor_instructor' => 'teacher',  // Tutor LMS instructor role
            'tutor' => 'teacher',              // Tutor LMS role
            'instructor' => 'teacher',        // Generic instructor role
            'teacher' => 'teacher',
            'student' => 'student',
            'subscriber' => 'student',        // Default WordPress role (lowest priority)
        ];

        // Check roles in priority order (most specific first)
        $priorityOrder = ['administrator', 'tutor_instructor', 'tutor', 'instructor', 'teacher', 'student'];
        
        Log::info('Checking roles in priority order', [
            'user_roles' => $roles,
            'priority_order' => $priorityOrder
        ]);
        
        foreach ($priorityOrder as $priorityRole) {
            if (in_array($priorityRole, $roles) && isset($roleMap[$priorityRole])) {
                Log::info("✓ PRIORITY MATCH: WordPress role '{$priorityRole}' → Laravel role '{$roleMap[$priorityRole]}'", [
                    'matched_role' => $priorityRole,
                    'mapped_to' => $roleMap[$priorityRole],
                    'all_roles' => $roles
                ]);
                return $roleMap[$priorityRole];
            }
        }

        // Fallback: Check remaining roles
        Log::info('No priority role matched, checking remaining roles', [
            'remaining_roles' => $roles,
            'role_map' => array_keys($roleMap)
        ]);
        
        foreach ($roles as $role) {
            if (isset($roleMap[$role])) {
                Log::info("✓ FALLBACK MATCH: WordPress role '{$role}' → Laravel role '{$roleMap[$role]}'", [
                    'matched_role' => $role,
                    'mapped_to' => $roleMap[$role]
                ]);
                return $roleMap[$role];
            }
        }

        // Special case: If user has admin-like characteristics, check for admin role
        $username = $wpUser['username'] ?? $wpUser['user_login'] ?? $wpUser['slug'] ?? '';
        $displayName = $wpUser['display_name'] ?? $wpUser['name'] ?? '';
        
        // Check if this looks like an admin user based on username or name
        if (strpos($username, 'admin') !== false || strpos($displayName, 'Admin') !== false || strpos($displayName, 'Dev') !== false) {
            Log::info("User appears to be admin based on username/name: {$username}/{$displayName}");
            return 'admin';
        }

        Log::info('No matching role found, defaulting to student', [
            'available_roles' => $roles,
            'checked_roles' => $priorityOrder
        ]);
        return 'student'; // fallback
    }
    
    /**
     * Alternative method to get user data from token
     */
    protected function getUserDataFromToken($token, $provider)
    {
        $serverUrl = SystemSetting::getValue('wp_oauth_server');
        $accessToken = $token->getToken();
        
        // Try to get user data directly from WordPress REST API
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->get($serverUrl . '/wp-json/wp/v2/users/me');
        
        if ($response->successful()) {
            $userData = $response->json();
            //Log::info('Got user data from WordPress REST API', $userData);
            
            // Try to get more detailed user information
            $userId = $userData['id'] ?? null;
            $detailedUserData = $this->getDetailedUserData($userId, $accessToken, $serverUrl);
            
            // Get enhanced user data including phone and meta information
            $enhancedData = $this->getEnhancedUserData($userId, $accessToken, $serverUrl);
            
            // Transform WordPress REST API response to expected format with maximum data
            return [
                'ID' => $userId,
                'user_email' => $detailedUserData['email'] ?? $userData['email'] ?? $enhancedData['email'] ?? null,
                'display_name' => $userData['name'] ?? $detailedUserData['display_name'] ?? null,
                'name' => $userData['name'] ?? $detailedUserData['display_name'] ?? null,
                'username' => $detailedUserData['username'] ?? $userData['slug'] ?? null,
                'user_login' => $detailedUserData['username'] ?? $userData['slug'] ?? null,
                'user_roles' => $detailedUserData['roles'] ?? $userData['roles'] ?? $enhancedData['roles'] ?? ['subscriber'],
                'phone' => $enhancedData['phone'] ?? null,
                'mobile' => $enhancedData['mobile'] ?? null,
                'telephone' => $enhancedData['telephone'] ?? null,
                'contact_phone' => $enhancedData['contact_phone'] ?? null,
                'user_phone' => $enhancedData['user_phone'] ?? null,
                'billing_phone' => $enhancedData['billing_phone'] ?? null,
                'shipping_phone' => $enhancedData['shipping_phone'] ?? null,
                'url' => $userData['url'] ?? $userData['link'] ?? $detailedUserData['url'] ?? null,
                'description' => $userData['description'] ?? $detailedUserData['description'] ?? null,
                'avatar_urls' => $userData['avatar_urls'] ?? $detailedUserData['avatar_urls'] ?? null,
                'meta' => $enhancedData['meta'] ?? $detailedUserData['meta'] ?? null,
                'capabilities' => $enhancedData['capabilities'] ?? $detailedUserData['capabilities'] ?? null,
            ];
        }
        
        throw new \Exception('Failed to get user data from WordPress REST API');
    }
    
    /**
     * Get detailed user data from WordPress
     */
    protected function getDetailedUserData($userId, $accessToken, $serverUrl)
    {
        try {
            // Try to get user data by ID with more details
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users/' . $userId);
            
            if ($response->successful()) {
                $userData = $response->json();
                //Log::info('Got detailed user data from WordPress', $userData);
                
                // Try to get user roles from WordPress admin API
                $roles = $this->getUserRolesFromWordPress($userId, $accessToken, $serverUrl);
                if (!empty($roles)) {
                    $userData['roles'] = $roles;
                    //Log::info('Updated user data with roles from WordPress admin API', $roles);
                }
                
                return $userData;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get detailed user data: ' . $e->getMessage());
        }
        
        // Fallback: try to get user data from slug
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users?slug=site_admin-1');
            
            if ($response->successful()) {
                $users = $response->json();
                if (!empty($users)) {
                    $userData = $users[0];
                    //Log::info('Got user data by slug from WordPress', $userData);
                    
                    // Try to get user roles from WordPress admin API
                    $roles = $this->getUserRolesFromWordPress($userData['id'], $accessToken, $serverUrl);
                    if (!empty($roles)) {
                        $userData['roles'] = $roles;
                        //Log::info('Updated user data with roles from WordPress admin API', $roles);
                    }
                    
                    return $userData;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user data by slug: ' . $e->getMessage());
        }
        
        return [];
    }
    
    /**
     * Get user roles from WordPress admin API
     */
    protected function getUserRolesFromWordPress($userId, $accessToken, $serverUrl)
    {
        try {
            // Try to get user roles from WordPress admin API
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users/' . $userId . '?context=edit');
            
            if ($response->successful()) {
                $userData = $response->json();
                $roles = $userData['roles'] ?? [];
                //Log::info('Got user roles from WordPress admin API', $roles);
                return $roles;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user roles from WordPress admin API: ' . $e->getMessage());
        }
        
        return [];
    }
    
    /**
     * Get enhanced user data including phone numbers and meta information
     */
    protected function getEnhancedUserData($userId, $accessToken, $serverUrl)
    {
        $enhancedData = [];
        
        try {
            // Get user meta data
            $metaResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users/' . $userId . '/meta');
            
            if ($metaResponse->successful()) {
                $metaData = $metaResponse->json();
                $enhancedData['meta'] = $metaData;
                
                // Extract phone numbers from meta data
                $phoneFields = [
                    'phone', 'mobile', 'telephone', 'contact_phone', 
                    'user_phone', 'billing_phone', 'shipping_phone'
                ];
                
                foreach ($phoneFields as $field) {
                    if (isset($metaData[$field])) {
                        $enhancedData[$field] = $metaData[$field];
                    }
                }
                
                //Log::info('Got enhanced user meta data', $enhancedData);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get enhanced user meta data: ' . $e->getMessage());
        }
        
        // Try to get user data with edit context for more details
        try {
            $editResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users/' . $userId . '?context=edit');
            
            if ($editResponse->successful()) {
                $editData = $editResponse->json();
                
                // Merge additional data
                if (isset($editData['email'])) {
                    $enhancedData['email'] = $editData['email'];
                }
                if (isset($editData['roles'])) {
                    $enhancedData['roles'] = $editData['roles'];
                }
                if (isset($editData['capabilities'])) {
                    $enhancedData['capabilities'] = $editData['capabilities'];
                }
                
                //Log::info('Got enhanced user data with edit context', $enhancedData);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get enhanced user data with edit context: ' . $e->getMessage());
        }
        
        // Try to get user data by slug as fallback
        try {
            $slugResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($serverUrl . '/wp-json/wp/v2/users?slug=site_admin-1');
            
            if ($slugResponse->successful()) {
                $slugData = $slugResponse->json();
                if (!empty($slugData) && isset($slugData[0])) {
                    $userData = $slugData[0];
                    
                    // Merge additional data from slug search
                    if (isset($userData['email']) && !isset($enhancedData['email'])) {
                        $enhancedData['email'] = $userData['email'];
                    }
                    if (isset($userData['roles']) && !isset($enhancedData['roles'])) {
                        $enhancedData['roles'] = $userData['roles'];
                    }
                    if (isset($userData['capabilities']) && !isset($enhancedData['capabilities'])) {
                        $enhancedData['capabilities'] = $userData['capabilities'];
                    }
                    
                    //Log::info('Got enhanced user data from slug search', $enhancedData);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get enhanced user data from slug search: ' . $e->getMessage());
        }
        
        return $enhancedData;
    }
    
    /**
     * Alternative WordPress REST API Authentication
     * This method uses WordPress REST API when OAuth is not available
     */
    public function restApiAuth(Request $request)
    {
        try {
            $serverUrl = SystemSetting::getValue('wp_oauth_server');
            $username = $request->input('username');
            $password = $request->input('password');
            
            if (!$username || !$password) {
                return redirect()->route('login')->with('error', 'Username and password are required.');
            }
            
            // Create WordPress REST API authentication URL
            $authUrl = $serverUrl . '/wp-json/wp/v2/users/me';
            
            // Make authenticated request to WordPress
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($username, $password)
                ->get($authUrl);
            
            if ($response->successful()) {
                $wpUser = $response->json();
                
                // Check if user exists in Laravel
                $user = User::where('email', $wpUser['email'])->first();
                
                if (!$user) {
                    // Create new user
                    $user = $this->createUserFromWordPress($wpUser);
                } else {
                    // Existing user - sync role from WordPress
                    $wpRole = $this->determineUserRole($wpUser);
                    if ($user->role !== $wpRole) {
                        Log::info('User role changed in WordPress (REST API), updating local role', [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'old_role' => $user->role,
                            'new_role' => $wpRole
                        ]);
                        $user->update(['role' => $wpRole]);
                        $this->ensureProfileExists($user, $wpUser, $wpRole);
                    }
                }
                
                // Verify user role is valid
                $this->verifyUserRole($user);
                
                // Login user
                Auth::login($user);
                
                // Redirect based on role
                return $this->redirectAfterLogin($user);
            } else {
                return redirect()->route('login')->with('error', 'Invalid WordPress credentials.');
            }
            
        } catch (\Exception $e) {
            Log::error('WordPress REST API auth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'WordPress authentication failed. Please try again.');
        }
    }
    
    /**
     * Create user from WordPress data
     */
    private function createUserFromWordPress($wpUser)
    {
        $user = User::create([
            'name' => $wpUser['name'] ?? $wpUser['username'],
            'email' => $wpUser['email'],
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)), // Random password for OAuth users
            'role' => 'student', // Default role
            'email_verified_at' => now(),
        ]);
        
        // Create student profile
        Student::create([
            'user_id' => $user->id,
            'phone' => $wpUser['phone'] ?? null,
            'address' => $wpUser['address'] ?? null,
        ]);
        
        return $user;
    }
}