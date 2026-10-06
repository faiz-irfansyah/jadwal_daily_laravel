<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayReminder extends Model
{
    protected $fillable = [
        'reminder_date',
        'reminder_time',
        'title',
        'notes',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'reminder_date' => 'date:Y-m-d',
            'notified_at' => 'datetime',
        ];
    }
}
