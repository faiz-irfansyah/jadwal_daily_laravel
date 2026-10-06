<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTemplate extends Model
{
    protected $fillable = ['name', 'days_of_week', 'activities', 'active'];

    protected function casts(): array
    {
        return ['days_of_week' => 'array', 'activities' => 'array', 'active' => 'boolean'];
    }
}
