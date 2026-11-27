<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Subject catalog shared by class requests and tutor profiles. */
class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = ['name', 'description'];

    /** TODO: Return tutor profiles associated through tutor_subjects. */
    public function tutorProfiles(): BelongsToMany
    {
        return $this->belongsToMany(TutorProfile::class, 'tutor_subjects');
    }

    /** TODO: Return class requests seeking this subject. */
    public function classes(): HasMany
    {
        return $this->hasMany(TutorClass::class);
    }
}
