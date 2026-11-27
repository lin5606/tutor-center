<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Store recurring weekday/time options for a requested class. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('2=Monday through 8=Sunday');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // TODO: Keep schedule domain checks at the MySQL layer; request validation covers SQLite tests.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE class_schedules ADD CONSTRAINT chk_class_schedules_day CHECK (day_of_week BETWEEN 2 AND 8)');
            DB::statement('ALTER TABLE class_schedules ADD CONSTRAINT chk_class_schedules_time CHECK (end_time > start_time)');
        }
    }

    /** Remove schedules before classes are rolled back. */
    // TODO: Maintain the documented schema constraints and rollback order.
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
