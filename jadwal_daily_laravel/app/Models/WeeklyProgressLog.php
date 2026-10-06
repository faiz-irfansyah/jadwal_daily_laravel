<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyProgressLog extends Model
{
    protected $fillable = ['week_start', 'entries', 'recommendations'];

    protected function casts(): array
    {
        return ['week_start' => 'date', 'entries' => 'array'];
    }
}
