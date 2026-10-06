<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('day_reminders', function (Blueprint $table) {
            $table->id();
            $table->date('reminder_date')->index();
            $table->time('reminder_time')->default('09:00:00');
            $table->string('title', 120);
            $table->text('notes')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_reminders');
    }
};
