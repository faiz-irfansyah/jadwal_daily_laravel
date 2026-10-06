<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class ScheduleCheckinWindow
{
    public static function canCheckIn(string $scheduleDate, string $startTime, ?Carbon $now = null): bool
    {
        $timezone = config('scheduler.timezone');
        $now = ($now ?? Carbon::now($timezone))->copy()->setTimezone($timezone);
        $today = $now->toDateString();

        if ($scheduleDate < $today) {
            return true;
        }

        if ($scheduleDate > $today) {
            return false;
        }

        $startsAt = Carbon::createFromFormat('!Y-m-d H:i', $scheduleDate.' '.$startTime, $timezone);

        return $startsAt !== false && $now->greaterThanOrEqualTo($startsAt->copy()->subMinutes(5));
    }

    public static function opensAt(string $scheduleDate, string $startTime): Carbon
    {
        return Carbon::createFromFormat('!Y-m-d H:i', $scheduleDate.' '.$startTime, config('scheduler.timezone'))
            ->subMinutes(5);
    }
}
