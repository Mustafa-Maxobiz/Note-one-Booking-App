{{-- Profile Picture Upload Component --}}
{{-- Reusable component for admin, teacher, and student profile picture uploads --}}

<div class="profile-picture-wrapper position-relative d-flex flex-column align-items-center" style="cursor: pointer;">
    <div class="profile-image-container mb-2">
        <img id="profilePicturePreview" 
             src="{{ $currentPicture ?? '/images/default-avatar.png' }}" 
             alt="Profile Picture" 
             class="profile-image">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded-circle" 
             style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s;">
            <i class="fas fa-camera text-white fa-2x"></i>
        </div>
    </div>
    <input type="file" 
           class="form-control @error('profile_picture') is-invalid @enderror" 
           id="profile_picture" 
           name="profile_picture" 
           accept="image/*" 
           style="display: none;">
    <div class="form-text text-center mt-2">Click to upload • Max 2MB • JPEG, PNG, JPG, GIF</div>
    @error('profile_picture')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('profile_picture');
    const profilePicturePreview = document.getElementById('profilePicturePreview');
    const profilePictureWrapper = document.querySelector('.profile-picture-wrapper');
    const overlay = profilePictureWrapper ? profilePictureWrapper.querySelector('.overlay') : null;
    
    if (profilePictureWrapper && profilePictureInput) {
        // Click to upload
        profilePictureWrapper.addEventListener('click', function() {
            profilePictureInput.click();
        });
    }
    
    // Hover effects (guard if overlay missing)
    if (profilePictureWrapper && overlay) {
        profilePictureWrapper.addEventListener('mouseenter', function() {
            overlay.style.opacity = '1';
        });
        
        profilePictureWrapper.addEventListener('mouseleave', function() {
            overlay.style.opacity = '0';
        });
    }
    
    // Preview uploaded image
    if (profilePictureInput && profilePicturePreview) {
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
    }
});
</script>

<style>
.profile-picture-wrapper:hover .overlay {
    opacity: 1 !important;
}

.profile-picture-container {
    position: relative;
    display: inline-block;
}

.profile-image-container { position: relative; display: inline-block; }
.profile-picture-container img { display: block; }
.profile-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #e9ecef; transition: all 0.3s ease; display: block; }
.profile-image:hover { border-color: #ef473e; transform: scale(1.05); }

.overlay {
    transition: opacity 0.3s ease;
}

@media (max-width: 768px) { .profile-image { width: 120px; height: 120px; } }
</style>
