<?php

use App\Http\Controllers\AiScheduleController;
use App\Http\Controllers\DayReminderController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RoutineTemplateController;
use App\Http\Controllers\ScheduleCheckinController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\WeeklyAgendaController;
use App\Models\DailySchedule;
use App\Models\DayReminder;
use App\Services\DailyScheduleGenerator;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request, DailyScheduleGenerator $generator) {
    $timezone = config('scheduler.timezone');
    $today = Carbon::now($timezone)->toDateString();
    $selectedDate = $request->query('date', $today);
    abort_unless(is_string($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate), 422);
    try {
        $parsedDate = Carbon::createFromFormat('!Y-m-d', $selectedDate, $timezone);
        abort_unless($parsedDate && $parsedDate->format('Y-m-d') === $selectedDate, 422);
    } catch (Throwable) {
        abort(422);
    }

    $schedule = $parsedDate->greaterThanOrEqualTo(Carbon::now($timezone)->startOfDay())
        ? $generator->generateForDate($parsedDate)
        : DailySchedule::whereDate('schedule_date', $selectedDate)->first();

    return view('welcome', [
        'selectedDate' => $selectedDate,
        'schedule' => $schedule,
        'checkins' => $schedule?->checkins()->get()->keyBy('activity_id') ?? collect(),
        'dayReminders' => DayReminder::whereDate('reminder_date', $selectedDate)->orderBy('reminder_time')->get(),
    ]);
})->name('dashboard');

Route::get('/agenda', WeeklyAgendaController::class)->name('agenda');
Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
Route::get('/settings', SettingsController::class)->name('settings');
Route::get('/reminders', [DayReminderController::class, 'index'])->name('reminders');
Route::post('/reminders', [DayReminderController::class, 'store'])->name('reminders.store');
Route::patch('/reminders/{reminder}', [DayReminderController::class, 'update'])->name('reminders.update');
Route::delete('/reminders/{reminder}', [DayReminderController::class, 'destroy'])->name('reminders.destroy');
Route::get('/routines', [RoutineTemplateController::class, 'index'])->name('routines');
Route::post('/routines/{template}/activities', [RoutineTemplateController::class, 'store'])->name('routines.activities.store');
Route::patch('/routines/{template}/activities/{activityId}', [RoutineTemplateController::class, 'update'])->name('routines.activities.update');
Route::delete('/routines/{template}/activities/{activityId}', [RoutineTemplateController::class, 'destroy'])->name('routines.activities.destroy');
Route::post('/check-ins', ScheduleCheckinController::class)->name('checkins.store');
Route::post('/schedule/ai-adjust', AiScheduleController::class)->name('schedule.ai-adjust');

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class]);
