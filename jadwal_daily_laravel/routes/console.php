<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('schedule:generate')->dailyAt('00:01')->timezone(config('scheduler.timezone'))->withoutOverlapping();
Schedule::command('schedule:remind')->everyMinute()->timezone(config('scheduler.timezone'))->withoutOverlapping();
Schedule::command('reminders:send-day-events')->everyMinute()->timezone(config('scheduler.timezone'))->withoutOverlapping();
Schedule::command('schedule:weekly-review')->weeklyOn(0, '19:00')->timezone(config('scheduler.timezone'))->withoutOverlapping();
