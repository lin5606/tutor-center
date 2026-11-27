<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** A named RBAC role such as admin, tutor, or parent. */
class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'slug', 'description'];

    /** TODO: Return users assigned to this role through user_roles. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /** TODO: Return permission slugs granted to this role. */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
