@extends('layouts.app')



@section('title', 'Student Profile')



@section('content')

<!-- Page Header -->

<div class="page-header mb-4">

    <div class="row align-items-center">

        <div class="col-md-8">

            <h1 class="page-title">

                <i class="fas fa-user-graduate me-3"></i>Student Profile

            </h1>

            <p class="page-subtitle">Manage your profile information, learning preferences, and goals</p>

        </div>

        <div class="col-md-4 text-end">

            <div class="profile-status">

                <span class="status-badge status-student">

                    <i class="fas fa-graduation-cap me-1"></i>{{ $student->level }} Level

                </span>

            </div>

        </div>

    </div>

</div>



<div class="row">

    <div class="col-lg-4">

        <!-- Profile Picture Card -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-camera me-2"></i>Profile Picture

                </h5>

                <p class="modern-card-subtitle">Update your profile photo</p>

            </div>

            <div class="modern-card-body text-center">
                @php
                    $studentPicture = auth()->user()->small_profile_picture_url
                        ? auth()->user()->small_profile_picture_url
                        : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) . '&background=28a745&color=fff&size=200';
                @endphp
                <img src="{{ $studentPicture }}" alt="{{ $student->user->name }}" class="profile-picture" id="profilePicturePreview">
                <p class="profile-picture-text">Your Profile Picture.</p>
            </div>

        </div>



        <!-- Student Stats -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-chart-line me-2"></i>Learning Statistics

                </h5>

                <p class="modern-card-subtitle">Your learning progress</p>

            </div>

            <div class="modern-card-body p-2">

                <div class="stats-grid">

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="fas fa-calendar-alt"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $student->bookings()->count() }}</div>

                            <div class="stat-label">Total Sessions</div>

                        </div>

                    </div>

                    <div class="stat-item">

                        <div class="stat-icon">

                            <i class="fas fa-check-circle"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $student->bookings()->where('status', 'completed')->count() }}</div>

                            <div class="stat-label">Completed</div>

                        </div>

                    </div>

                </div>

                

                <div class="profile-info">

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-graduation-cap me-2"></i>Level

                        </div>

                        <div class="info-value">

                            <span class="level-badge level-{{ strtolower($student->level) }}">{{ $student->level }}</span>

                        </div>

                    </div>

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-brain me-2"></i>Learning Style

                        </div>

                        <div class="info-value">{{ $student->learning_style ?? 'Not specified' }}</div>

                    </div>

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-calendar-plus me-2"></i>Member Since

                        </div>

                        <div class="info-value">{{ $student->user->created_at->format('M d, Y') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="col-lg-8">

        <!-- Profile Information -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-user-edit me-2"></i>Profile Information

                </h5>

                <p class="modern-card-subtitle">Update your personal details and learning preferences</p>

            </div>

            <div class="modern-card-body">

                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="profile-form" id="profileForm">

                    @csrf

                    @method('PUT')

                    

                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="name" class="form-label">

                                <i class="fas fa-user me-2"></i>Full Name

                            </label>

                            <input type="text" class="form-control modern-input @error('name') is-invalid @enderror" 

                                   id="name" name="name" value="{{ old('name', $student->user->name) }}" required>

                            @error('name')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label for="email" class="form-label">

                                <i class="fas fa-envelope me-2"></i>Email Address

                            </label>

                            <input type="email" class="form-control modern-input @error('email') is-invalid @enderror" 

                                   id="email" name="email" value="{{ old('email', $student->user->email) }}" required>

                            @error('email')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                    </div>



                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="phone" class="form-label">

                                <i class="fas fa-phone me-2"></i>Phone Number

                            </label>

                            <input type="text" class="form-control modern-input @error('phone') is-invalid @enderror" 

                                   id="phone" name="phone" value="{{ old('phone', $student->user->phone) }}"
                                   
                                   placeholder="e.g., +1 (555) 123-4567">

                            @error('phone')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                            <div class="form-text">

                                <i class="fas fa-globe me-1"></i>International format accepted (e.g., +1 555-1234, +44 20 1234 5678)

                            </div>

                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">
                                <i class="fas fa-image me-2"></i>Profile Picture
                            </label>
                            @php
                                $studentPictureForm = auth()->user()->small_profile_picture_url
                                    ? auth()->user()->small_profile_picture_url
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) . '&background=28a745&color=fff&size=200';
                            @endphp
                            @include('components.profile-picture-upload', ['currentPicture' => $studentPictureForm])
                        </div>

                    </div>



                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="level" class="form-label">

                                <i class="fas fa-graduation-cap me-2"></i>Learning Level

                            </label>

                            <select class="form-control modern-select @error('level') is-invalid @enderror" 

                                    id="level" name="level" required>

                                <option value="">Select level</option>

                                <option value="Beginner" {{ old('level', $student->level) == 'Beginner' || strtolower($student->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>

                                <option value="Intermediate" {{ old('level', $student->level) == 'Intermediate' || strtolower($student->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>

                                <option value="Advanced" {{ old('level', $student->level) == 'Advanced' || strtolower($student->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>

                            </select>

                            @error('level')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label for="learning_style" class="form-label">

                                <i class="fas fa-brain me-2"></i>Learning Style

                            </label>

                            <select class="form-control modern-select @error('learning_style') is-invalid @enderror" 

                                    id="learning_style" name="learning_style">

                                <option value="">Select style</option>

                                <option value="Visual" {{ old('learning_style', $student->learning_style) == 'Visual' || strtolower($student->learning_style) == 'visual' ? 'selected' : '' }}>Visual</option>

                                <option value="Auditory" {{ old('learning_style', $student->learning_style) == 'Auditory' || strtolower($student->learning_style) == 'auditory' ? 'selected' : '' }}>Auditory</option>

                                <option value="Kinesthetic" {{ old('learning_style', $student->learning_style) == 'Kinesthetic' || strtolower($student->learning_style) == 'kinesthetic' ? 'selected' : '' }}>Kinesthetic</option>

                            </select>

                            @error('learning_style')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                    </div>


                    <div class="row">
                    <div class="form-group">

                        <label for="goals" class="form-label">

                            <i class="fas fa-target me-2"></i>Learning Goals

                        </label>

                        <textarea class="form-control modern-input @error('goals') is-invalid @enderror" 

                                  id="goals" name="goals" rows="4" 

                                  placeholder="What are your learning objectives? What do you want to achieve?">{{ old('goals', $student->learning_goals) }}</textarea>

                        @error('goals')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>



                    <div class="form-group">

                        <label class="form-label">

                            <i class="fas fa-book me-2"></i>Preferred Subjects

                        </label>

                        <div class="subjects-grid">

                            @php

                                $preferredSubjects = old('preferred_subjects', $student->preferred_subjects ?? []);

                                $subjects = [

                                    'math' => ['icon' => 'fas fa-calculator', 'label' => 'Mathematics'],

                                    'science' => ['icon' => 'fas fa-flask', 'label' => 'Science'],

                                    'english' => ['icon' => 'fas fa-language', 'label' => 'English'],

                                    'history' => ['icon' => 'fas fa-landmark', 'label' => 'History'],

                                    'computer' => ['icon' => 'fas fa-laptop-code', 'label' => 'Computer Science'],

                                    'general' => ['icon' => 'fas fa-graduation-cap', 'label' => 'General Studies']

                                ];

                            @endphp

                            @foreach($subjects as $subject => $data)

                                <div class="subject-item">

                                    <input class="subject-checkbox" type="checkbox" 

                                           id="subject_{{ $subject }}" name="preferred_subjects[]" 

                                           value="{{ $subject }}" 

                                           {{ in_array($subject, $preferredSubjects) ? 'checked' : '' }}>

                                    <label class="subject-label" for="subject_{{ $subject }}">

                                        <div class="subject-icon">

                                            <i class="{{ $data['icon'] }}"></i>

                                        </div>

                                        <span class="subject-name">{{ $data['label'] }}</span>

                                    </label>

                                </div>

                            @endforeach

                        </div>

                        @error('preferred_subjects')

                            <div class="text-danger small">{{ $message }}</div>

                        @enderror

                    </div>

                    <div class="form-group">

                        <label for="timezone" class="form-label">

                            <i class="fas fa-clock me-2"></i>Timezone

                        </label>

                        <select class="form-control modern-input @error('timezone') is-invalid @enderror" 

                                id="timezone" name="timezone" required>

                            <option value="">Select your timezone</option>
                            
                            <!-- UTC -->
                            <option value="UTC" {{ old('timezone', $student->timezone) == 'UTC' ? 'selected' : '' }}>UTC - Coordinated Universal Time</option>
                            
                            <!-- North America -->
                            <optgroup label="🇺🇸 North America">
                                <option value="America/New_York" {{ old('timezone', $student->timezone) == 'America/New_York' ? 'selected' : '' }}>New York (ET - Eastern Time)</option>
                                <option value="America/Chicago" {{ old('timezone', $student->timezone) == 'America/Chicago' ? 'selected' : '' }}>Chicago (CT - Central Time)</option>
                                <option value="America/Denver" {{ old('timezone', $student->timezone) == 'America/Denver' ? 'selected' : '' }}>Denver (MT - Mountain Time)</option>
                                <option value="America/Phoenix" {{ old('timezone', $student->timezone) == 'America/Phoenix' ? 'selected' : '' }}>Phoenix (MST - Mountain Standard)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $student->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Los Angeles (PT - Pacific Time)</option>
                                <option value="America/Anchorage" {{ old('timezone', $student->timezone) == 'America/Anchorage' ? 'selected' : '' }}>Anchorage (AKT - Alaska Time)</option>
                                <option value="Pacific/Honolulu" {{ old('timezone', $student->timezone) == 'Pacific/Honolulu' ? 'selected' : '' }}>Honolulu (HST - Hawaii Standard)</option>
                                <option value="America/Toronto" {{ old('timezone', $student->timezone) == 'America/Toronto' ? 'selected' : '' }}>Toronto (ET - Eastern Time)</option>
                                <option value="America/Vancouver" {{ old('timezone', $student->timezone) == 'America/Vancouver' ? 'selected' : '' }}>Vancouver (PT - Pacific Time)</option>
                            </optgroup>

                            <!-- Europe -->
                            <optgroup label="🇪🇺 Europe">
                                <option value="Europe/London" {{ old('timezone', $student->timezone) == 'Europe/London' ? 'selected' : '' }}>London (GMT/BST)</option>
                                <option value="Europe/Paris" {{ old('timezone', $student->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Paris (CET/CEST)</option>
                                <option value="Europe/Berlin" {{ old('timezone', $student->timezone) == 'Europe/Berlin' ? 'selected' : '' }}>Berlin (CET/CEST)</option>
                                <option value="Europe/Rome" {{ old('timezone', $student->timezone) == 'Europe/Rome' ? 'selected' : '' }}>Rome (CET/CEST)</option>
                                <option value="Europe/Madrid" {{ old('timezone', $student->timezone) == 'Europe/Madrid' ? 'selected' : '' }}>Madrid (CET/CEST)</option>
                                <option value="Europe/Amsterdam" {{ old('timezone', $student->timezone) == 'Europe/Amsterdam' ? 'selected' : '' }}>Amsterdam (CET/CEST)</option>
                                <option value="Europe/Brussels" {{ old('timezone', $student->timezone) == 'Europe/Brussels' ? 'selected' : '' }}>Brussels (CET/CEST)</option>
                                <option value="Europe/Zurich" {{ old('timezone', $student->timezone) == 'Europe/Zurich' ? 'selected' : '' }}>Zurich (CET/CEST)</option>
                                <option value="Europe/Athens" {{ old('timezone', $student->timezone) == 'Europe/Athens' ? 'selected' : '' }}>Athens (EET/EEST)</option>
                                <option value="Europe/Moscow" {{ old('timezone', $student->timezone) == 'Europe/Moscow' ? 'selected' : '' }}>Moscow (MSK)</option>
                            </optgroup>

                            <!-- Asia -->
                            <optgroup label="🌏 Asia">
                                <option value="Asia/Dubai" {{ old('timezone', $student->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>Dubai (GST - Gulf Standard)</option>
                                <option value="Asia/Karachi" {{ old('timezone', $student->timezone) == 'Asia/Karachi' ? 'selected' : '' }}>Karachi/Lahore (PKT)</option>
                                <option value="Asia/Kolkata" {{ old('timezone', $student->timezone) == 'Asia/Kolkata' ? 'selected' : '' }}>Mumbai/Delhi (IST - India Standard)</option>
                                <option value="Asia/Dhaka" {{ old('timezone', $student->timezone) == 'Asia/Dhaka' ? 'selected' : '' }}>Dhaka (BST - Bangladesh Standard)</option>
                                <option value="Asia/Bangkok" {{ old('timezone', $student->timezone) == 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok (ICT - Indochina Time)</option>
                                <option value="Asia/Singapore" {{ old('timezone', $student->timezone) == 'Asia/Singapore' ? 'selected' : '' }}>Singapore (SGT)</option>
                                <option value="Asia/Hong_Kong" {{ old('timezone', $student->timezone) == 'Asia/Hong_Kong' ? 'selected' : '' }}>Hong Kong (HKT)</option>
                                <option value="Asia/Shanghai" {{ old('timezone', $student->timezone) == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai/Beijing (CST - China Standard)</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $student->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST - Japan Standard)</option>
                                <option value="Asia/Seoul" {{ old('timezone', $student->timezone) == 'Asia/Seoul' ? 'selected' : '' }}>Seoul (KST - Korea Standard)</option>
                                <option value="Asia/Manila" {{ old('timezone', $student->timezone) == 'Asia/Manila' ? 'selected' : '' }}>Manila (PHT - Philippine Time)</option>
                                <option value="Asia/Jakarta" {{ old('timezone', $student->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta (WIB - Western Indonesia)</option>
                            </optgroup>

                            <!-- Australia & Pacific -->
                            <optgroup label="🇦🇺 Australia & Pacific">
                                <option value="Australia/Perth" {{ old('timezone', $student->timezone) == 'Australia/Perth' ? 'selected' : '' }}>Perth (AWST - Australian Western Standard)</option>
                                <option value="Australia/Adelaide" {{ old('timezone', $student->timezone) == 'Australia/Adelaide' ? 'selected' : '' }}>Adelaide (ACST/ACDT - Australian Central)</option>
                                <option value="Australia/Brisbane" {{ old('timezone', $student->timezone) == 'Australia/Brisbane' ? 'selected' : '' }}>Brisbane (AEST - Australian Eastern Standard)</option>
                                <option value="Australia/Sydney" {{ old('timezone', $student->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEST/AEDT - Australian Eastern)</option>
                                <option value="Australia/Melbourne" {{ old('timezone', $student->timezone) == 'Australia/Melbourne' ? 'selected' : '' }}>Melbourne (AEST/AEDT)</option>
                                <option value="Australia/Hobart" {{ old('timezone', $student->timezone) == 'Australia/Hobart' ? 'selected' : '' }}>Hobart (AEST/AEDT)</option>
                                <option value="Pacific/Auckland" {{ old('timezone', $student->timezone) == 'Pacific/Auckland' ? 'selected' : '' }}>Auckland (NZST/NZDT)</option>
                                <option value="Pacific/Fiji" {{ old('timezone', $student->timezone) == 'Pacific/Fiji' ? 'selected' : '' }}>Fiji (FJT - Fiji Time)</option>
                            </optgroup>

                            <!-- South America -->
                            <optgroup label="🌎 South America">
                                <option value="America/Sao_Paulo" {{ old('timezone', $student->timezone) == 'America/Sao_Paulo' ? 'selected' : '' }}>São Paulo (BRT - Brazil Time)</option>
                                <option value="America/Buenos_Aires" {{ old('timezone', $student->timezone) == 'America/Buenos_Aires' ? 'selected' : '' }}>Buenos Aires (ART - Argentina Time)</option>
                                <option value="America/Santiago" {{ old('timezone', $student->timezone) == 'America/Santiago' ? 'selected' : '' }}>Santiago (CLT - Chile Time)</option>
                                <option value="America/Lima" {{ old('timezone', $student->timezone) == 'America/Lima' ? 'selected' : '' }}>Lima (PET - Peru Time)</option>
                                <option value="America/Bogota" {{ old('timezone', $student->timezone) == 'America/Bogota' ? 'selected' : '' }}>Bogotá (COT - Colombia Time)</option>
                            </optgroup>

                            <!-- Africa -->
                            <optgroup label="🌍 Africa">
                                <option value="Africa/Cairo" {{ old('timezone', $student->timezone) == 'Africa/Cairo' ? 'selected' : '' }}>Cairo (EET - Eastern European)</option>
                                <option value="Africa/Johannesburg" {{ old('timezone', $student->timezone) == 'Africa/Johannesburg' ? 'selected' : '' }}>Johannesburg (SAST - South Africa)</option>
                                <option value="Africa/Lagos" {{ old('timezone', $student->timezone) == 'Africa/Lagos' ? 'selected' : '' }}>Lagos (WAT - West Africa Time)</option>
                                <option value="Africa/Nairobi" {{ old('timezone', $student->timezone) == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi (EAT - East Africa Time)</option>
                            </optgroup>

                        </select>

                        @error('timezone')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>
                    



                    <div class="form-actions">

                        <button type="submit" class="btn btn-primary btn-lg">

                            <i class="fas fa-save me-2"></i>Update Profile

                        </button>

                    </div>
                    </div>

                </form>

            </div>

        </div>



        <!-- Change Password -->

        <div class="modern-card">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-key me-2"></i>Change Password

                </h5>

                <p class="modern-card-subtitle">Update your account password</p>

            </div>

            <div class="modern-card-body">

                <form method="POST" action="{{ route('student.profile.password') }}" class="password-form">

                    @csrf

                    @method('PUT')

                    

                    <div class="form-group">

                        <label for="current_password" class="form-label">

                            <i class="fas fa-lock me-2"></i>Current Password

                        </label>

                        <div class="input-group">

                            <input type="password" class="form-control modern-input @error('current_password') is-invalid @enderror" 

                                   id="current_password" name="current_password" required>

                            <button type="button" class="input-group-text" onclick="togglePasswordField('current_password', 'current_password_icon')">

                                <i class="fas fa-eye" id="current_password_icon"></i>

                            </button>

                        </div>

                        @error('current_password')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>



                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="password" class="form-label">

                                <i class="fas fa-key me-2"></i>New Password

                            </label>

                            <div class="input-group">

                                <input type="password" class="form-control modern-input @error('password') is-invalid @enderror" 

                                       id="password" name="password" required>

                                <button type="button" class="input-group-text" onclick="togglePasswordField('password', 'password_icon')">

                                    <i class="fas fa-eye" id="password_icon"></i>

                                </button>

                            </div>

                            @error('password')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label for="password_confirmation" class="form-label">

                                <i class="fas fa-check-circle me-2"></i>Confirm New Password

                            </label>

                            <div class="input-group">

                                <input type="password" class="form-control modern-input" 

                                       id="password_confirmation" name="password_confirmation" required>

                                <button type="button" class="input-group-text" onclick="togglePasswordField('password_confirmation', 'password_confirmation_icon')">

                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>

                                </button>

                            </div>

                        </div>

                    </div>



                    <div class="form-actions">

                        <button type="submit" class="btn btn-warning btn-lg">

                            <i class="fas fa-key me-2"></i>Change Password

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection



@section('styles')

<style>

    /* Page Header */

    .page-header {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        border-radius: 16px;

        padding: 2rem;

        color: white;

        margin-bottom: 2rem;

        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);

    }

    

    .page-title {

        font-size: 2.5rem;

        font-weight: 700;

        margin: 0;

        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

    }

    

    .page-subtitle {

        font-size: 1.1rem;

        opacity: 0.9;

        margin: 0.5rem 0 0 0;

    }

    

    .profile-status {

        display: flex;

        align-items: center;

        gap: 1rem;

    }

    

    /* Modern Card */

    .modern-card {

        background: white;

        border-radius: 16px;

        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);

        border: none;

        overflow: hidden;

        margin-bottom: 1.5rem;

    }

    

    .modern-card-header {

        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);

        padding: 1.5rem;

        border-bottom: 1px solid #e9ecef;

    }

    

    .modern-card-title {

        font-size: 1.25rem;

        font-weight: 600;

        color: #2c3e50;

        margin: 0;

    }

    

    .modern-card-subtitle {

        font-size: 0.875rem;

        color: #6c757d;

        margin: 0.25rem 0 0 0;

    }

    

    .modern-card-body {

        padding: 1.5rem;

    }

    

    /* Profile Picture */

    .profile-picture-container {

        display: flex;

        justify-content: center;

        margin-bottom: 1rem;

    }

    

    .profile-picture-wrapper {

        position: relative;

        width: 150px;

        height: 150px;

        border-radius: 50%;

        overflow: hidden;

        cursor: pointer;

        transition: all 0.3s ease;

        border: 4px solid white;

        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);

    }

    

    .profile-picture-wrapper:hover {

        transform: scale(1.05);

        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);

    }

    

    .profile-picture {

        width: 100%;

        height: 100%;

        object-fit: cover;

        transition: all 0.3s ease;

    }

    

    .profile-picture-overlay {

        position: absolute;

        top: 0;

        left: 0;

        right: 0;

        bottom: 0;

        background: rgba(0, 0, 0, 0.5);

        display: flex;

        align-items: center;

        justify-content: center;

        opacity: 0;

        transition: all 0.3s ease;

        color: white;

        font-size: 1.5rem;

    }

    

    .profile-picture-wrapper:hover .profile-picture-overlay {

        opacity: 1;

    }

    

    .profile-picture-text {

        font-weight: 500;

        color: #2c3e50;

        margin-bottom: 0.5rem;

    }

    

    /* Stats Grid */

    .stats-grid {

        display: grid;

        grid-template-columns: 1fr 1fr;

        gap: 1rem;

        margin-bottom: 2rem;

    }

    

    .stat-item {

        display: flex;

        align-items: center;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border-radius: 12px;

        border-left: 4px solid #ef473e;

    }

    

    .stat-icon {

        width: 50px;

        height: 50px;

        border-radius: 50%;

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 1.25rem;

    }

    

    .stat-content {

        flex: 1;

    }

    

    .stat-value {

        font-size: 1.5rem;

        font-weight: 700;

        color: #2c3e50;

        margin-bottom: 0.25rem;

    }

    

    .stat-label {

        font-size: 0.875rem;

        color: #6c757d;

        font-weight: 500;

    }

    

    /* Profile Info */

    .profile-info {

        display: flex;

        flex-direction: column;

        gap: 1rem;

    }

    

    .info-item {

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding: 0.75rem 0;

        border-bottom: 1px solid #f1f3f4;

    }

    

    .info-item:last-child {

        border-bottom: none;

    }

    

    .info-label {

        font-weight: 600;

        color: #2c3e50;

        display: flex;

        align-items: center;

    }

    

    .info-value {

        font-weight: 500;

        color: #6c757d;

    }

    

    /* Status Badges */

    .status-badge {

        display: inline-flex;

        align-items: center;

        padding: 0.375rem 0.75rem;

        border-radius: 15px;

        font-size: 0.7rem;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.5px;

    }

    

    .status-student {

        background: linear-gradient(135deg, #28a745, #1e7e34);

        color: white;

    }

    

    /* Level Badges */

    .level-badge {

        display: inline-flex;

        align-items: center;

        padding: 0.375rem 0.75rem;

        border-radius: 15px;

        font-size: 0.7rem;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: 0.5px;

    }

    

    .level-beginner {

        background: #d4edda;

        color: #155724;

    }

    

    .level-intermediate {

        background: #fff3cd;

        color: #856404;

    }

    

    .level-advanced {

        background: #f8d7da;

        color: #721c24;

    }

    

    /* Form Styles */

    .profile-form, .password-form {

        max-width: 100%;

    }

    

    .form-row {

        display: grid;

        grid-template-columns: 1fr 1fr;

        gap: 1.5rem;

        margin-bottom: 1.5rem;

    }

    

    .form-group {

        margin-bottom: 1.5rem;

    }

    

    .form-label {

        font-weight: 600;

        color: #2c3e50;

        margin-bottom: 0.75rem;

        display: block;

        font-size: 0.95rem;

    }

    

    .form-label i {

        color: #ef473e;

    }

    

    .modern-input, .modern-select {

        border: 2px solid #e9ecef;

        border-radius: 12px;

        padding: 0.875rem 1rem;

        font-size: 0.95rem;

        transition: all 0.3s ease;

        background: #fafafa;

    }

    

    .modern-input:focus, .modern-select:focus {

        border-color: #ef473e;

        box-shadow: 0 0 0 0.2rem rgba(239, 71, 62, 0.15);

        background: white;

        outline: none;

    }

    

    .modern-input::placeholder {

        color: #adb5bd;

    }

    

    .form-text {

        font-size: 0.8rem;

        color: #6c757d;

        margin-top: 0.5rem;

    }

    

    /* Subjects Grid */

    .subjects-grid {

        display: grid;

        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

        gap: 1rem;

        margin-top: 1rem;

    }

    

    .subject-item {

        position: relative;

    }

    

    .subject-checkbox {

        position: absolute;

        opacity: 0;

        pointer-events: none;

    }

    

    .subject-label {

        display: flex;

        align-items: center;

        gap: 1rem;

        padding: 1rem;

        background: #f8f9fa;

        border: 2px solid #e9ecef;

        border-radius: 12px;

        cursor: pointer;

        transition: all 0.3s ease;

        font-weight: 500;

        color: #2c3e50;

    }

    

    .subject-label:hover {

        background: #e9ecef;

        border-color: #ef473e;

    }

    

    .subject-checkbox:checked + .subject-label {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        color: white;

        border-color: #ef473e;

    }

    

    .subject-icon {

        width: 40px;

        height: 40px;

        border-radius: 50%;

        background: rgba(255, 255, 255, 0.2);

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 1.25rem;

    }

    

    .subject-checkbox:checked + .subject-label .subject-icon {

        background: rgba(255, 255, 255, 0.3);

    }

    

    .subject-name {

        font-weight: 600;

    }

    

    .form-actions {

        margin-top: 2rem;

        padding-top: 1.5rem;

        border-top: 1px solid #e9ecef;

    }

    

    .btn-lg {

        padding: 0.875rem 2rem;

        font-size: 1rem;

        font-weight: 600;

        border-radius: 12px;

        transition: all 0.3s ease;

    }

    

    .btn-primary {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

        border: none;

        color: white;

    }

    

    .btn-primary:hover {

        background: linear-gradient(135deg, #ef473e 0%, #fdb838 100%);

        transform: translateY(-2px);

        box-shadow: 0 8px 25px rgba(239, 71, 62, 0.3);

        color: white;

    }

    

    .btn-warning {

        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);

        border: none;

        color: white;

    }

    

    .btn-warning:hover {

        background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);

        transform: translateY(-2px);

        box-shadow: 0 8px 25px rgba(253, 126, 20, 0.3);

        color: white;

    }

    

    /* Responsive */

    @media (max-width: 768px) {

        .page-title {

            font-size: 2rem;

        }

        

        .modern-card-body {

            padding: 1rem;

        }

        

        .form-row {

            grid-template-columns: 1fr;

            gap: 1rem;

        }

        

        .stats-grid {

            grid-template-columns: 1fr;

        }

        

        .stat-item {

            flex-direction: column;

            text-align: center;

            gap: 0.5rem;

        }

        

        .profile-info {

            gap: 0.75rem;

        }

        

        .info-item {

            flex-direction: column;

            align-items: flex-start;

            gap: 0.5rem;

        }

        

        .profile-picture-wrapper {

            width: 120px;

            height: 120px;

        }

        

        .subjects-grid {

            grid-template-columns: 1fr;

        }

    }

</style>

@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Profile picture functionality is now handled by the component

    

    // Form validation

    const forms = document.querySelectorAll('.profile-form, .password-form');

    forms.forEach(form => {

        form.addEventListener('submit', function(e) {

            const submitBtn = form.querySelector('button[type="submit"]');

            const originalText = submitBtn.innerHTML;

            

            // Show loading state

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            submitBtn.disabled = true;

            

            // Re-enable after 3 seconds (in case of errors)

            setTimeout(() => {

                submitBtn.innerHTML = originalText;

                submitBtn.disabled = false;

            }, 3000);

        });

    });

    

    // Auto-save draft functionality

    const formInputs = document.querySelectorAll('.modern-input, .modern-select, .subject-checkbox');

    formInputs.forEach(input => {

        input.addEventListener('input', function() {

            // Save to localStorage as draft

            const formData = new FormData(document.querySelector('.profile-form'));

            const data = {};

            for (let [key, value] of formData.entries()) {

                if (data[key]) {

                    if (Array.isArray(data[key])) {

                        data[key].push(value);

                    } else {

                        data[key] = [data[key], value];

                    }

                } else {

                    data[key] = value;

                }

            }

            localStorage.setItem('studentProfileDraft', JSON.stringify(data));

        });

        

        input.addEventListener('change', function() {

            // Save to localStorage as draft

            const formData = new FormData(document.querySelector('.profile-form'));

            const data = {};

            for (let [key, value] of formData.entries()) {

                if (data[key]) {

                    if (Array.isArray(data[key])) {

                        data[key].push(value);

                    } else {

                        data[key] = [data[key], value];

                    }

                } else {

                    data[key] = value;

                }

            }

            localStorage.setItem('studentProfileDraft', JSON.stringify(data));

        });

    });

    

    // Load draft on page load

    const draft = localStorage.getItem('studentProfileDraft');

    if (draft) {

        try {

            const data = JSON.parse(draft);

            Object.keys(data).forEach(key => {

                const input = document.querySelector(`[name="${key}"]`);

                if (input && input.type !== 'file') {

                    if (input.type === 'checkbox') {

                        input.checked = Array.isArray(data[key]) ? data[key].includes(input.value) : data[key] === input.value;

                    } else {

                        input.value = Array.isArray(data[key]) ? data[key][0] : data[key];

                    }

                }

            });

        } catch (e) {

            console.log('Error loading draft:', e);

        }

    }

    

    // Clear draft on successful form submission

    document.querySelector('.profile-form').addEventListener('submit', function() {

        localStorage.removeItem('studentProfileDraft');

    });

});

</script>

<script>

function togglePasswordField(inputId, iconId) {

    const input = document.getElementById(inputId);

    const icon = document.getElementById(iconId);

    if (!input || !icon) return;

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('fa-eye');

        icon.classList.add('fa-eye-slash');

    } else {

        input.type = 'password';

        icon.classList.remove('fa-eye-slash');

        icon.classList.add('fa-eye');

    }

}

</script>

<script src="{{ asset('js/phone-formatter.js') }}"></script>

@endsection

