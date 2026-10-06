<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('days_of_week');
            $table->json('activities');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('daily_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('schedule_date')->unique();
            $table->string('source')->default('template');
            $table->json('activities');
            $table->text('adjustment_note')->nullable();
            $table->timestamps();
        });

        Schema::create('weekly_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->date('week_start');
            $table->json('entries');
            $table->text('recommendations')->nullable();
            $table->timestamps();
            $table->unique('week_start');
        });

        Schema::create('sent_schedule_reminders', function (Blueprint $table) {
            $table->id();
            $table->date('schedule_date');
            $table->string('activity_id');
            $table->timestamp('sent_at');
            $table->unique(['schedule_date', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_schedule_reminders');
        Schema::dropIfExists('weekly_progress_logs');
        Schema::dropIfExists('daily_schedules');
        Schema::dropIfExists('schedule_templates');
    }
};
