<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tutor's class-receipt fee, evidence, and administrator confirmation. */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'class_id', 'application_id', 'tutor_id', 'amount', 'payment_method',
        'transaction_code', 'proof_image', 'status', 'confirmed_by', 'confirmed_at', 'note',
    ];

    /** TODO: Cast currency and confirmation timestamp consistently. */
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'confirmed_at' => 'datetime'];
    }

    /** TODO: Return the class whose receipt fee is being paid. */
    public function tutorClass(): BelongsTo
    {
        return $this->belongsTo(TutorClass::class, 'class_id');
    }

    /** TODO: Return the application that triggered this fee, if recorded. */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /** TODO: Return the tutor responsible for this payment. */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /** TODO: Return the administrator who confirmed receipt. */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /** TODO: Return refund requests against this payment. */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
