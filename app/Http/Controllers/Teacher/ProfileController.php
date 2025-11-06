<?php



namespace App\Http\Controllers\Teacher;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;



class ProfileController extends Controller

{

    public function index()

    {

        $teacher = Auth::user()->teacher;

        

        // If teacher profile doesn't exist, create it

        if (!$teacher) {

            $teacher = \App\Models\Teacher::create([

                'user_id' => Auth::user()->id,

                'bio' => '',

                'qualifications' => '',

                'subjects' => [],

                'teaching_style' => '',

                'experience_years' => 0,

            ]);

        }

        

        return view('teacher.profile.index', compact('teacher'));

    }



    public function update(Request $request)

    {

        $user = Auth::user();

        $teacher = $user->teacher;



        // If teacher profile doesn't exist, create it

        if (!$teacher) {

            $teacher = \App\Models\Teacher::create([

                'user_id' => $user->id,

                'bio' => '',

                'qualifications' => '',

                'subjects' => [],

                'teaching_style' => '',

                'experience_years' => 0,

            ]);

        }



        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email,' . $user->id,

            'phone' => 'required|string|max:30',

            'bio' => 'required|string|max:1000',

            'qualifications' => 'required|string|max:255',

            'experience_years' => 'required|integer|min:0|max:50',
            
            'subjects' => 'nullable|array',

            'teaching_style' => 'required|string|max:500',

            'timezone' => 'required|string|max:255',

            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);



        // Handle profile picture upload

        if ($request->hasFile('profile_picture')) {

            // Delete old profile picture if exists

            if ($user->profile_picture && file_exists(public_path('storage/' . $user->profile_picture))) {

                unlink(public_path('storage/' . $user->profile_picture));

            }

            

            // Store new profile picture

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');

            $validated['profile_picture'] = $path;

        }



        // Update user info

        $user->update([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'phone' => $validated['phone'],

            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,

        ]);



        // Update teacher profile

        $teacher->update([

            'bio' => $validated['bio'],

            'qualifications' => $validated['qualifications'],

            'subjects' => $validated['subjects'] ?? [],

            'teaching_style' => $validated['teaching_style'],

            'experience_years' => $validated['experience_years'],

            'timezone' => $validated['timezone'],
        ]);



        return redirect()->route('teacher.profile.index')->with('success', 'Profile updated successfully!');

    }



    public function updatePassword(Request $request)

    {

        $validated = $request->validate([

            'current_password' => 'required|current_password',

            'password' => 'required|string|min:8|confirmed',

        ]);



        Auth::user()->update([

            'password' => Hash::make($validated['password']),

        ]);



        return redirect()->route('teacher.profile.index')->with('success', 'Password updated successfully!');

    }

    /**
     * Check for status updates (verification and availability)
     */
    public function statusCheck()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'verification_status' => $teacher->is_verified,
            'availability_status' => $teacher->is_available,
            'last_updated' => $teacher->updated_at->toISOString()
        ]);
    }

}

