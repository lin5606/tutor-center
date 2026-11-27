<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Tutor qualifications, private verification documents, and review status. */
class TutorProfile extends Model
{
    protected $table = 'tutor_profiles';

    protected $fillable = [
        'user_id', 'university', 'major', 'graduation_year', 'id_card_front',
        'id_card_back', 'student_card', 'bio', 'approval_status', 'reviewed_by',
        'reviewed_at', 'reject_reason',
    ];

    /** TODO: Cast review date and graduation year for application use. */
    protected function casts(): array
    {
        return ['graduation_year' => 'integer', 'reviewed_at' => 'datetime'];
    }

    /** TODO: Return the account that owns this tutor profile. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** TODO: Return the administrator who reviewed this profile. */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** TODO: Return subjects this tutor can teach through tutor_subjects. */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'tutor_subjects');
    }
}
