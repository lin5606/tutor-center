<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Keep an auditable record of full or partial fee-refund requests. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->foreignId('tutor_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])
                ->default('pending')->comment('Refund approval and settlement lifecycle');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /** Remove refunds before their payment records. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
