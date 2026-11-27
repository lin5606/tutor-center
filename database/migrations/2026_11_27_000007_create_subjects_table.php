<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create subjects offered by tutors and requested by parents. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /** Remove the subject catalog. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
