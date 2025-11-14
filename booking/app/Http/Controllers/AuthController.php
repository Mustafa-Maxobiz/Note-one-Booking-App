<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Verify and sync role if user has WordPress ID
            if ($user->wordpress_id) {
                $user = $this->syncRoleFromWordPress($user);
            }
            
            // Verify user role is valid
            $this->verifyUserRole($user);
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Sync role from WordPress for OAuth users
     */
    protected function syncRoleFromWordPress(User $user)
    {
        try {
            $serverUrl = \App\Models\SystemSetting::getValue('wp_oauth_server');
            $enabled = \App\Models\SystemSetting::getValue('wp_oauth_enabled');
            
            if (!$serverUrl || $enabled != '1') {
                return $user; // WordPress OAuth not configured
            }
            
            // Try to get user data from WordPress REST API
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get($serverUrl . '/wp-json/wp/v2/users/' . $user->wordpress_id);
            
            if ($response->successful()) {
                $wpUser = $response->json();
                
                // Determine role from WordPress
                $wpRoles = $wpUser['roles'] ?? [];
                $newRole = $this->determineRoleFromWordPress($wpRoles);
                
                // Extract membership level from WordPress
                $membershipLevel = $this->extractMembershipLevel($wpUser);
                
                $updateData = [];
                
                // Update role if changed
                if ($user->role !== $newRole) {
                    $updateData['role'] = $newRole;
                    \Illuminate\Support\Facades\Log::info('User role synced from WordPress on login', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'old_role' => $user->role,
                        'new_role' => $newRole
                    ]);
                }
                
                // Update membership level if changed
                if ($membershipLevel && $user->membership_level !== $membershipLevel) {
                    $updateData['membership_level'] = $membershipLevel;
                    \Illuminate\Support\Facades\Log::info('User membership level synced from WordPress on login', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'old_level' => $user->membership_level,
                        'new_level' => $membershipLevel
                    ]);
                }
                
                if (!empty($updateData)) {
                    $user->update($updateData);
                    $user->refresh();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to sync role from WordPress', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return $user;
    }

    /**
     * Determine role from WordPress roles array
     */
    protected function determineRoleFromWordPress(array $wpRoles)
    {
        $map = [
            'administrator' => 'admin',
            'teacher'       => 'teacher',
            'student'       => 'student',
            'subscriber'    => 'student',
        ];

        foreach ($wpRoles as $role) {
            if (isset($map[$role])) {
                return $map[$role];
            }
        }

        return 'student'; // Default fallback
    }

    /**
     * Extract membership level from WordPress user data
     */
    protected function extractMembershipLevel($wpUser)
    {
        // Try various possible meta keys for membership level
        $possibleKeys = [
            'membership_level',
            'pmpro_membership_level',
            'pmpro_level',
            'level',
            'wc_memberships_plan',
            'membership_plan',
        ];
        
        // Check direct keys first
        foreach ($possibleKeys as $key) {
            if (isset($wpUser[$key]) && !empty($wpUser[$key])) {
                $level = is_array($wpUser[$key]) ? ($wpUser[$key]['name'] ?? $wpUser[$key]['title'] ?? null) : $wpUser[$key];
                if ($level) {
                    return $level;
                }
            }
        }
        
        // Check meta array if exists
        if (isset($wpUser['meta']) && is_array($wpUser['meta'])) {
            foreach ($possibleKeys as $key) {
                if (isset($wpUser['meta'][$key]) && !empty($wpUser['meta'][$key])) {
                    $level = is_array($wpUser['meta'][$key]) ? ($wpUser['meta'][$key]['name'] ?? $wpUser['meta'][$key]['title'] ?? null) : $wpUser['meta'][$key];
                    if ($level) {
                        return $level;
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Verify user role is valid
     */
    protected function verifyUserRole(User $user)
    {
        $validRoles = ['admin', 'teacher', 'student'];
        
        if (!in_array($user->role, $validRoles)) {
            \Illuminate\Support\Facades\Log::warning('Invalid user role detected, resetting to student', [
                'user_id' => $user->id,
                'invalid_role' => $user->role
            ]);
            
            $user->update(['role' => 'student']);
            $user->refresh();
        }
        
        // Verify user is active
        if (!$user->is_active) {
            Auth::logout();
            abort(403, 'Your account has been deactivated. Please contact administrator.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
