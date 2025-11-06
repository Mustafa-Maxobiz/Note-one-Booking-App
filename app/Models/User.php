<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditable;
use App\Traits\SoftHardDelete;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, Auditable, SoftHardDelete;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_picture',
        'role',
        'is_active',
        'password',
        'wordpress_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the teacher profile for this user.
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the student profile for this user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can be deleted
     */
    public function canBeDeleted()
    {
        // Cannot delete if has bookings as teacher
        if ($this->role === 'teacher' && $this->teacher && $this->teacher->bookings()->count() > 0) {
            return false;
        }
        
        // Cannot delete if has bookings as student
        if ($this->role === 'student' && $this->student && $this->student->bookings()->count() > 0) {
            return false;
        }
        
        

        return true;
    }

    /**
     * Get reason why user cannot be deleted
     */
    public function getDeletionBlockReason()
    {
        $reasons = [];
        
        if ($this->role === 'teacher' && $this->teacher && $this->teacher->bookings()->count() > 0) {
            $reasons[] = 'has bookings as teacher';
        }
        
        if ($this->role === 'student' && $this->student && $this->student->bookings()->count() > 0) {
            $reasons[] = 'has bookings as student';
        }
        
        

        if (!empty($reasons)) {
            return 'This user ' . implode(', ', $reasons) . '. Please handle these records first.';
        }

        return null;
    }

    /**
     * Get redirect URL after delete
     */
    protected function getRedirectAfterDelete()
    {
        return route('admin.users.index');
    }

    /**
     * Get redirect URL after restore
     */
    protected function getRedirectAfterRestore()
    {
        return route('admin.users.index');
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is student.
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }


    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }


    /**
     * Get the active subscription for this user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('expires_at', '>', now());
    }

    /**
     * Get all subscriptions for this user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if user has active subscription.
     */
    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Get subscription status.
     */
    public function getSubscriptionStatusAttribute()
    {
        if ($this->hasActiveSubscription()) {
            return 'active';
        }
        
        $lastSubscription = $this->subscriptions()->latest()->first();
        if ($lastSubscription && $lastSubscription->isExpired()) {
            return 'expired';
        }
        
        return 'none';
    }

    /**
     * Get the profile picture URL or default avatar
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/app/public/' . $this->profile_picture);
        }
        
        // Return default avatar based on role
        $defaultAvatars = [
            'admin' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc3545&color=fff&size=200',
            'teacher' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=007bff&color=fff&size=200',
            'student' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=28a745&color=fff&size=200',
        ];
        
        return $defaultAvatars[$this->role] ?? $defaultAvatars['student'];
    }

    /**
     * Get the profile picture URL for small avatars
     */
    public function getSmallProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/app/public/' . $this->profile_picture);
        }
        
        // Return default avatar based on role
        $defaultAvatars = [
            'admin' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc3545&color=fff&size=100',
            'teacher' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=007bff&color=fff&size=100',
            'student' => 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=28a745&color=fff&size=100',
        ];
        
        return $defaultAvatars[$this->role] ?? $defaultAvatars['student'];
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}
