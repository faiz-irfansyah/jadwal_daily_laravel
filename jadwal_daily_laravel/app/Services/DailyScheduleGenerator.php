<?php

namespace App\Services;

use App\Models\DailySchedule;
use App\Models\ScheduleTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DailyScheduleGenerator
{
    public function generateForDate(Carbon|string $date, bool $force = false): ?DailySchedule
    {
        $date = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date, config('scheduler.timezone'))->startOfDay();
        $dateString = $date->toDateString();
        $schedule = DailySchedule::whereDate('schedule_date', $dateString)->first();

        if ($schedule && ! $force) {
            return $schedule;
        }

        $weekday = strtolower($date->englishDayOfWeek);
        $template = ScheduleTemplate::query()->where('active', true)->get()
            ->first(fn (ScheduleTemplate $item) => in_array($weekday, $item->days_of_week, true));

        if (! $template) {
            return null;
        }

        $activities = array_map(fn (array $item) => [
            'id' => $item['id'] ?? (string) Str::uuid(),
            'title' => $item['title'],
            'start' => $item['start'],
            'end' => $item['end'],
            'locked' => $item['locked'] ?? false,
            'notes' => $item['notes'] ?? null,
        ], $template->activities);
        $attributes = ['source' => 'template', 'activities' => $activities, 'adjustment_note' => null];

        if ($schedule) {
            $schedule->update($attributes);

            return $schedule;
        }

        return DailySchedule::firstOrCreate(['schedule_date' => $dateString], $attributes);
    }
}
