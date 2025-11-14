<?php



namespace App\Http\Controllers\Student;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;



class ProfileController extends Controller

{

    public function index()

    {

        $student = Auth::user()->student;

        

        // If student profile doesn't exist, create it

        if (!$student) {

            $student = \App\Models\Student::create([

                'user_id' => Auth::user()->id,

                'level' => 'Beginner',

                'goals' => '',

                'learning_style' => null,

                'preferred_subjects' => [],

            ]);

        }

        

        return view('student.profile.index', compact('student'));

    }



    public function update(Request $request)

    {

        $user = Auth::user();

        $student = $user->student;

        

        // If student profile doesn't exist, create it

        if (!$student) {

            $student = \App\Models\Student::create([

                'user_id' => $user->id,

                'level' => 'Beginner',

                'goals' => '',

                'learning_style' => null,

                'preferred_subjects' => [],

            ]);

        }

        

        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'email' => [

                'required',

                'email',

                Rule::unique('users')->ignore($user->id),

            ],

            'phone' => 'nullable|string|max:20',

            'level' => 'required|in:Beginner,Intermediate,Advanced',

            'goals' => 'nullable|string|max:500',

            'learning_style' => 'nullable|in:Visual,Auditory,Kinesthetic',

            'preferred_subjects' => 'nullable|array',

            'preferred_subjects.*' => 'string|in:math,science,english,history,computer,general',

            'timezone' => 'required|string|max:255',

            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        // Debug logging
        \Log::info('Student profile update', [
            'user_id' => $user->id,
            'student_id' => $student->id,
            'level' => $validated['level'],
            'learning_style' => $validated['learning_style'],
            'goals' => $validated['goals'],
            'has_profile_picture' => $request->hasFile('profile_picture')
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



        // Update user information

        $user->update([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'phone' => $validated['phone'],

            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,

        ]);



        // Update student information

        $student->update([

            'level' => $validated['level'],

            'learning_goals' => $validated['goals'],

            'learning_style' => $validated['learning_style'],

            'preferred_subjects' => $validated['preferred_subjects'] ?? [],

            'timezone' => $validated['timezone'],

        ]);

        // Debug logging after update
        \Log::info('Student profile updated', [
            'student_id' => $student->id,
            'updated_level' => $student->fresh()->level,
            'updated_learning_style' => $student->fresh()->learning_style,
            'updated_goals' => $student->fresh()->learning_goals,
            'updated_profile_picture' => $user->fresh()->profile_picture
        ]);

        return redirect()->route('student.profile.index')

            ->with('success', 'Profile updated successfully.');

    }



    public function updatePassword(Request $request)

    {

        $user = Auth::user();

        

        $validated = $request->validate([

            'current_password' => 'required|current_password',

            'password' => 'required|string|min:8|confirmed',

        ]);



        $user->update([

            'password' => Hash::make($validated['password'])

        ]);



        return redirect()->route('student.profile.index')

            ->with('success', 'Password updated successfully.');

    }

}

