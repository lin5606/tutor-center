<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Store tutor qualifications and administrator review data. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('tutor_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('university', 150)->nullable();
            $table->string('major', 100)->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();
            $table->string('student_card')->nullable();
            $table->text('bio')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('pending')->comment('Administrator review status');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /** Remove tutor profiles before users are rolled back. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('tutor_profiles');
    }
};
