<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tutor application and administrator decision for a class request. */
class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'class_id', 'tutor_id', 'proposal_note', 'status', 'decided_by', 'decided_at',
    ];

    /** TODO: Cast the decision time. */
    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }

    /** TODO: Return the class the tutor applied to. */
    public function tutorClass(): BelongsTo
    {
        return $this->belongsTo(TutorClass::class, 'class_id');
    }

    /** TODO: Return the applicant's account. */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /** TODO: Return the administrator who decided this application. */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /** TODO: Return fee payments associated with this application. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
