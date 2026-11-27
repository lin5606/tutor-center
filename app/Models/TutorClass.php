<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Parent's tutor request; the explicit table name avoids the reserved Class name. */
class TutorClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'parent_id', 'subject_id', 'assigned_tutor_id', 'selected_application_id',
        'title', 'grade', 'district', 'private_address', 'proposed_rate', 'rate_unit',
        'requirement', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason',
    ];

    /** TODO: Cast the proposed amount and administrative review time. */
    protected function casts(): array
    {
        return ['proposed_rate' => 'decimal:2', 'reviewed_at' => 'datetime'];
    }

    /** TODO: Return the parent who posted this request. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /** TODO: Return the requested subject. */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /** TODO: Return the tutor assigned by an administrator. */
    public function assignedTutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_tutor_id');
    }

    /** TODO: Return the selected tutor application. */
    public function selectedApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'selected_application_id');
    }

    /** TODO: Return all possible weekly study time slots. */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }

    /** TODO: Return tutor applications submitted to this request. */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'class_id');
    }

    /** TODO: Return receipt-fee payment records for this class. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'class_id');
    }

    /** TODO: Return the single parent review allowed for this class. */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'class_id');
    }
}
