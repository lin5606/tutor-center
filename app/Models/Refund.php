<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Refund request and processing history for a tutor fee payment. */
class Refund extends Model
{
    protected $table = 'refunds';

    protected $fillable = [
        'payment_id', 'class_id', 'tutor_id', 'amount', 'reason', 'status',
        'processed_by', 'processed_at',
    ];

    /** TODO: Cast refund currency and processing time. */
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'processed_at' => 'datetime'];
    }

    /** TODO: Return the original payment this refund relates to. */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /** TODO: Return the class affected by the refund. */
    public function tutorClass(): BelongsTo
    {
        return $this->belongsTo(TutorClass::class, 'class_id');
    }

    /** TODO: Return the tutor receiving the refund. */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /** TODO: Return the administrator who processed this refund. */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
