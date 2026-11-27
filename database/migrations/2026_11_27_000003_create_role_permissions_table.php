<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Link roles to their allowed permission records. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });
    }

    /** Remove role-permission assignments. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
