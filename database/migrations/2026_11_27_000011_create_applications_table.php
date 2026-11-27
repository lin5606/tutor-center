<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create tutor applications and then connect the class's selected application. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('users')->cascadeOnDelete();
            $table->text('proposal_note')->nullable();
            $table->enum('status', ['pending', 'selected', 'rejected', 'withdrawn'])
                ->default('pending')->comment('Application decision state');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'tutor_id']);
            $table->unique(['id', 'class_id']);
        });

        Schema::table('classes', function (Blueprint $table): void {
            $table->foreign('selected_application_id', 'classes_selected_application_fk')
                ->references('id')->on('applications')->nullOnDelete();
        });
    }

    /** Remove the circular selection reference before dropping applications. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table): void {
            $table->dropForeign('classes_selected_application_fk');
        });

        Schema::dropIfExists('applications');
    }
};
