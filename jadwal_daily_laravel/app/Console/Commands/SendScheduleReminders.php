<?php

namespace App\Console\Commands;

use App\Models\DailySchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SendScheduleReminders extends Command
{
    protected $signature = 'schedule:remind';
    protected $description = 'Send Telegram reminders for activities starting in 15 minutes';

    public function handle(): int
    {
        $now = Carbon::now(config('scheduler.timezone'))->startOfMinute();
        $target = $now->copy()->addMinutes(15);
        $schedule = DailySchedule::whereDate('schedule_date', $target->toDateString())->first();
        if (! $schedule) return self::SUCCESS;
        foreach ($schedule->activities as $activity) {
            if ($activity['start'] !== $target->format('H:i')) continue;
            try {
                $inserted = DB::table('sent_schedule_reminders')->insertOrIgnore([
                    'schedule_date' => $schedule->schedule_date->toDateString(), 'activity_id' => $activity['id'], 'sent_at' => now(),
                ]);
                if (! $inserted) continue;
                try {
                    $this->send("⏰ In 15 minutes: {$activity['title']} ({$activity['start']}–{$activity['end']})");
                } catch (\Throwable $e) {
                    DB::table('sent_schedule_reminders')->where('schedule_date', $schedule->schedule_date->toDateString())->where('activity_id', $activity['id'])->delete();
                    throw $e;
                }
            } catch (\Throwable $e) { Log::error('Schedule reminder failed', ['exception_type' => get_class($e)]); }
        }
        return self::SUCCESS;
    }

    private function send(string $text): void
    {
        if (! config('scheduler.telegram.bot_token') || ! config('scheduler.telegram.chat_id')) throw new \RuntimeException('Telegram is not configured.');
        $token = config('scheduler.telegram.bot_token');
        if (! $token) throw new \RuntimeException('TELEGRAM_BOT_TOKEN is not configured.');
        $response = Http::timeout(10)->post('https://api.telegram.org/bot'.$token.'/sendMessage', [
            'chat_id' => config('scheduler.telegram.chat_id'), 'text' => $text,
        ])->throw();
        if (! $response->json('ok')) throw new \RuntimeException('Telegram rejected the reminder message.');
    }
}
