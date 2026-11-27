<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Create permission slugs used by the RBAC middleware. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique()->comment('Permission identifier checked by middleware');
            $table->string('module', 50)->comment('Feature area, e.g. classes, payments, refunds');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /** Remove permissions. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
