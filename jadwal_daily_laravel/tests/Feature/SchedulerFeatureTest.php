<?php

namespace Tests\Feature;

use App\Models\DailySchedule;
use App\Models\DayReminder;
use App\Models\ScheduleTemplate;
use App\Models\WeeklyProgressLog;
use App\Services\AiScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class SchedulerFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_without_a_generated_schedule(): void
    {
        $this->get('/')->assertOk()->assertSee('Jadwalmu sedang menunggu.')->assertSee('Atur jadwal dengan AI');
    }

    public function test_natural_language_ai_request_can_add_an_activity_without_moving_work(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 10:00:00', config('scheduler.timezone')));
        config(['scheduler.gemini.key' => 'gemini-test-key']);
        $activities = [
            ['id' => 'study', 'title' => 'Study Block', 'start' => '08:00', 'end' => '09:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
        ];
        DailySchedule::create(['schedule_date' => '2026-10-05', 'source' => 'template', 'activities' => $activities]);
        $proposal = [
            $activities[0],
            ['id' => 'new:hangout-1', 'title' => 'Hang out with friend', 'start' => '11:00', 'end' => '12:00', 'locked' => false, 'notes' => null],
            $activities[1],
        ];
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => json_encode(['activities' => $proposal])]]]]]], 200)]);

        $this->get('/')->assertOk()->assertSee('today after my work shift');
        $this->post(route('schedule.ai-adjust'), ['instruction' => 'Add an hour hanging out with a friend after my work shift'])
            ->assertRedirect(route('dashboard'));

        $saved = DailySchedule::whereDate('schedule_date', '2026-10-05')->firstOrFail()->activities;
        $this->assertSame('11:00', collect($saved)->firstWhere('title', 'Hang out with friend')['start']);
        $this->assertSame('14:00', collect($saved)->firstWhere('title', 'Work Shift')['start']);
        $this->assertSame('gemini', DailySchedule::firstOrFail()->source);
    }

    public function test_ai_can_fit_an_after_shift_event_by_skipping_one_conflicting_future_activity(): void
    {
        $now = '2026-10-05T10:00:00+08:00';
        $activities = [
            ['id' => 'winddown', 'title' => 'Shower & Wind Down', 'start' => '00:00', 'end' => '01:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
            ['id' => 'gym', 'title' => 'Gym Session', 'start' => '23:00', 'end' => '00:00', 'locked' => false, 'notes' => null],
        ];
        $proposal = [
            $activities[0],
            $activities[1],
            ['id' => 'new:hangout-1', 'title' => 'Hang out with friend', 'start' => '23:00', 'end' => '00:00', 'locked' => false, 'notes' => null],
        ];
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => json_encode(['activities' => $proposal])]]]]]], 200)]);
        config(['scheduler.gemini.key' => 'gemini-test-key']);

        $result = app(AiScheduleService::class)->applyNaturalLanguageRequest($activities, 'After work, add one hour to hang out with a friend', $now);

        $this->assertCount(3, $result);
        $this->assertSame('14:00', collect($result)->firstWhere('title', 'Work Shift')['start']);
        $this->assertSame('00:00', collect($result)->firstWhere('title', 'Shower & Wind Down')['start']);
        $this->assertSame('00:00', collect($result)->firstWhere('title', 'Hang out with friend')['end']);
        $this->assertNotContains('Gym Session', collect($result)->pluck('title')->all());
    }

    public function test_agenda_progress_and_settings_pages_are_available_without_login(): void
    {
        $this->get('/agenda')->assertOk()->assertSee('Agenda mingguan.');
        $this->get('/progress')->assertOk()->assertSee('Catatan progres.');
        $this->get('/settings')->assertOk()->assertSee('Pengaturan.');
        $this->get('/reminders')->assertOk()->assertSee('Pengingat hari.');
    }

    public function test_day_reminders_support_crud_and_show_on_their_date(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 08:00:00', config('scheduler.timezone')));
        $this->post(route('reminders.store'), [
            'reminder_date' => '2026-10-08',
            'reminder_time' => '09:30',
            'title' => 'Janji dokter',
            'notes' => 'Bawa hasil pemeriksaan.',
        ])->assertRedirect(route('reminders'));

        $reminder = DayReminder::firstOrFail();
        $this->assertDatabaseHas('day_reminders', ['id' => $reminder->id, 'reminder_date' => '2026-10-08', 'title' => 'Janji dokter']);
        $this->get(route('reminders'))->assertOk()->assertSee('Janji dokter')->assertSee('Bawa hasil pemeriksaan.');
        $this->get('/?date=2026-10-08')->assertOk()->assertSee('Janji dokter')->assertSee('Bawa hasil pemeriksaan.');
        $this->get('/agenda?week=2026-10-05')->assertOk()->assertSee('Janji dokter');

        $this->patch(route('reminders.update', $reminder), [
            'reminder_date' => '2026-10-09',
            'reminder_time' => '10:00',
            'title' => 'Janji dokter gigi',
            'notes' => 'Bawa kartu asuransi.',
        ])->assertRedirect(route('reminders'));
        $this->assertDatabaseHas('day_reminders', ['id' => $reminder->id, 'reminder_date' => '2026-10-09', 'title' => 'Janji dokter gigi']);

        $this->delete(route('reminders.destroy', $reminder))->assertRedirect(route('reminders'));
        $this->assertDatabaseMissing('day_reminders', ['id' => $reminder->id]);
    }

    public function test_progress_page_saves_entries_for_the_current_week(): void
    {
        $this->post('/progress', ['entry' => 'Menyelesaikan latihan statistik selama 45 menit.'])
            ->assertRedirect(route('progress'));
        $this->post('/progress', ['entry' => 'Membaca ulang catatan probabilitas.'])
            ->assertRedirect(route('progress'));

        $this->assertDatabaseCount('weekly_progress_logs', 1);
        $this->assertCount(2, WeeklyProgressLog::firstOrFail()->entries);
        $this->assertSame('Menyelesaikan latihan statistik selama 45 menit.', WeeklyProgressLog::firstOrFail()->entries[0]['text']);
    }

    public function test_checkins_record_done_and_skipped_statuses_for_a_day(): void
    {
        $today = Carbon::now(config('scheduler.timezone'))->toDateString();
        $this->travelTo(Carbon::parse($today.' 07:00:00', config('scheduler.timezone')));
        $schedule = DailySchedule::create([
            'schedule_date' => $today,
            'source' => 'template',
            'activities' => [['id' => 'walk', 'title' => 'Morning Walk', 'start' => '08:00', 'end' => '08:30', 'locked' => false]],
        ]);
        $this->get('/?date='.$today)->assertOk()->assertSee('Check-in tersedia pukul 07:55')->assertSee('disabled title="Check-in tersedia mulai 5 menit sebelum kegiatan"', false);

        $this->post(route('checkins.store'), ['schedule_date' => $today, 'activity_id' => 'walk', 'status' => 'done'])
            ->assertRedirect(route('dashboard', ['date' => $today]))
            ->assertSessionHas('checkin_error');
        $this->assertDatabaseMissing('schedule_activity_checkins', ['daily_schedule_id' => $schedule->id, 'activity_id' => 'walk']);

        $this->travelTo(Carbon::parse($today.' 07:55:00', config('scheduler.timezone')));
        $this->post(route('checkins.store'), ['schedule_date' => $today, 'activity_id' => 'walk', 'status' => 'done'])
            ->assertRedirect(route('dashboard', ['date' => $today]));
        $this->assertDatabaseHas('schedule_activity_checkins', ['daily_schedule_id' => $schedule->id, 'activity_id' => 'walk', 'status' => 'done']);

        $this->post(route('checkins.store'), ['schedule_date' => $today, 'activity_id' => 'walk', 'status' => 'skipped'])
            ->assertRedirect();
        $this->assertDatabaseHas('schedule_activity_checkins', ['daily_schedule_id' => $schedule->id, 'activity_id' => 'walk', 'status' => 'skipped']);
    }

    public function test_editing_a_template_updates_its_next_matching_day_and_preserves_work(): void
    {
        $this->travelTo(Carbon::parse('2026-10-04 08:00:00', config('scheduler.timezone')));
        config(['scheduler.gemini.key' => null]);
        Artisan::call('schedule:seed-templates');
        $template = ScheduleTemplate::where('name', 'Gym Days')->firstOrFail();
        $study = collect($template->activities)->firstWhere('title', 'Data Science Study Block');

        $this->patch(route('routines.activities.update', [$template, $study['id']]), [
            'title' => 'Machine Learning Study', 'start' => '09:30', 'end' => '11:30',
        ])->assertRedirect(route('routines', ['template' => $template->id]));

        $tomorrow = DailySchedule::whereDate('schedule_date', '2026-10-05')->firstOrFail();
        $activities = collect($tomorrow->activities);
        $this->assertSame('Machine Learning Study', $activities->firstWhere('id', $study['id'])['title']);
        $this->assertSame('14:00', $activities->firstWhere('title', 'Work Shift')['start']);
        $this->assertSame('manual_template', $tomorrow->source);
        $this->get('/routines?template='.$template->id)->assertOk()->assertSee('Machine Learning Study');
    }

    public function test_template_commands_generate_a_daily_schedule_with_a_locked_shift(): void
    {
        Artisan::call('schedule:seed-templates');
        Artisan::call('schedule:generate', ['--date' => '2026-10-05']); // Monday

        $schedule = DailySchedule::whereDate('schedule_date', '2026-10-05')->firstOrFail();
        $shift = collect($schedule->activities)->firstWhere('title', 'Work Shift');
        $gym = collect($schedule->activities)->firstWhere('title', 'Gym Session');

        $this->assertSame('14:00', $shift['start']);
        $this->assertSame('23:00', $shift['end']);
        $this->assertTrue($shift['locked']);
        $this->assertSame('00:00', $gym['end']);
        $this->get('/agenda?week=2026-10-05')->assertOk()->assertSee('Work Shift');
        $this->get('/?date=2026-10-05')->assertOk()->assertSee('Gym Session');
        $this->get('/settings')->assertOk()->assertSee('Gym Days');
    }

    public function test_generate_does_not_overwrite_existing_schedule_unless_forced(): void
    {
        Artisan::call('schedule:seed-templates');
        Artisan::call('schedule:generate', ['--date' => '2026-10-05']);
        $schedule = DailySchedule::whereDate('schedule_date', '2026-10-05')->firstOrFail();
        $schedule->update(['source' => 'gemini', 'adjustment_note' => 'manual change']);

        Artisan::call('schedule:generate', ['--date' => '2026-10-05']);
        $this->assertSame('gemini', $schedule->fresh()->source);
        Artisan::call('schedule:generate', ['--date' => '2026-10-05', '--force' => true]);
        $this->assertSame('template', $schedule->fresh()->source);
    }

    public function test_manual_activity_move_keeps_work_shift_fixed_and_rejects_collisions(): void
    {
        $service = app(AiScheduleService::class);
        $activities = [
            ['id' => 'study', 'title' => 'Study Block', 'start' => '09:00', 'end' => '11:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
        ];
        $now = '2026-10-05T08:00:00+08:00';

        $moved = $service->moveActivity($activities, 'study', '10:00', $now);
        $this->assertSame('10:00', $moved[0]['start']);
        $this->assertSame('14:00', $moved[1]['start']);
        $this->expectException(RuntimeException::class);
        $service->moveActivity($activities, 'work', '15:00', $now);
    }

    public function test_manual_fallback_only_applies_a_collision_free_delay(): void
    {
        $activities = [
            ['id' => 'study', 'title' => 'Study Block', 'start' => '09:00', 'end' => '10:00', 'locked' => false, 'notes' => null],
            ['id' => 'break', 'title' => 'Break', 'start' => '11:00', 'end' => '12:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
        ];

        $moved = app(AiScheduleService::class)->fallback($activities, '2026-10-05T08:00:00+08:00', 30);

        $this->assertSame('09:30', $moved[0]['start']);
        $this->assertSame('14:00', $moved[2]['start']);
    }

    public function test_ai_schedule_that_moves_a_future_item_into_the_past_is_rejected(): void
    {
        $activities = [
            ['id' => 'study', 'title' => 'Study Block', 'start' => '09:00', 'end' => '10:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
        ];
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => json_encode(['activities' => [
            [...$activities[0], 'start' => '07:00', 'end' => '08:00'], $activities[1],
        ]])]]]]]], 200)]);
        config(['scheduler.gemini.key' => 'test-key']);

        $this->expectException(RuntimeException::class);
        app(AiScheduleService::class)->reschedule($activities, 'I woke up late', '2026-10-05T08:00:00+08:00');
    }

    public function test_gemini_reviews_template_changes_while_work_stays_fixed(): void
    {
        $activities = [
            ['id' => 'study', 'title' => 'Study Block', 'start' => '09:00', 'end' => '10:00', 'locked' => false, 'notes' => null],
            ['id' => 'work', 'title' => 'Work Shift', 'start' => '14:00', 'end' => '23:00', 'locked' => true, 'notes' => null],
        ];
        $proposal = [
            [...$activities[0], 'start' => '10:00', 'end' => '11:00'],
            $activities[1],
        ];
        $geminiText = json_encode(['activities' => $proposal]);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => $geminiText]]]]]], 200),
        ]);
        config(['scheduler.gemini.key' => 'gemini-test-key']);

        $result = app(AiScheduleService::class)->organizeSchedule($activities, 'move study after breakfast', '2026-10-05T08:00:00+08:00');

        $this->assertSame('10:00', $result[0]['start']);
        $this->assertSame('14:00', $result[1]['start']);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_webhook_rejects_wrong_secret_and_logs_progress_once(): void
    {
        config([
            'scheduler.telegram.webhook_secret' => 'test-secret',
            'scheduler.telegram.chat_id' => '12345',
            'scheduler.telegram.bot_token' => 'test-token',
        ]);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
        $payload = ['update_id' => 1001, 'message' => ['chat' => ['id' => 12345], 'text' => '/log Finished a study session']];

        $this->postJson('/telegram/webhook', $payload)->assertForbidden();
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'test-secret')->postJson('/telegram/webhook', $payload)->assertOk();
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'test-secret')->postJson('/telegram/webhook', $payload)->assertOk();

        $this->assertCount(1, WeeklyProgressLog::firstOrFail()->entries);
        Http::assertSentCount(1);
    }

    public function test_reminder_command_sends_a_starting_activity_once(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 08:00:00', config('scheduler.timezone')));
        config(['scheduler.telegram.bot_token' => 'test-token', 'scheduler.telegram.chat_id' => '12345']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
        DailySchedule::create([
            'schedule_date' => '2026-10-05',
            'source' => 'template',
            'activities' => [['id' => 'study', 'title' => 'Study Block', 'start' => '08:15', 'end' => '09:00', 'locked' => false]],
        ]);

        Artisan::call('schedule:remind');
        Artisan::call('schedule:remind');

        $this->assertDatabaseCount('sent_schedule_reminders', 1);
        Http::assertSentCount(1);
    }

    public function test_day_event_reminders_are_sent_once_when_due(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 10:00:00', config('scheduler.timezone')));
        config(['scheduler.telegram.bot_token' => 'test-token', 'scheduler.telegram.chat_id' => '12345']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
        DayReminder::create([
            'reminder_date' => '2026-10-05',
            'reminder_time' => '09:00:00',
            'title' => 'Janji dokter',
            'notes' => 'Bawa hasil pemeriksaan.',
        ]);

        Artisan::call('reminders:send-day-events');
        Artisan::call('reminders:send-day-events');

        $this->assertDatabaseCount('day_reminders', 1);
        $this->assertNotNull(DayReminder::firstOrFail()->notified_at);
        Http::assertSentCount(1);
    }
}
