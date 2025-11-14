<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\WordPressOAuthProvider;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Services\NotificationService;

class OAuthController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new WordPressOAuthProvider([
            'clientId' => config('oauth.wordpress.client_id'),
            'clientSecret' => config('oauth.wordpress.client_secret'),
            'redirectUri' => config('oauth.wordpress.redirect_uri'),
            'server_url' => config('oauth.wordpress.server_url'),
            'authorize_url' => config('oauth.wordpress.authorize_url'),
            'token_url' => config('oauth.wordpress.token_url'),
            'user_url' => config('oauth.wordpress.user_url'),
        ]);
    }

    /**
     * Redirect to WordPress OAuth2 authorization
     */
    public function redirectToWordPress()
    {
        try {
            $authorizationUrl = $this->provider->getAuthorizationUrl([
                'scope' => ['read', 'profile', 'email']
            ]);

            // Store state for security
            session(['oauth2state' => $this->provider->getState()]);

            return redirect($authorizationUrl);

        } catch (\Exception $e) {
            Log::error('WordPress OAuth2 redirect error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Failed to initiate WordPress login.');
        }
    }

    /**
     * Handle WordPress OAuth2 callback
     */
    public function handleWordPressCallback(Request $request)
    {
        try {
            // Verify state parameter
            $state = $request->get('state');
            if (!$state || $state !== session('oauth2state')) {
                Log::warning('WordPress OAuth2 state mismatch');
                return redirect()->route('login')->with('error', 'Invalid state parameter.');
            }

            // Clear state from session
            session()->forget('oauth2state');

            // Check for error
            if ($request->has('error')) {
                $error = $request->get('error_description', $request->get('error'));
                Log::error('WordPress OAuth2 error: ' . $error);
                return redirect()->route('login')->with('error', 'WordPress login failed: ' . $error);
            }

            // Get authorization code
            $code = $request->get('code');
            if (!$code) {
                return redirect()->route('login')->with('error', 'Authorization code not received.');
            }

            // Exchange code for access token
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            // Get user information
            $wordPressUser = $this->provider->getResourceOwner($accessToken);

            // Find or create user
            $user = $this->findOrCreateUser($wordPressUser);

            // Log user in
            Auth::login($user);

            // Send welcome notification
            NotificationService::sendNotification(
                $user->id,
                'welcome',
                'Welcome to Online Lesson Booking System!',
                'You have successfully logged in using your WordPress account.'
            );

            Log::info('WordPress OAuth2 login successful', [
                'user_id' => $user->id,
                'wordpress_id' => $wordPressUser->getId()
            ]);

            // Redirect based on user role
            return $this->redirectAfterLogin($user);

        } catch (\Exception $e) {
            Log::error('WordPress OAuth2 callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'WordPress login failed. Please try again.');
        }
    }

    /**
     * Find existing user or create new one
     */
    protected function findOrCreateUser($wordPressUser)
    {
        $email = $wordPressUser->getEmail();
        
        if (!$email) {
            throw new \Exception('Email not provided by WordPress');
        }

        // Check if user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // Update WordPress ID if not set
            if (!$user->wordpress_id) {
                $user->update(['wordpress_id' => $wordPressUser->getId()]);
            }
            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $wordPressUser->getDisplayName() ?: $wordPressUser->getLogin(),
            'email' => $email,
            'password' => bcrypt(str_random(32)), // Random password since using OAuth
            'role' => 'student', // Default role
            'wordpress_id' => $wordPressUser->getId(),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create student profile
        Student::create([
            'user_id' => $user->id,
            'phone' => null,
            'date_of_birth' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'country' => null,
            'postal_code' => null,
            'bio' => null,
            'profile_image' => $wordPressUser->getAvatarUrl(),
        ]);

        // Send welcome email to new user
        $this->sendWelcomeEmail($user);

        return $user;
    }

    /**
     * Send welcome email to new user
     */
    protected function sendWelcomeEmail($user)
    {
        try {
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'login_url' => route('login'),
                'dashboard_url' => $user->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'),
                'booking_url' => route('student.dashboard'), // Default booking URL
            ];

            // Send welcome email using EmailService
            \App\Services\EmailService::sendEmail(
                $user->email,
                'Welcome to Online Lesson Booking System',
                $data,
                'emails.welcome'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    /**
     * Redirect user after successful login
     */
    protected function redirectAfterLogin($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
            default:
                return redirect()->route('student.dashboard');
        }
    }

    /**
     * Test WordPress OAuth2 connection
     */
    public function testWordPressConnection()
    {
        try {
            $config = [
                'clientId' => config('oauth.wordpress.client_id'),
                'clientSecret' => config('oauth.wordpress.client_secret'),
                'redirectUri' => config('oauth.wordpress.redirect_uri'),
                'server_url' => config('oauth.wordpress.server_url'),
            ];

            $missing = [];
            foreach ($config as $key => $value) {
                if (empty($value)) {
                    $missing[] = $key;
                }
            }

            if (!empty($missing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing configuration: ' . implode(', ', $missing)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'WordPress OAuth2 configuration is valid',
                'config' => [
                    'server_url' => $config['server_url'],
                    'redirect_uri' => $config['redirectUri'],
                    'client_id' => substr($config['clientId'], 0, 10) . '...'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'WordPress OAuth2 test failed: ' . $e->getMessage()
            ]);
        }
    }
}
