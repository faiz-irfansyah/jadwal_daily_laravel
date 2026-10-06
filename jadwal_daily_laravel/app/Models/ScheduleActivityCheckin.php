<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleActivityCheckin extends Model
{
    protected $fillable = ['daily_schedule_id', 'activity_id', 'status', 'checked_at'];

    protected function casts(): array
    {
        return ['checked_at' => 'datetime'];
    }

    public function dailySchedule(): BelongsTo
    {
        return $this->belongsTo(DailySchedule::class);
    }
}
