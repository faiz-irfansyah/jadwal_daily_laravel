<?php

namespace App\Console\Commands;

use App\Models\WeeklyProgressLog;
use App\Services\AiScheduleService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeeklyStudyReview extends Command
{
    protected $signature = 'schedule:weekly-review';
    protected $description = 'Ask Gemini for a weekly study focus recommendation and send it on Telegram';

    public function handle(AiScheduleService $ai): int
    {
        $start = Carbon::now(config('scheduler.timezone'))->startOfWeek(Carbon::MONDAY)->toDateString();
        $logs = WeeklyProgressLog::whereBetween('week_start', [$start, Carbon::parse($start)->addDays(6)->toDateString()])->get();
        $entries = $logs->flatMap(fn ($log) => $log->entries)->values()->all();
        if ($entries === []) {
            try {
                $this->sendTelegram('Belum ada catatan progres minggu ini. Kirim /log <progres belajar> di Telegram sebelum ulasan mingguan berikutnya.');
            } catch (\Throwable $e) {
                Log::error('Telegram progress reminder failed.', ['exception_type' => get_class($e)]);
                $this->error('Could not send the weekly progress reminder; check Telegram configuration and logs.');
                return self::FAILURE;
            }
            $this->info('No study progress has been logged this week; a reminder was sent.');
            return self::SUCCESS;
        }
        try {
            $recommendations = $ai->weeklyRecommendations($entries);
            $log = WeeklyProgressLog::whereDate('week_start', $start)->first();
            if (! $log) $log = new WeeklyProgressLog(['week_start' => $start]);
            $log->entries = $entries;
            $log->recommendations = $recommendations;
            $log->save();
            $this->sendTelegram("📚 Weekly study focus\n{$recommendations}");
            $this->info('Weekly recommendations generated and sent.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Weekly Gemini review failed.', ['exception_type' => get_class($e)]);
            $this->error('Weekly review failed; check Gemini and Telegram configuration and logs.');
            return self::FAILURE;
        }
    }

    private function sendTelegram(string $text): void
    {
        $token = config('scheduler.telegram.bot_token');
        $chatId = config('scheduler.telegram.chat_id');
        if (! $token || ! $chatId) throw new \RuntimeException('TELEGRAM_BOT_TOKEN and TELEGRAM_CHAT_ID must be configured.');
        $response = Http::timeout(10)->post('https://api.telegram.org/bot'.$token.'/sendMessage', [
            'chat_id' => $chatId, 'text' => $text,
        ])->throw();
        if (! $response->json('ok')) throw new \RuntimeException('Telegram rejected the weekly message.');
    }
}
