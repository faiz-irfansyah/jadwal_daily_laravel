<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\WeeklyProgressLog;
use App\Services\AiScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, AiScheduleService $ai): JsonResponse
    {
        $configuredSecret = (string) config('scheduler.telegram.webhook_secret');
        $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');
        abort_if($configuredSecret === '' || ! hash_equals($configuredSecret, $providedSecret), 403);

        $message = $request->input('message');
        $chatId = (string) data_get($message, 'chat.id', '');
        $allowedChat = (string) config('scheduler.telegram.chat_id');
        abort_if($allowedChat === '' || ! hash_equals($allowedChat, $chatId), 403);

        $updateId = $request->input('update_id');
        if (is_numeric($updateId)) {
            DB::table('telegram_processed_updates')->where('created_at', '<', now()->subDays(30))->delete();
            $inserted = DB::table('telegram_processed_updates')->insertOrIgnore([
                'update_id' => (int) $updateId,
                'created_at' => now(),
            ]);
            if (! $inserted) {
                return response()->json(['ok' => true]);
            }
        }

        $text = trim((string) data_get($message, 'text', ''));
        if ($text === '') {
            return response()->json(['ok' => true]);
        }
        $text = mb_substr($text, 0, 2000);

        try {
            if (preg_match('/^\/(?:start|help)(?:@\w+)?\s*$/i', $text)) {
                $reply = "Perintah yang tersedia:\n/today — lihat jadwal hari ini\n/log <catatan> — simpan progres belajar\n/move <nama kegiatan> HH:MM — pindahkan kegiatan ke waktu yang tersedia\n/fallback [menit] — coba geser satu kegiatan dengan aman\nPesan biasa — minta AI menyesuaikan jadwal.";
            } elseif (preg_match('/^\/today(?:@\w+)?\s*$/i', $text)) {
                $schedule = DailySchedule::whereDate('schedule_date', Carbon::now(config('scheduler.timezone'))->toDateString())->first();
                $reply = $schedule ? $this->formatSchedule($schedule->activities) : 'No schedule exists for today yet.';
            } elseif (preg_match('/^\/log(?:@\w+)?\s+(.+)$/is', $text, $matches)) {
                $entry = trim(mb_substr($matches[1], 0, 500));
                $weekStart = Carbon::now(config('scheduler.timezone'))->startOfWeek(Carbon::MONDAY)->toDateString();
                $log = WeeklyProgressLog::whereDate('week_start', $weekStart)->first();
                if (! $log) {
                    $log = new WeeklyProgressLog(['week_start' => $weekStart, 'entries' => []]);
                }
                $log->entries = [...($log->entries ?? []), [
                    'logged_at' => Carbon::now(config('scheduler.timezone'))->toIso8601String(),
                    'text' => $entry,
                ]];
                $log->save();
                $reply = 'Catatan progres minggu ini tersimpan.';
            } elseif (preg_match('/^\/move(?:@\w+)?\s+(.+)\s+((?:[01]\d|2[0-3]):[0-5]\d)\s*$/i', $text, $matches)) {
                $schedule = $this->todaySchedule();
                try {
                    $activities = $ai->moveActivity($schedule->activities, trim($matches[1]), $matches[2], Carbon::now(config('scheduler.timezone'))->toIso8601String());
                    $schedule->update(['activities' => $activities, 'source' => 'manual_override', 'adjustment_note' => $text]);
                    $reply = "Jadwal diperbarui secara manual.\n".$this->formatSchedule($activities);
                } catch (\Throwable $exception) {
                    $reply = $exception->getMessage();
                }
            } elseif (preg_match('/^\/fallback(?:\s+(\d{1,3}))?\s*$/i', $text, $matches)) {
                $schedule = $this->todaySchedule();
                try {
                    $activities = $ai->fallback($schedule->activities, Carbon::now(config('scheduler.timezone'))->toIso8601String(), min((int) ($matches[1] ?? 30), 180));
                    $schedule->update(['activities' => $activities, 'source' => 'manual_fallback', 'adjustment_note' => 'Manual fallback adjustment requested via Telegram.']);
                    $reply = "Manual fallback applied.\n".$this->formatSchedule($activities);
                } catch (\Throwable $exception) {
                    $reply = $exception->getMessage();
                }
            } else {
                $schedule = $this->todaySchedule();
                try {
                    $activities = $ai->reschedule($schedule->activities, $text, Carbon::now(config('scheduler.timezone'))->toIso8601String());
                    $schedule->update(['activities' => $activities, 'source' => 'gemini', 'adjustment_note' => $text]);
                    $reply = "Schedule adjusted.\n".$this->formatSchedule($activities);
                } catch (\Throwable $exception) {
                    Log::warning('AI schedule adjustment failed; schedule left unchanged', ['exception_type' => get_class($exception)]);
                    $reply = 'AI could not safely adjust your schedule, so it was left unchanged. Send /fallback [minutes] to apply a manual delay, or try again later.';
                }
            }
            $this->sendTelegram($chatId, $reply);
        } catch (\Throwable $exception) {
            Log::error('Telegram webhook processing failed', ['exception_type' => get_class($exception)]);
        }

        // Always acknowledge promptly; Telegram retries on non-2xx responses.
        return response()->json(['ok' => true]);
    }

    private function todaySchedule(): DailySchedule
    {
        return DailySchedule::whereDate('schedule_date', Carbon::now(config('scheduler.timezone'))->toDateString())->firstOrFail();
    }

    private function formatSchedule(array $activities): string
    {
        usort($activities, fn ($a, $b) => $a['start'] <=> $b['start']);

        return implode("\n", array_map(fn ($item) => sprintf('%s–%s %s%s', $item['start'], $item['end'], $item['title'], ($item['locked'] ?? false) ? ' 🔒' : ''), $activities));
    }

    private function sendTelegram(string $chatId, string $text): void
    {
        $token = config('scheduler.telegram.bot_token');
        if (! $token) {
            throw new \RuntimeException('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }
        $response = Http::timeout(10)->post('https://api.telegram.org/bot'.$token.'/sendMessage', [
            'chat_id' => $chatId, 'text' => mb_substr($text, 0, 4000),
        ])->throw();
        if (! $response->json('ok')) {
            throw new \RuntimeException('Telegram menolak pesan keluar.');
        }
    }
}
