<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Application account shared by parents, tutors, and administrators. */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'full_name', 'email', 'password', 'phone', 'address', 'avatar', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    /** TODO: Keep account status and password values in their intended PHP types. */
    protected function casts(): array
    {
        return ['status' => 'boolean', 'password' => 'hashed'];
    }

    /** TODO: Return every role assigned to this user through user_roles. */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /** TODO: Return the tutor-specific profile, when this account is a tutor. */
    public function tutorProfile(): HasOne
    {
        return $this->hasOne(TutorProfile::class);
    }

    /** TODO: Return class requests created by this parent. */
    public function parentClasses(): HasMany
    {
        return $this->hasMany(TutorClass::class, 'parent_id');
    }

    /** TODO: Return classes assigned to this tutor. */
    public function assignedClasses(): HasMany
    {
        return $this->hasMany(TutorClass::class, 'assigned_tutor_id');
    }

    /** TODO: Return applications submitted by this tutor. */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'tutor_id');
    }

    /** TODO: Return payment records submitted by this tutor. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'tutor_id');
    }

    /** TODO: Check a permission slug through this user's roles. */
    public function hasPermission(string $slug): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('slug', $slug))
            ->exists();
    }
}
