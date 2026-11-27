<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create the dynamic RBAC role catalog. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 50)->unique()->comment('Display name, e.g. Administrator');
            $table->string('slug', 50)->unique()->comment('Stable role code: admin, tutor, parent');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /** Remove the role catalog. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
