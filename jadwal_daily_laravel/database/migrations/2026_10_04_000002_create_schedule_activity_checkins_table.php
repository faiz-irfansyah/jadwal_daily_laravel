<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_activity_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('activity_id');
            $table->string('status', 16);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->unique(['daily_schedule_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_activity_checkins');
    }
};
