<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create parent requests and their moderation/matching lifecycle. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_tutor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('selected_application_id')->nullable()->comment('Selected application, constrained after applications exists');
            $table->string('title', 200);
            $table->string('grade', 50);
            $table->string('district', 100)->comment('Publicly displayed area');
            $table->string('private_address')->nullable()->comment('Only shown to assigned tutor after payment confirmation');
            $table->decimal('proposed_rate', 12, 2);
            $table->enum('rate_unit', ['per_session', 'per_month'])
                ->default('per_session')->comment('Unit for parent proposed rate');
            $table->text('requirement')->nullable();
            $table->enum('status', [
                'pending_review', 'published', 'awaiting_tutor_payment', 'active',
                'in_progress', 'completed', 'rejected', 'cancelled', 'closed',
            ])->default('pending_review')->comment('Moderation, matching, payment, and study lifecycle');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    /** Remove class requests after dependent records have been rolled back. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
