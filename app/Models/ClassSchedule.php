<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One recurring day/time option for a class request. */
class ClassSchedule extends Model
{
    protected $table = 'class_schedules';

    protected $fillable = ['class_id', 'day_of_week', 'start_time', 'end_time'];

    /** TODO: Cast the weekday to an integer for form selection. */
    protected function casts(): array
    {
        return ['day_of_week' => 'integer'];
    }

    /** TODO: Return the class request this time slot belongs to. */
    public function tutorClass(): BelongsTo
    {
        return $this->belongsTo(TutorClass::class, 'class_id');
    }
}
