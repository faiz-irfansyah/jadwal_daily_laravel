<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\DayReminder;
use App\Services\DailyScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WeeklyAgendaController extends Controller
{
    public function __invoke(Request $request, DailyScheduleGenerator $generator): View
    {
        $timezone = config('scheduler.timezone');
        $anchor = $request->query('week', Carbon::now($timezone)->toDateString());
        abort_unless(is_string($anchor) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $anchor), 422);
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $anchor, $timezone);
            abort_unless($date && $date->format('Y-m-d') === $anchor, 422);
        } catch (\Throwable) {
            abort(422);
        }

        $start = $date->copy()->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
        $schedules = DailySchedule::whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()])
            ->get()->keyBy(fn (DailySchedule $schedule) => $schedule->schedule_date->toDateString());
        $today = Carbon::now($timezone)->startOfDay();

        foreach (range(0, 6) as $offset) {
            $day = $start->copy()->addDays($offset);
            $dateString = $day->toDateString();

            if ($day->greaterThanOrEqualTo($today) && ! $schedules->has($dateString)) {
                $schedule = $generator->generateForDate($day);

                if ($schedule) {
                    $schedules->put($dateString, $schedule);
                }
            }
        }

        $remindersByDate = DayReminder::whereBetween('reminder_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('reminder_time')->get()->groupBy(fn (DayReminder $reminder) => $reminder->reminder_date->toDateString());
        $days = collect(range(0, 6))->map(function (int $offset) use ($start, $schedules, $remindersByDate) {
            $day = $start->copy()->addDays($offset);
            $schedule = $schedules->get($day->toDateString());
            $activities = $schedule?->activities ?? [];
            usort($activities, fn ($a, $b) => $a['start'] <=> $b['start']);

            return [
                'date' => $day,
                'schedule' => $schedule,
                'activities' => $activities,
                'checkins' => $schedule?->checkins()->get()->keyBy('activity_id') ?? collect(),
                'reminders' => $remindersByDate->get($day->toDateString(), collect()),
            ];
        });

        return view('section', [
            'page' => 'agenda', 'weekStart' => $start, 'weekEnd' => $end,
            'previousWeek' => $start->copy()->subWeek()->toDateString(),
            'nextWeek' => $start->copy()->addWeek()->toDateString(),
            'days' => $days,
        ]);
    }
}
