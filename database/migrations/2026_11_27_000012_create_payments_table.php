<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Store the tutor's class-receipt fee and payment evidence. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tutor_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('bank_transfer');
            $table->string('transaction_code', 100)->nullable();
            $table->string('proof_image')->nullable();
            $table->enum('status', ['pending', 'submitted', 'confirmed', 'failed', 'cancelled'])
                ->default('pending')->comment('Fee receipt verification lifecycle');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('confirmed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['class_id', 'status']);
        });
    }

    /** Remove payments after refunds have been rolled back. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
