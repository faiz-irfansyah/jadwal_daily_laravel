<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Services\AiScheduleService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiScheduleController extends Controller
{
    public function __invoke(Request $request, AiScheduleService $ai): RedirectResponse
    {
        $data = $request->validate([
            'instruction' => ['required', 'string', 'max:2000'],
        ]);
        $today = Carbon::now(config('scheduler.timezone'));
        $schedule = DailySchedule::whereDate('schedule_date', $today->toDateString())->firstOrFail();

        try {
            $activities = $ai->applyNaturalLanguageRequest(
                $schedule->activities,
                trim($data['instruction']),
                $today->toIso8601String(),
                $schedule->checkins()->pluck('activity_id')->all(),
            );
            $schedule->update([
                'activities' => $activities,
                'source' => 'gemini',
                'adjustment_note' => trim($data['instruction']),
            ]);

            return redirect()->route('dashboard')->with('status', 'AI sudah menyesuaikan jadwal hari ini. Periksa agenda dan check-in di bawah.');
        } catch (RequestException $exception) {
            $status = $exception->response->status();
            Log::warning('Gemini rejected the natural language schedule request.', [
                'http_status' => $status,
                'provider_status' => $exception->response->json('error.status'),
                'provider_message' => mb_substr((string) $exception->response->json('error.message'), 0, 500),
            ]);
            $message = match (true) {
                $status === 401 || $status === 403 => 'Gemini menolak API key. Periksa GEMINI_API_KEY di file .env, lalu jalankan php artisan config:clear.',
                $status === 429 => 'Batas penggunaan Gemini tercapai. Periksa kuota dan billing project Google AI Studio/Cloud.',
                $status === 400 => 'Konfigurasi model Gemini tidak diterima. Periksa GEMINI_MODEL di file .env.',
                $status === 503 => 'Gemini mengembalikan HTTP 503 dari model utama dan cadangan setelah percobaan ulang. Jadwal tidak diubah; layanan Gemini masih tidak tersedia.',
                $status >= 500 => 'Server Gemini mengalami error (HTTP '.$status.') setelah percobaan ulang otomatis. Coba lagi beberapa saat.',
                default => 'Gemini mengembalikan HTTP '.$status.'. Periksa konfigurasi API dan coba lagi.',
            };

            return redirect()->route('dashboard')->with('ai_error', $message);
        } catch (ConnectionException $exception) {
            Log::warning('Could not connect to the Gemini API for schedule adjustment.');

            return redirect()->route('dashboard')->with('ai_error', 'Aplikasi tidak dapat terhubung ke Gemini. Periksa koneksi internet/server lalu coba lagi.');
        } catch (RuntimeException $exception) {
            Log::notice('Natural language schedule request could not be safely applied.', ['reason' => $exception->getMessage()]);
            $reason = $exception->getMessage();
            $message = str_starts_with($reason, 'GEMINI_API_KEY')
                ? 'GEMINI_API_KEY belum terbaca. Isi .env dan jalankan php artisan config:clear.'
                : (str_starts_with($reason, 'AI ') || str_starts_with($reason, 'Work Shift') || str_starts_with($reason, 'Kegiatan ')
                    ? $reason.' Jadwal lama tetap utuh.'
                    : 'AI tidak dapat menyusun perubahan yang valid. Jadwal lama tetap utuh.');

            return redirect()->route('dashboard')->with('ai_error', $message);
        } catch (\Throwable $exception) {
            Log::warning('Natural language schedule adjustment failed; schedule left unchanged.', [
                'exception_type' => get_class($exception),
            ]);

            return redirect()->route('dashboard')->with('ai_error', 'Jadwal belum diubah. AI gagal membuat susunan yang aman. Coba instruksi yang lebih jelas atau atur kegiatan lewat halaman Rutinitas.');
        }
    }
}
