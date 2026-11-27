<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** A fine-grained action identifier checked by admin route middleware. */
class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['name', 'slug', 'module', 'description'];

    /** TODO: Return all roles granted this permission. */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
