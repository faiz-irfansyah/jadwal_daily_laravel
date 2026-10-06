<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailySchedule extends Model
{
    protected $fillable = ['schedule_date', 'source', 'activities', 'adjustment_note'];

    protected function casts(): array
    {
        return ['schedule_date' => 'date', 'activities' => 'array'];
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(ScheduleActivityCheckin::class);
    }
}
