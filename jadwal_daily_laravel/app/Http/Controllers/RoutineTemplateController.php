<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\ScheduleTemplate;
use App\Services\AiScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoutineTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $templates = ScheduleTemplate::where('active', true)->orderBy('name')->get();
        $selected = $templates->firstWhere('id', (int) $request->query('template')) ?? $templates->first();

        return view('section', ['page' => 'routines', 'templates' => $templates, 'selectedTemplate' => $selected]);
    }

    public function store(Request $request, ScheduleTemplate $template, AiScheduleService $ai): RedirectResponse
    {
        $data = $this->validatedActivity($request);
        $activities = $template->activities ?? [];
        $this->ensureUniqueTitle($activities, $data['title']);
        if (strcasecmp($data['title'], 'Work Shift') === 0) {
            throw ValidationException::withMessages(['title' => 'Work Shift dibuat oleh sistem dan tidak bisa ditambahkan.']);
        }
        $activities[] = ['id' => (string) Str::uuid(), ...$data, 'locked' => false, 'notes' => null];

        $this->saveTemplateAndSchedules($template, $activities, 'Kegiatan baru ditambahkan: '.$data['title'], $ai);

        return redirect()->route('routines', ['template' => $template->id])->with('status', 'Kegiatan ditambahkan. Jadwal hari ini atau besok yang memakai template ini sudah diselaraskan.');
    }

    public function update(Request $request, ScheduleTemplate $template, string $activityId, AiScheduleService $ai): RedirectResponse
    {
        $data = $this->validatedActivity($request);
        $activities = $template->activities ?? [];
        $index = collect($activities)->search(fn ($activity) => (string) ($activity['id'] ?? '') === $activityId);
        abort_if($index === false, 404);
        if (($activities[$index]['locked'] ?? false) || ($activities[$index]['title'] ?? '') === 'Work Shift') {
            throw ValidationException::withMessages(['activity' => 'Work Shift terkunci dan tidak bisa diubah.']);
        }
        $this->ensureUniqueTitle($activities, $data['title'], $activityId);
        $activities[$index] = [...$activities[$index], ...$data];

        $this->saveTemplateAndSchedules($template, array_values($activities), 'Kegiatan diperbarui: '.$data['title'], $ai);

        return redirect()->route('routines', ['template' => $template->id])->with('status', 'Perubahan disimpan dan jadwal terdekat sudah diselaraskan.');
    }

    public function destroy(ScheduleTemplate $template, string $activityId, AiScheduleService $ai): RedirectResponse
    {
        $activities = $template->activities ?? [];
        $target = collect($activities)->first(fn ($activity) => (string) ($activity['id'] ?? '') === $activityId);
        abort_if(! $target, 404);
        abort_if(($target['locked'] ?? false) || ($target['title'] ?? '') === 'Work Shift', 422, 'Work Shift terkunci dan tidak bisa dihapus.');
        $activities = array_values(array_filter($activities, fn ($activity) => (string) ($activity['id'] ?? '') !== $activityId));

        $this->saveTemplateAndSchedules($template, $activities, 'Kegiatan dihapus: '.$target['title'], $ai);

        return redirect()->route('routines', ['template' => $template->id])->with('status', 'Kegiatan dihapus dan jadwal terdekat sudah diselaraskan.');
    }

    private function validatedActivity(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i', 'different:start'],
        ]);
        $data['title'] = trim($data['title']);
        if ($data['title'] === '') throw ValidationException::withMessages(['title' => 'Nama kegiatan wajib diisi.']);

        return $data;
    }

    private function ensureUniqueTitle(array $activities, string $title, ?string $exceptId = null): void
    {
        $duplicate = collect($activities)->contains(fn ($activity) =>
            (string) ($activity['id'] ?? '') !== (string) $exceptId
            && mb_strtolower(trim($activity['title'] ?? '')) === mb_strtolower($title)
        );
        if ($duplicate) throw ValidationException::withMessages(['title' => 'Nama kegiatan sudah ada di template ini.']);
    }

    private function saveTemplateAndSchedules(ScheduleTemplate $template, array $activities, string $description, AiScheduleService $ai): void
    {
        $timezone = config('scheduler.timezone');
        $today = Carbon::now($timezone)->startOfDay();
        $dates = [$today, $today->copy()->addDay()];
        $dailyUpdates = [];

        foreach ($dates as $date) {
            if (! in_array(strtolower($date->englishDayOfWeek), $template->days_of_week, true)) continue;

            $schedule = DailySchedule::whereDate('schedule_date', $date->toDateString())->first();
            $existing = collect($schedule?->activities ?? [])->keyBy(fn ($activity) => (string) ($activity['id'] ?? ''));
            $checkins = $schedule?->checkins()->get()->keyBy('activity_id') ?? collect();
            $clock = $date->isToday() ? Carbon::now($timezone) : $date->copy();
            $minuteNow = $clock->hour * 60 + $clock->minute;
            $desiredIds = collect($activities)->pluck('id')->map(fn ($id) => (string) $id)->all();
            $proposed = [];

            // Keep activities that already started or were checked in today, even if the template changed.
            foreach ($existing as $id => $oldActivity) {
                $started = $this->activityHasStarted($oldActivity, $minuteNow);
                $wasCheckedIn = $checkins->has($id);
                if (($started || $wasCheckedIn) && ! in_array($id, $desiredIds, true)) $proposed[] = $oldActivity;
                elseif (($started || $wasCheckedIn) && in_array($id, $desiredIds, true)) $proposed[] = $oldActivity;
            }
            $kept = collect($proposed)->pluck('id')->map(fn ($id) => (string) $id)->all();
            foreach ($activities as $activity) {
                if (in_array((string) $activity['id'], $kept, true)) continue;
                $proposed[] = [...$activity, 'notes' => $activity['notes'] ?? null];
            }
            usort($proposed, fn ($a, $b) => $a['start'] <=> $b['start']);
            $now = $clock->toIso8601String();
            try {
                $result = $ai->organizeScheduleWithSource($proposed, $description, $now);
                $organized = $result['activities'];
                $source = $result['source'];
            } catch (\Throwable $exception) {
                // Keep the requested times only when the local safety checks confirm the result.
                Log::warning('AI routine organization fell back to submitted times.', ['exception_type' => get_class($exception)]);
                try {
                    $organized = $ai->validatePreferredSchedule($proposed, $now);
                } catch (\Throwable $validationException) {
                    throw ValidationException::withMessages(['activity' => $validationException->getMessage()]);
                }
                $source = 'manual_template';
            }
            $dailyUpdates[] = [
                'schedule_date' => $date->toDateString(),
                'source' => $source,
                'activities' => $organized,
                'adjustment_note' => $description,
            ];
        }

        DB::transaction(function () use ($template, $activities, $dailyUpdates): void {
            $template->activities = array_values($activities);
            $template->save();
            foreach ($dailyUpdates as $update) {
                $schedule = DailySchedule::whereDate('schedule_date', $update['schedule_date'])->first();
                $attributes = [
                    'source' => $update['source'],
                    'activities' => $update['activities'],
                    'adjustment_note' => $update['adjustment_note'],
                ];
                if ($schedule) $schedule->update($attributes);
                else DailySchedule::create(['schedule_date' => $update['schedule_date'], ...$attributes]);
            }
        });
    }

    private function activityHasStarted(array $activity, int $minuteNow): bool
    {
        $start = ((int) substr($activity['start'], 0, 2) * 60) + (int) substr($activity['start'], 3, 2);
        return $start < $minuteNow;
    }
}
