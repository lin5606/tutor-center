<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One parent rating and comment for the tutor assigned to a class. */
class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = ['class_id', 'parent_id', 'tutor_id', 'rating', 'comment'];

    /** TODO: Cast the rating to an integer. */
    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    /** TODO: Return the completed class being reviewed. */
    public function tutorClass(): BelongsTo
    {
        return $this->belongsTo(TutorClass::class, 'class_id');
    }

    /** TODO: Return the parent who submitted this review. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /** TODO: Return the tutor being reviewed. */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
