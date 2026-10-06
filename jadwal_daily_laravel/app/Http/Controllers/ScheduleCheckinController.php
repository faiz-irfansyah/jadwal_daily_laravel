<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Support\ScheduleCheckinWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleCheckinController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'schedule_date' => ['required', 'date_format:Y-m-d'],
            'activity_id' => ['required', 'string', 'max:64'],
            'status' => ['required', 'in:done,skipped,pending'],
            'return_to' => ['nullable', 'in:dashboard,agenda'],
        ]);
        $date = Carbon::createFromFormat('!Y-m-d', $data['schedule_date'], config('scheduler.timezone'));
        abort_unless($date && $date->toDateString() === $data['schedule_date'], 422);
        abort_if($date->greaterThan(Carbon::now(config('scheduler.timezone'))->startOfDay()), 422, 'You can only check in on today or a past day.');

        $schedule = DailySchedule::whereDate('schedule_date', $data['schedule_date'])->firstOrFail();
        $activity = collect($schedule->activities)->first(fn ($activity) => (string) ($activity['id'] ?? '') === $data['activity_id']);
        abort_unless($activity, 404);

        if ($data['status'] !== 'pending' && ! ScheduleCheckinWindow::canCheckIn($date->toDateString(), $activity['start'])) {
            $redirect = ($data['return_to'] ?? 'dashboard') === 'agenda'
                ? redirect()->route('agenda', ['week' => $date->toDateString()])
                : redirect()->route('dashboard', ['date' => $date->toDateString()]);

            return $redirect->with('checkin_error', 'Check-in baru bisa dilakukan mulai 5 menit sebelum kegiatan dimulai.');
        }

        if ($data['status'] === 'pending') {
            $schedule->checkins()->where('activity_id', $data['activity_id'])->delete();
        } else {
            $schedule->checkins()->updateOrCreate(['activity_id' => $data['activity_id']], [
                'status' => $data['status'],
                'checked_at' => $data['status'] === 'done' ? Carbon::now(config('scheduler.timezone')) : null,
            ]);
        }

        return ($data['return_to'] ?? 'dashboard') === 'agenda'
            ? redirect()->route('agenda', ['week' => $date->toDateString()])->with('status', 'Check-in kegiatan diperbarui.')
            : redirect()->route('dashboard', ['date' => $date->toDateString()])->with('status', 'Check-in kegiatan diperbarui.');
    }
}
