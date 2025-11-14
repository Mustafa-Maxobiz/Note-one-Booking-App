<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Services\EmailService;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['teacher', 'student']);
        
        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Apply teacher-specific filters
        if ($request->filled('teacher_verification')) {
            if ($request->teacher_verification === 'verified') {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_verified', true);
                });
            } elseif ($request->teacher_verification === 'unverified') {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_verified', false);
                });
            }
        }
        
        if ($request->filled('teacher_availability')) {
            if ($request->teacher_availability === 'available') {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_available', true);
                });
            } elseif ($request->teacher_availability === 'unavailable') {
                $query->whereHas('teacher', function($q) {
                    $q->where('is_available', false);
                });
            }
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,teacher', // Only admin and teacher allowed
            'phone' => 'nullable|string|max:30', // Allow international format
        ]);

        // Generate a secure random password
        $password = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'password' => bcrypt($password),
        ]);

        // Send email notification with login credentials
        EmailService::sendAccountCreationEmail($user, $password);

        // Log the account creation
        \Log::info('User account created', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'created_by' => auth()->id(),
            'created_by_name' => auth()->user()->name,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User created successfully! Login credentials have been sent to {$user->email}.");
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string|max:30', // Allow international format
            'is_active' => 'boolean',
            // Teacher-specific fields
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'is_verified' => 'boolean',
            'is_available' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        // Update teacher profile if user is a teacher
        if ($user->role === 'teacher' && $user->teacher) {
            $user->teacher->update([
                'bio' => $validated['bio'] ?? $user->teacher->bio,
                'qualifications' => $validated['qualifications'] ?? $user->teacher->qualifications,
                'is_verified' => $validated['is_verified'] ?? $user->teacher->is_verified,
                'is_available' => $validated['is_available'] ?? $user->teacher->is_available,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Check if user can be deleted using model method
        if (!$user->canBeDeleted()) {
            $reason = $user->getDeletionBlockReason();
            return redirect()->route('admin.users.index')
                ->with('error', $reason);
        }

        // Safe to delete
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle teacher verification status
     */
    public function toggleVerification(User $user)
    {
        try {
            if ($user->role != 'teacher' || !$user->teacher) {
                return redirect()->route('admin.users.index')->with('error', 'User is not a teacher!');
            }

            $wasVerified = $user->teacher->is_verified;
            
            $user->teacher->update([
                'is_verified' => !$user->teacher->is_verified
            ]);

            $newStatus = $user->teacher->fresh()->is_verified;
            $status = $newStatus ? 'verified' : 'unverified';

            // Send approval email if teacher is being verified (not unverified)
            if (!$wasVerified && $newStatus) {
                $this->sendTeacherApprovalEmail($user);
                
                \Log::info('Teacher approved and email sent', [
                    'teacher_id' => $user->id,
                    'teacher_email' => $user->email,
                    'teacher_name' => $user->name,
                    'approved_by' => auth()->id(),
                    'approved_by_name' => auth()->user()->name,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', "Teacher {$user->name} has been {$status}!" . ($newStatus ? ' Approval email sent.' : ''));
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to toggle verification status: ' . $e->getMessage());
        }
    }

    /**
     * Send teacher approval email
     */
    private function sendTeacherApprovalEmail(User $user)
    {
        try {
            $data = [
                'teacher_name' => $user->name,
                'approved_at' => now()->format('F d, Y g:i A'),
                'login_url' => route('login'),
                'dashboard_url' => route('teacher.dashboard'),
            ];

            \Mail::send('emails.teacher-approved', $data, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Congratulations! Your Teacher Account is Approved');
            });

            \Log::info('Teacher approval email sent successfully', [
                'teacher_id' => $user->id,
                'teacher_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send teacher approval email', [
                'teacher_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle teacher availability status
     */
    public function toggleAvailability(User $user)
    {
        try {
            if ($user->role != 'teacher' || !$user->teacher) {
                return redirect()->route('admin.users.index')->with('error', 'User is not a teacher!');
            }

            $user->teacher->update([
                'is_available' => !$user->teacher->is_available
            ]);

            $status = $user->teacher->fresh()->is_available ? 'available' : 'unavailable';
            return redirect()->route('admin.users.index')->with('success', "Teacher {$user->name} is now {$status} for bookings!");
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Failed to toggle availability status: ' . $e->getMessage());
        }
    }

    /**
     * Auto-login as a specific user (admin only)
     */
    public function autoLogin(User $user)
    {
        try {
            // Check if current user is admin
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('admin.users.index')->with('error', 'Only administrators can use auto-login feature!');
            }

            // Prevent auto-login to another admin for security
            if ($user->isAdmin()) {
                return redirect()->route('admin.users.index')->with('error', 'Cannot auto-login to another admin account for security reasons!');
            }

            // Store current admin user ID for return
            session(['admin_user_id' => auth()->id()]);
            
            // Log the auto-login action
            \Log::info('Admin auto-login', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'target_user_id' => $user->id,
                'target_user_name' => $user->name,
                'target_user_role' => $user->role,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Login as the target user
            auth()->login($user);

            // Redirect based on user role
            switch ($user->role) {
                case 'teacher':
                    return redirect()->route('teacher.dashboard')->with('success', "Successfully logged in as {$user->name} (Teacher)");
                case 'student':
                    return redirect()->route('student.dashboard')->with('success', "Successfully logged in as {$user->name} (Student)");
                default:
                    return redirect()->route('admin.dashboard')->with('success', "Successfully logged in as {$user->name}");
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Auto-login failed: ' . $e->getMessage());
        }
    }

    /**
     * Return to admin account from auto-login
     */
    public function returnToAdmin()
    {
        try {
            $adminUserId = session('admin_user_id');
            
            if (!$adminUserId) {
                return redirect()->route('login')->with('error', 'No admin session found. Please log in normally.');
            }

            $adminUser = User::find($adminUserId);
            
            if (!$adminUser) {
                return redirect()->route('login')->with('error', 'Admin user not found. Please log in normally.');
            }

            // Check if the stored user is actually an admin (double-check)
            if (!$adminUser->isAdmin()) {
                return redirect()->route('login')->with('error', 'Stored user is not an admin. Please log in normally.');
            }

            // Log the return to admin
            \Log::info('Return to admin from auto-login', [
                'admin_id' => $adminUserId,
                'admin_name' => $adminUser->name,
                'previous_user_id' => auth()->id(),
                'previous_user_name' => auth()->user()->name,
                'previous_user_role' => auth()->user()->role,
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            // Clear the admin session
            session()->forget('admin_user_id');
            
            // Login as admin
            auth()->login($adminUser);

            return redirect()->route('admin.dashboard')->with('success', 'Successfully returned to admin account');
        } catch (\Exception $e) {
            \Log::error('Return to admin failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => session('admin_user_id'),
                'current_user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);
            
            return redirect()->route('login')->with('error', 'Failed to return to admin account: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a user with confirmation
     */
    public function delete(Request $request)
    {
        $user = User::findOrFail($request->id);
        
        // Check if user can be deleted
        if (!$user->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => $user->getDeletionBlockReason()
            ]);
        }

        return $user->softDeleteWithConfirmation($request);
    }

    /**
     * Restore a soft deleted user
     */
    public function restore(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->id);
        return $user->restoreWithConfirmation($request);
    }

    /**
     * Display trashed users
     */
    public function trashed()
    {
        $users = User::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('admin.users.trashed', compact('users'));
    }

    /**
     * Resend password setup email to user
     */
    public function resendPasswordEmail(User $user)
    {
        try {
            // Generate a new secure random password
            $password = Str::random(12);

            // Update user's password
            $user->update([
                'password' => bcrypt($password),
            ]);

            // Send email notification with new login credentials
            EmailService::sendAccountCreationEmail($user, $password);

            // Log the password reset
            \Log::info('Password reset email sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'reset_by' => auth()->id(),
                'reset_by_name' => auth()->user()->name,
                'ip_address' => request()->ip(),
                'timestamp' => now()
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "Password reset! New login credentials have been sent to {$user->email}.");

        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'reset_by' => auth()->id(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to send password reset email. Please try again.');
        }
    }
}
