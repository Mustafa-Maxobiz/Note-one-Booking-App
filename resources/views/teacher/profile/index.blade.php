@extends('layouts.app')



@section('title', 'Teacher Profile')



@section('content')

<!-- Page Header -->

<div class="page-header mb-4">

    <div class="row align-items-center">

        <div class="col-md-8">

            <h1 class="page-title">

                <i class="fas fa-user-edit me-3"></i>Teacher Profile

            </h1>

            <p class="page-subtitle">Manage your profile information, bio, and teaching preferences</p>

        </div>

        <div class="col-md-4 text-end">

            <div class="profile-status" id="verificationStatus">

                @if($teacher->is_verified)

                    <span class="status-badge status-verified">

                        <i class="fas fa-certificate me-1"></i>Verified Teacher

                    </span>

                @else

                    <span class="status-badge status-unverified">

                        <i class="fas fa-exclamation-triangle me-1"></i>Pending Verification

                    </span>

                @endif

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
                    $teacherPicture = auth()->user()->small_profile_picture_url
                        ? auth()->user()->small_profile_picture_url
                        : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->user->name) . '&background=007bff&color=fff&size=200';
                @endphp
                <img src="{{ $teacherPicture }}" alt="{{ $teacher->user->name }}" class="profile-picture" id="profilePicturePreview">
                <p class="profile-picture-text">You can update your profile picture below.</p>
            </div>

        </div>



        <!-- Teacher Stats -->

        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h5 class="modern-card-title">

                    <i class="fas fa-chart-line me-2"></i>Teaching Statistics

                </h5>

                <p class="modern-card-subtitle">Your teaching performance</p>

            </div>

            <div class="modern-card-body p-1">

                <div class="stats-grid">

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-primary">

                            <i class="fas fa-calendar-alt"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $teacher->bookings()->count() }}</div>

                            <div class="stat-label">Total Sessions</div>

                        </div>

                    </div>

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-success">

                            <i class="fas fa-check-circle"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $teacher->bookings()->where('status', 'completed')->count() }}</div>

                            <div class="stat-label">Completed</div>

                        </div>

                    </div>

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-warning">

                            <i class="fas fa-clock"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $teacher->bookings()->where('status', 'pending')->count() }}</div>

                            <div class="stat-label">Pending</div>

                        </div>

                    </div>

                    <div class="stat-item">

                        <div class="stat-icon stat-icon-info">

                            <i class="fas fa-calendar-check"></i>

                        </div>

                        <div class="stat-content">

                            <div class="stat-value">{{ $teacher->bookings()->where('status', 'confirmed')->count() }}</div>

                            <div class="stat-label">Confirmed</div>

                        </div>

                    </div>

                </div>

                

                <div class="profile-info">

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-graduation-cap me-2"></i>Experience

                        </div>

                        <div class="info-value">{{ $teacher->experience_years }} years</div>

                    </div>

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-calendar-plus me-2"></i>Member Since

                        </div>

                        <div class="info-value">{{ $teacher->user->created_at->format('M d, Y') }}</div>

                    </div>

                    <div class="info-item">

                        <div class="info-label">

                            <i class="fas fa-clock me-2"></i>Availability

                        </div>

                        <div class="info-value" id="availabilityStatus">

                            @if($teacher->is_available)

                                <span class="status-badge status-available">

                                    <i class="fas fa-check me-1"></i>Available

                                </span>

                            @else

                                <span class="status-badge status-unavailable">

                                    <i class="fas fa-pause me-1"></i>Unavailable

                                </span>

                            @endif

                        </div>

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

                <p class="modern-card-subtitle">Update your personal and professional details</p>

            </div>

            <div class="modern-card-body">

                <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data" class="profile-form">

                    @csrf

                    @method('PUT')

                    

                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="name" class="form-label">

                                <i class="fas fa-user me-2"></i>Full Name

                            </label>

                            <input type="text" class="form-control modern-input @error('name') is-invalid @enderror" 

                                   id="name" name="name" value="{{ old('name', $teacher->user->name) }}" required>

                            @error('name')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label for="email" class="form-label">

                                <i class="fas fa-envelope me-2"></i>Email Address

                            </label>

                            <input type="email" class="form-control modern-input @error('email') is-invalid @enderror" 

                                   id="email" name="email" value="{{ old('email', $teacher->user->email) }}" required>

                            @error('email')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                    </div>



                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="phone" class="form-label">

                                <i class="fas fa-phone me-2"></i>Phone Number <span class="text-danger">*</span>

                            </label>

                            <input type="text" class="form-control modern-input @error('phone') is-invalid @enderror" 

                                   id="phone" name="phone" value="{{ old('phone', $teacher->user->phone) }}" 

                                   placeholder="e.g., +1 (555) 123-4567" required>

                            @error('phone')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                            <div class="form-text">

                                <i class="fas fa-globe me-1"></i>International format accepted

                            </div>

                        </div>

                        

                        <div class="form-group col-md-6">
                            <label class="form-label">
                                <i class="fas fa-image me-2"></i>Profile Picture
                            </label>
                            @php
                                $teacherPictureForm = auth()->user()->small_profile_picture_url
                                    ? auth()->user()->small_profile_picture_url
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->user->name) . '&background=007bff&color=fff&size=200';
                            @endphp
                            @include('components.profile-picture-upload', ['currentPicture' => $teacherPictureForm])
                        </div>

                    </div>



                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="qualifications" class="form-label">

                                <i class="fas fa-graduation-cap me-2"></i>Qualifications <span class="text-danger">*</span>

                            </label>

                            <input type="text" class="form-control modern-input @error('qualifications') is-invalid @enderror" 

                                   id="qualifications" name="qualifications" 

                                   value="{{ old('qualifications', $teacher->qualifications) }}"

                                   placeholder="e.g., Master's in Education, TEFL Certificate" required>

                            @error('qualifications')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                        <div class="form-group col-md-6">

                            <label for="experience_years" class="form-label">

                                <i class="fas fa-calendar-alt me-2"></i>Years of Experience <span class="text-danger">*</span>

                            </label>

                            <input type="number" class="form-control modern-input @error('experience_years') is-invalid @enderror" 

                                   id="experience_years" name="experience_years" 

                                   value="{{ old('experience_years', $teacher->experience_years) }}"

                                   min="0" max="50" placeholder="Years of teaching experience" required>

                            @error('experience_years')

                                <div class="invalid-feedback">{{ $message }}</div>

                            @enderror

                        </div>

                    </div>


                    <div class="row">
                    <div class="form-group">

                        <label for="bio" class="form-label">

                            <i class="fas fa-user-circle me-2"></i>Bio <span class="text-danger">*</span>

                        </label>

                        <textarea class="form-control modern-input @error('bio') is-invalid @enderror" 

                                  id="bio" name="bio" rows="4" 

                                  placeholder="Tell students about your teaching experience, background, and what makes you unique..." required>{{ old('bio', $teacher->bio) }}</textarea>

                        @error('bio')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>



                    <div class="form-group">

                        <label for="teaching_style" class="form-label">

                            <i class="fas fa-chalkboard-teacher me-2"></i>Teaching Style <span class="text-danger">*</span>

                        </label>

                        <textarea class="form-control modern-input @error('teaching_style') is-invalid @enderror" 

                                  id="teaching_style" name="teaching_style" rows="3" 

                                  placeholder="Describe your teaching methodology, approach, and how you help students learn..." required>{{ old('teaching_style', $teacher->teaching_style) }}</textarea>

                        @error('teaching_style')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>

                    <div class="form-group">

                        <label for="timezone" class="form-label">

                            <i class="fas fa-globe me-2"></i>Timezone <span class="text-danger">*</span>

                        </label>

                        <select class="form-control modern-input @error('timezone') is-invalid @enderror" 

                                id="timezone" name="timezone" required>

                            <option value="">Select your timezone</option>
                            
                            <!-- UTC -->
                            <option value="UTC" {{ old('timezone', $teacher->timezone) == 'UTC' ? 'selected' : '' }}>UTC - Coordinated Universal Time</option>
                            
                            <!-- North America -->
                            <optgroup label="🇺🇸 North America">
                                <option value="America/New_York" {{ old('timezone', $teacher->timezone) == 'America/New_York' ? 'selected' : '' }}>New York (ET - Eastern Time)</option>
                                <option value="America/Chicago" {{ old('timezone', $teacher->timezone) == 'America/Chicago' ? 'selected' : '' }}>Chicago (CT - Central Time)</option>
                                <option value="America/Denver" {{ old('timezone', $teacher->timezone) == 'America/Denver' ? 'selected' : '' }}>Denver (MT - Mountain Time)</option>
                                <option value="America/Phoenix" {{ old('timezone', $teacher->timezone) == 'America/Phoenix' ? 'selected' : '' }}>Phoenix (MST - Mountain Standard)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $teacher->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Los Angeles (PT - Pacific Time)</option>
                                <option value="America/Anchorage" {{ old('timezone', $teacher->timezone) == 'America/Anchorage' ? 'selected' : '' }}>Anchorage (AKT - Alaska Time)</option>
                                <option value="Pacific/Honolulu" {{ old('timezone', $teacher->timezone) == 'Pacific/Honolulu' ? 'selected' : '' }}>Honolulu (HST - Hawaii Standard)</option>
                                <option value="America/Toronto" {{ old('timezone', $teacher->timezone) == 'America/Toronto' ? 'selected' : '' }}>Toronto (ET - Eastern Time)</option>
                                <option value="America/Vancouver" {{ old('timezone', $teacher->timezone) == 'America/Vancouver' ? 'selected' : '' }}>Vancouver (PT - Pacific Time)</option>
                            </optgroup>

                            <!-- Europe -->
                            <optgroup label="🇪🇺 Europe">
                                <option value="Europe/London" {{ old('timezone', $teacher->timezone) == 'Europe/London' ? 'selected' : '' }}>London (GMT/BST)</option>
                                <option value="Europe/Paris" {{ old('timezone', $teacher->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Paris (CET/CEST)</option>
                                <option value="Europe/Berlin" {{ old('timezone', $teacher->timezone) == 'Europe/Berlin' ? 'selected' : '' }}>Berlin (CET/CEST)</option>
                                <option value="Europe/Rome" {{ old('timezone', $teacher->timezone) == 'Europe/Rome' ? 'selected' : '' }}>Rome (CET/CEST)</option>
                                <option value="Europe/Madrid" {{ old('timezone', $teacher->timezone) == 'Europe/Madrid' ? 'selected' : '' }}>Madrid (CET/CEST)</option>
                                <option value="Europe/Amsterdam" {{ old('timezone', $teacher->timezone) == 'Europe/Amsterdam' ? 'selected' : '' }}>Amsterdam (CET/CEST)</option>
                                <option value="Europe/Brussels" {{ old('timezone', $teacher->timezone) == 'Europe/Brussels' ? 'selected' : '' }}>Brussels (CET/CEST)</option>
                                <option value="Europe/Zurich" {{ old('timezone', $teacher->timezone) == 'Europe/Zurich' ? 'selected' : '' }}>Zurich (CET/CEST)</option>
                                <option value="Europe/Athens" {{ old('timezone', $teacher->timezone) == 'Europe/Athens' ? 'selected' : '' }}>Athens (EET/EEST)</option>
                                <option value="Europe/Moscow" {{ old('timezone', $teacher->timezone) == 'Europe/Moscow' ? 'selected' : '' }}>Moscow (MSK)</option>
                            </optgroup>

                            <!-- Asia -->
                            <optgroup label="🌏 Asia">
                                <option value="Asia/Dubai" {{ old('timezone', $teacher->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>Dubai (GST - Gulf Standard)</option>
                                <option value="Asia/Karachi" {{ old('timezone', $teacher->timezone) == 'Asia/Karachi' ? 'selected' : '' }}>Karachi/Lahore (PKT)</option>
                                <option value="Asia/Kolkata" {{ old('timezone', $teacher->timezone) == 'Asia/Kolkata' ? 'selected' : '' }}>Mumbai/Delhi (IST - India Standard)</option>
                                <option value="Asia/Dhaka" {{ old('timezone', $teacher->timezone) == 'Asia/Dhaka' ? 'selected' : '' }}>Dhaka (BST - Bangladesh Standard)</option>
                                <option value="Asia/Bangkok" {{ old('timezone', $teacher->timezone) == 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok (ICT - Indochina Time)</option>
                                <option value="Asia/Singapore" {{ old('timezone', $teacher->timezone) == 'Asia/Singapore' ? 'selected' : '' }}>Singapore (SGT)</option>
                                <option value="Asia/Hong_Kong" {{ old('timezone', $teacher->timezone) == 'Asia/Hong_Kong' ? 'selected' : '' }}>Hong Kong (HKT)</option>
                                <option value="Asia/Shanghai" {{ old('timezone', $teacher->timezone) == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai/Beijing (CST - China Standard)</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $teacher->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST - Japan Standard)</option>
                                <option value="Asia/Seoul" {{ old('timezone', $teacher->timezone) == 'Asia/Seoul' ? 'selected' : '' }}>Seoul (KST - Korea Standard)</option>
                                <option value="Asia/Manila" {{ old('timezone', $teacher->timezone) == 'Asia/Manila' ? 'selected' : '' }}>Manila (PHT - Philippine Time)</option>
                                <option value="Asia/Jakarta" {{ old('timezone', $teacher->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta (WIB - Western Indonesia)</option>
                            </optgroup>

                            <!-- Australia & Pacific -->
                            <optgroup label="🇦🇺 Australia & Pacific">
                                <option value="Australia/Perth" {{ old('timezone', $teacher->timezone) == 'Australia/Perth' ? 'selected' : '' }}>Perth (AWST - Australian Western Standard)</option>
                                <option value="Australia/Adelaide" {{ old('timezone', $teacher->timezone) == 'Australia/Adelaide' ? 'selected' : '' }}>Adelaide (ACST/ACDT - Australian Central)</option>
                                <option value="Australia/Brisbane" {{ old('timezone', $teacher->timezone) == 'Australia/Brisbane' ? 'selected' : '' }}>Brisbane (AEST - Australian Eastern Standard)</option>
                                <option value="Australia/Sydney" {{ old('timezone', $teacher->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEST/AEDT - Australian Eastern)</option>
                                <option value="Australia/Melbourne" {{ old('timezone', $teacher->timezone) == 'Australia/Melbourne' ? 'selected' : '' }}>Melbourne (AEST/AEDT)</option>
                                <option value="Australia/Hobart" {{ old('timezone', $teacher->timezone) == 'Australia/Hobart' ? 'selected' : '' }}>Hobart (AEST/AEDT)</option>
                                <option value="Pacific/Auckland" {{ old('timezone', $teacher->timezone) == 'Pacific/Auckland' ? 'selected' : '' }}>Auckland (NZST/NZDT)</option>
                                <option value="Pacific/Fiji" {{ old('timezone', $teacher->timezone) == 'Pacific/Fiji' ? 'selected' : '' }}>Fiji (FJT - Fiji Time)</option>
                            </optgroup>

                            <!-- South America -->
                            <optgroup label="🌎 South America">
                                <option value="America/Sao_Paulo" {{ old('timezone', $teacher->timezone) == 'America/Sao_Paulo' ? 'selected' : '' }}>São Paulo (BRT - Brazil Time)</option>
                                <option value="America/Buenos_Aires" {{ old('timezone', $teacher->timezone) == 'America/Buenos_Aires' ? 'selected' : '' }}>Buenos Aires (ART - Argentina Time)</option>
                                <option value="America/Santiago" {{ old('timezone', $teacher->timezone) == 'America/Santiago' ? 'selected' : '' }}>Santiago (CLT - Chile Time)</option>
                                <option value="America/Lima" {{ old('timezone', $teacher->timezone) == 'America/Lima' ? 'selected' : '' }}>Lima (PET - Peru Time)</option>
                                <option value="America/Bogota" {{ old('timezone', $teacher->timezone) == 'America/Bogota' ? 'selected' : '' }}>Bogotá (COT - Colombia Time)</option>
                            </optgroup>

                            <!-- Africa -->
                            <optgroup label="🌍 Africa">
                                <option value="Africa/Cairo" {{ old('timezone', $teacher->timezone) == 'Africa/Cairo' ? 'selected' : '' }}>Cairo (EET - Eastern European)</option>
                                <option value="Africa/Johannesburg" {{ old('timezone', $teacher->timezone) == 'Africa/Johannesburg' ? 'selected' : '' }}>Johannesburg (SAST - South Africa)</option>
                                <option value="Africa/Lagos" {{ old('timezone', $teacher->timezone) == 'Africa/Lagos' ? 'selected' : '' }}>Lagos (WAT - West Africa Time)</option>
                                <option value="Africa/Nairobi" {{ old('timezone', $teacher->timezone) == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi (EAT - East Africa Time)</option>
                            </optgroup>

                        </select>

                        @error('timezone')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>

                    <!-- Availability Toggle -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-toggle-on me-2"></i>Availability Status
                        </label>
                        <div class="availability-toggle-section">
                            <div class="availability-info">
                                <div class="availability-status">
                                    <strong>Current Status:</strong>
                                    @if($teacher->is_available)
                                        <span class="status-badge status-available">
                                            <i class="fas fa-check me-1"></i>Available for Bookings
                                        </span>
                                    @else
                                        <span class="status-badge status-unavailable">
                                            <i class="fas fa-pause me-1"></i>Not Available
                                        </span>
                                    @endif
                                </div>
                                <p class="availability-description">
                                    When available, students can see your profile and book sessions with you. 
                                    For detailed schedule management, use the 
                                    <a href="{{ route('teacher.availability.index') }}" class="text-primary">
                                        <i class="fas fa-calendar me-1"></i>Availability Schedule
                                    </a> page.
                                </p>
                            </div>
                            <a href="{{ route('teacher.availability.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Manage Schedule
                            </a>
                        </div>
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

                <form method="POST" action="{{ route('teacher.profile.password') }}" class="password-form">

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

        transition: all 0.3s ease;

    }

    

    .stat-item:nth-child(1) {

        border-left: 4px solid #ef473e;

    }

    

    .stat-item:nth-child(2) {

        border-left: 4px solid #28a745;

    }

    

    .stat-item:nth-child(3) {

        border-left: 4px solid #ffc107;

    }

    

    .stat-item:nth-child(4) {

        border-left: 4px solid #17a2b8;

    }

    

    .stat-item:hover {

        transform: translateY(-2px);

        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

    }

    

    .stat-icon {

        width: 50px;

        height: 50px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        font-size: 1.25rem;

    }

    

    .stat-icon-primary {

        background: linear-gradient(135deg, #fdb838 0%, #ef473e 100%);

    }

    

    .stat-icon-success {

        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);

    }

    

    .stat-icon-warning {

        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);

    }

    

    .stat-icon-info {

        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);

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

    

    .status-verified {

        background: #d1ecf1;

        color: #0c5460;

    }

    

    .status-unverified {

        background: #fff3cd;

        color: #856404;

    }

    

    .status-available {

        background: #d4edda;

        color: #155724;

    }

    

    .status-unavailable {

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

    

    .modern-input {

        border: 2px solid #e9ecef;

        border-radius: 12px;

        padding: 0.875rem 1rem;

        font-size: 0.95rem;

        transition: all 0.3s ease;

        background: #fafafa;

    }

    

    .modern-input:focus {

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

    

    /* Availability Toggle Section */

    .availability-toggle-section {

        background: #f8f9fa;

        border: 2px solid #e9ecef;

        border-radius: 12px;

        padding: 1.5rem;

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 1rem;

    }

    

    .availability-info {

        flex: 1;

    }

    

    .availability-status {

        margin-bottom: 0.75rem;

    }

    

    .availability-status strong {

        color: #2c3e50;

        margin-right: 0.5rem;

    }

    

    .availability-description {

        font-size: 0.875rem;

        color: #6c757d;

        margin: 0;

        line-height: 1.5;

    }

    

    .availability-description a {

        font-weight: 600;

        text-decoration: none;

    }

    

    .availability-description a:hover {

        text-decoration: underline;

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

            flex-direction: row;

            text-align: left;

            gap: 1rem;

        }

        

        .availability-toggle-section {

            flex-direction: column;

            align-items: stretch;

        }

        

        .availability-toggle-section .btn {

            width: 100%;

            margin-top: 1rem;

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

    }

</style>

@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Profile picture upload functionality

    const profilePictureInput = document.getElementById('profile_picture');

    const profilePicturePreview = document.getElementById('profilePicturePreview');

    const profilePictureWrapper = document.querySelector('.profile-picture-wrapper');

    

    // Click to upload

    profilePictureWrapper.addEventListener('click', function() {

        profilePictureInput.click();

    });

    

    // Preview uploaded image

    profilePictureInput.addEventListener('change', function(e) {

        const file = e.target.files[0];

        if (file) {

            // Validate file size (2MB)

            if (file.size > 2 * 1024 * 1024) {

                alert('File size must be less than 2MB');

                this.value = '';

                return;

            }

            

            // Validate file type

            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

            if (!allowedTypes.includes(file.type)) {

                alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');

                this.value = '';

                return;

            }

            

            const reader = new FileReader();

            reader.onload = function(e) {

                profilePicturePreview.src = e.target.result;

            };

            reader.readAsDataURL(file);

        }

    });

    

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

    const formInputs = document.querySelectorAll('.modern-input');

    formInputs.forEach(input => {

        input.addEventListener('input', function() {

            // Save to localStorage as draft

            const formData = new FormData(document.querySelector('.profile-form'));

            const data = {};

            for (let [key, value] of formData.entries()) {

                data[key] = value;

            }

            localStorage.setItem('teacherProfileDraft', JSON.stringify(data));

        });

    });

    

    // Load draft on page load

    const draft = localStorage.getItem('teacherProfileDraft');

    if (draft) {

        try {

            const data = JSON.parse(draft);

            Object.keys(data).forEach(key => {

                const input = document.querySelector(`[name="${key}"]`);

                if (input && input.type !== 'file') {

                    input.value = data[key];

                }

            });

        } catch (e) {

            console.log('Error loading draft:', e);

        }

    }

    

    // Clear draft on successful form submission

    document.querySelector('.profile-form').addEventListener('submit', function() {

        localStorage.removeItem('teacherProfileDraft');

    });

});

// Real-time status updates
document.addEventListener('DOMContentLoaded', function() {
    // Function to update verification status
    function updateVerificationStatus(isVerified) {
        const statusContainer = document.getElementById('verificationStatus');
        if (statusContainer) {
            if (isVerified) {
                statusContainer.innerHTML = `
                    <span class="status-badge status-verified">
                        <i class="fas fa-certificate me-1"></i>Verified Teacher
                    </span>
                `;
            } else {
                statusContainer.innerHTML = `
                    <span class="status-badge status-unverified">
                        <i class="fas fa-exclamation-triangle me-1"></i>Pending Verification
                    </span>
                `;
            }
        }
    }

    // Function to update availability status
    function updateAvailabilityStatus(isAvailable) {
        const statusContainer = document.getElementById('availabilityStatus');
        if (statusContainer) {
            if (isAvailable) {
                statusContainer.innerHTML = `
                    <span class="status-badge status-available">
                        <i class="fas fa-check me-1"></i>Available
                    </span>
                `;
            } else {
                statusContainer.innerHTML = `
                    <span class="status-badge status-unavailable">
                        <i class="fas fa-pause me-1"></i>Unavailable
                    </span>
                `;
            }
        }
    }

    // Function to check for status updates
    function checkStatusUpdates() {
        fetch('/teacher/profile/status-check', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update verification status if changed
                if (data.verification_status !== null) {
                    updateVerificationStatus(data.verification_status);
                }
                
                // Update availability status if changed
                if (data.availability_status !== null) {
                    updateAvailabilityStatus(data.availability_status);
                }
            }
        })
        .catch(error => {
            console.log('Status check failed:', error);
        });
    }

    // Check for updates every 30 seconds
    setInterval(checkStatusUpdates, 30000);

    // Also check immediately on page load
    setTimeout(checkStatusUpdates, 2000);
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

