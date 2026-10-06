<?php

namespace App\Console\Commands;

use App\Models\DayReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDayReminders extends Command
{
    protected $signature = 'reminders:send-day-events';

    protected $description = 'Send due date-based event reminders through Telegram';

    public function handle(): int
    {
        $now = Carbon::now(config('scheduler.timezone'))->startOfMinute();
        if (! config('scheduler.telegram.bot_token') || ! config('scheduler.telegram.chat_id')) {
            return self::SUCCESS;
        }

        $dueReminders = DayReminder::query()
            ->whereDate('reminder_date', $now->toDateString())
            ->whereTime('reminder_time', '<=', $now->format('H:i:s'))
            ->whereNull('notified_at')
            ->orderBy('reminder_time')
            ->get();

        foreach ($dueReminders as $reminder) {
            $reservedAt = Carbon::now(config('scheduler.timezone'));
            $reserved = DayReminder::query()
                ->whereKey($reminder->id)
                ->whereNull('notified_at')
                ->update(['notified_at' => $reservedAt]);

            if (! $reserved) {
                continue;
            }

            try {
                $message = "🔔 Pengingat hari: {$reminder->title}\n"
                    .$reminder->reminder_date->locale('id')->translatedFormat('l, d F Y')
                    .' · '.substr($reminder->reminder_time, 0, 5).' WITA';
                if ($reminder->notes) {
                    $message .= "\n\n{$reminder->notes}";
                }

                $this->send($message);
            } catch (\Throwable $exception) {
                DayReminder::query()->whereKey($reminder->id)->update(['notified_at' => null]);
                Log::warning('Date-based Telegram reminder could not be sent.', ['exception_type' => get_class($exception)]);
            }
        }

        return self::SUCCESS;
    }

    private function send(string $text): void
    {
        $token = config('scheduler.telegram.bot_token');
        $response = Http::timeout(10)
            ->post('https://api.telegram.org/bot'.$token.'/sendMessage', [
                'chat_id' => config('scheduler.telegram.chat_id'),
                'text' => mb_substr($text, 0, 4000),
            ])
            ->throw();

        if (! $response->json('ok')) {
            throw new \RuntimeException('Telegram rejected the date-based reminder.');
        }
    }
}
