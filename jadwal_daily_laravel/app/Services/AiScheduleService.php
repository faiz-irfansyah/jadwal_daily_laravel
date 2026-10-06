<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiScheduleService
{
    private const NATURAL_LANGUAGE_PROMPT = <<<'PROMPT'
You are a personal schedule editor. The user describes a change in natural language, possibly in English or Indonesian. Return ONLY JSON with an "activities" array in the exact output schema used below. You may add a new activity when requested. Preserve every existing activity unless a future, unlocked activity must be skipped to make the requested change feasible; in that case skip the fewest activities possible and never skip locked or already-started activities. Existing activity ids and titles must stay exactly the same; newly created activities must use ids beginning with "new:" and have a concise title, plus start/end in HH:MM, locked=false, and notes=null. Interpret relative references such as "after my work shift" using the supplied schedule and local time. Keep requested durations exact. Reorganize only future, unlocked activities as needed, retaining every activity's duration unless the user explicitly changes it.

ABSOLUTE RULE: The activity named "Work Shift" is fixed at 14:00–23:00 Asia/Makassar on the same date and must never be moved, shortened, split, deleted, renamed, or overlapped. Preserve all activities that have already started exactly as supplied. Never move a future activity into the past or create overlaps. Do not invent changes beyond the user's request. If no safe placement is possible, return the original schedule unchanged. Output valid JSON only, no markdown.
PROMPT;

    public function reschedule(array $activities, string $interruption, string $now): array
    {
        if (! config('scheduler.gemini.key')) {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        return $this->geminiScheduleProposal($activities, $interruption, $now);
    }

    /** Interpret a free-form request and safely apply its additions or schedule adjustments. */
    public function applyNaturalLanguageRequest(array $activities, string $request, string $now, array $protectedActivityIds = []): array
    {
        if (! config('scheduler.gemini.key')) {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $proposed = $this->geminiScheduleProposal($activities, mb_substr($request, 0, 2000), $now, true, $protectedActivityIds);

        return $this->validateNaturalLanguageSchedule($activities, $proposed, $now, $protectedActivityIds);
    }

    private function validateNaturalLanguageSchedule(array $original, array $proposed, string $now, array $protectedActivityIds = []): array
    {
        $originalById = collect($original)->keyBy(fn ($item) => (string) $item['id']);
        $seen = [];
        $result = [];
        $ranges = [];
        $nowMinute = $this->toMinutes(substr($now, 11, 5));
        $today = substr($now, 0, 10);

        foreach ($proposed as $item) {
            if (! is_array($item) || ! isset($item['id'], $item['title'], $item['start'], $item['end'])) {
                throw new RuntimeException('AI mengembalikan data kegiatan yang tidak lengkap.');
            }
            $id = (string) $item['id'];
            if (isset($seen[$id])) {
                throw new RuntimeException('AI mengembalikan kegiatan duplikat.');
            }
            $seen[$id] = true;
            $old = $originalById->get($id);
            $isNew = $old === null;
            if ($isNew && ! str_starts_with($id, 'new:')) {
                throw new RuntimeException('AI mencoba mengubah identitas kegiatan.');
            }
            if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', (string) $item['start']) || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', (string) $item['end'])) {
                throw new RuntimeException('AI memberikan waktu yang tidak valid.');
            }
            if (trim((string) $item['title']) === '' || mb_strlen((string) $item['title']) > 120) {
                throw new RuntimeException('Nama kegiatan dari AI tidak valid.');
            }
            if ($isNew && (($item['locked'] ?? false) || strcasecmp((string) $item['title'], 'Work Shift') === 0)) {
                throw new RuntimeException('AI tidak boleh membuat atau mengunci Work Shift baru.');
            }
            if ($old && $item['title'] !== $old['title']) {
                throw new RuntimeException('AI mengubah nama kegiatan yang sudah ada.');
            }
            if ($old && (($old['locked'] ?? false) || $old['title'] === 'Work Shift') && ($item['start'] !== $old['start'] || $item['end'] !== $old['end'])) {
                throw new RuntimeException('AI mencoba mengubah Work Shift atau kegiatan terkunci.');
            }
            if ($item['title'] === 'Work Shift' && ($item['start'] !== '14:00' || $item['end'] !== '23:00')) {
                throw new RuntimeException('Work Shift harus tetap pukul 14:00–23:00.');
            }

            $start = $this->toMinutes($item['start']);
            $end = $this->toMinutes($item['end']);
            // The baseline uses 23:00–00:00 to represent an activity ending at
            // midnight. Keep that one-hour interval distinct from zero duration.
            if ($end === 0 && $start > 0) {
                $end = 1440;
            } elseif ($end <= $start) {
                throw new RuntimeException('Kegiatan harus selesai setelah waktu mulai pada hari yang sama.');
            }
            if ($isNew && $start < $nowMinute) {
                throw new RuntimeException('AI menempatkan kegiatan baru pada waktu yang sudah lewat.');
            }
            if ($old) {
                $oldStart = $this->toMinutes($old['start']);
                if ($oldStart < $nowMinute && $today === substr($now, 0, 10) && ($item['start'] !== $old['start'] || $item['end'] !== $old['end'])) {
                    throw new RuntimeException('AI mengubah kegiatan yang sudah dimulai.');
                }
                if ($oldStart >= $nowMinute && $start < $nowMinute && $today === substr($now, 0, 10)) {
                    throw new RuntimeException('AI memindahkan kegiatan ke waktu yang sudah lewat.');
                }
                if ($oldStart >= $nowMinute) {
                    $oldEnd = $this->toMinutes($old['end']);
                    if ($oldEnd === 0 && $oldStart > 0) {
                        $oldEnd = 1440;
                    }
                    if (($end - $start) !== ($oldEnd - $oldStart)) {
                        throw new RuntimeException('Durasi kegiatan yang ada harus dipertahankan.');
                    }
                }
            }
            $ranges[] = [$start, $end];
            $result[] = [...($old ?? []), ...$item, 'id' => $id, 'locked' => $old['locked'] ?? false, 'notes' => $item['notes'] ?? null];
        }

        $skipped = [];
        foreach ($original as $item) {
            $id = (string) $item['id'];
            if (isset($seen[$id])) {
                continue;
            }
            $oldStart = $this->toMinutes($item['start']);
            if (in_array($id, $protectedActivityIds, true) || ($item['locked'] ?? false) || $item['title'] === 'Work Shift' || $oldStart < $nowMinute) {
                throw new RuntimeException('AI menghapus kegiatan yang sedang berlangsung, sudah dimulai, atau terkunci.');
            }
            $skipped[] = $item['title'];
        }
        if (count($skipped) > 1) {
            throw new RuntimeException('AI menghapus terlalu banyak kegiatan dari jadwal.');
        }

        usort($ranges, fn ($a, $b) => $a[0] <=> $b[0]);
        for ($index = 1; $index < count($ranges); $index++) {
            if ($ranges[$index][0] < $ranges[$index - 1][1]) {
                throw new RuntimeException('Jadwal hasil AI memiliki waktu yang bertabrakan.');
            }
        }

        usort($result, fn ($a, $b) => $a['start'] <=> $b['start']);
        if ($skipped !== []) {
            foreach ($result as &$item) {
                if (str_starts_with((string) $item['id'], 'new:')) {
                    $item['notes'] = trim((string) ($item['notes'] ?? '').' Ditambahkan dengan melewati kegiatan bentrok: '.implode(', ', $skipped).'.');
                    break;
                }
            }
            unset($item);
        }

        return $result;
    }

    public function organizeSchedule(array $activities, string $changeDescription, string $now): array
    {
        return $this->organizeScheduleWithSource($activities, $changeDescription, $now)['activities'];
    }

    public function organizeScheduleWithSource(array $activities, string $changeDescription, string $now): array
    {
        if (config('scheduler.gemini.key')) {
            try {
                return [
                    'activities' => $this->geminiScheduleProposal(
                        $activities,
                        "The user changed a routine activity. Reorganize the supplied day's schedule around the change, keep completed or already-started items unchanged, and preserve every activity and its identity. Change details: {$changeDescription}",
                        $now,
                    ),
                    'source' => 'gemini_template',
                ];
            } catch (\Throwable $exception) {
                Log::warning('Gemini routine proposal failed; using the validated submitted times.', ['exception_type' => get_class($exception)]);
            }
        }

        return [
            'activities' => $this->validateAdjusted($activities, $activities, $now),
            'source' => 'manual_template',
        ];
    }

    private function geminiScheduleProposal(array $activities, string $changeDescription, string $now, bool $naturalLanguage = false, array $protectedActivityIds = []): array
    {
        $payload = [
            'system_instruction' => [
                'parts' => [[
                    'text' => $naturalLanguage
                        ? self::NATURAL_LANGUAGE_PROMPT
                        : 'You organize a personal daily schedule. Return only JSON with an activities array. Preserve every supplied activity id and title. You may move only future unlocked activities. Preserve activities already started. Never create overlaps, move an activity into the past, delete activities, or change a locked item. ABSOLUTE RULE: any activity titled Work Shift stays 14:00–23:00 and locked=true. Treat all times as Asia/Makassar.',
                ]],
            ],
            'contents' => [[
                'role' => 'user',
                'parts' => [[
                    'text' => json_encode([
                        'now' => $now,
                        'timezone' => 'Asia/Makassar',
                        'change' => $changeDescription,
                        'activities' => $activities,
                        'protected_activity_ids' => $protectedActivityIds,
                    ], JSON_THROW_ON_ERROR),
                ]],
            ]],
            'generationConfig' => ['responseMimeType' => 'application/json'],
        ];
        $content = $this->generateGeminiContent($payload)->json('candidates.0.content.parts.0.text');

        $parsed = json_decode((string) $content, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($parsed['activities'] ?? null)) {
            throw new RuntimeException('Gemini returned an invalid schedule proposal.');
        }

        return $naturalLanguage
            ? $parsed['activities']
            : $this->validateAdjusted($activities, $parsed['activities'], $now);
    }

    public function validatePreferredSchedule(array $activities, string $now): array
    {
        return $this->validateAdjusted($activities, $activities, $now);
    }

    public function fallback(array $activities, string $now, int $delayMinutes = 30): array
    {
        $minuteNow = $this->toMinutes(substr($now, 11, 5));
        foreach ($activities as $activity) {
            if (($activity['title'] ?? '') === 'Work Shift' || ($activity['locked'] ?? false)) {
                continue;
            }
            $start = $this->toMinutes($activity['start']);
            if ($start < $minuteNow || $start >= 14 * 60) {
                continue;
            }
            $newStart = $start + $delayMinutes;
            if ($newStart >= 14 * 60) {
                continue;
            }

            try {
                return $this->moveActivity($activities, $activity['title'], $this->fromMinutes($newStart), $now);
            } catch (RuntimeException) {
                // Try another future activity. Never persist an overlapping fallback.
            }
        }

        throw new RuntimeException('No future activity can be delayed safely without colliding with another activity or the fixed Work Shift. Use /move <activity name> HH:MM to choose an available time.');
    }

    public function moveActivity(array $activities, string $query, string $newStart, string $now): array
    {
        if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $newStart)) {
            throw new RuntimeException('Use a 24-hour time such as 10:30.');
        }
        $matches = array_values(array_filter($activities, fn ($activity) => str_contains(mb_strtolower($activity['title']), mb_strtolower(trim($query)))));
        if (count($matches) !== 1) {
            throw new RuntimeException(count($matches) === 0 ? 'No activity matches that name.' : 'That name matches multiple activities. Use a more specific title.');
        }
        $target = $matches[0];
        if (($target['title'] ?? '') === 'Work Shift' || ($target['locked'] ?? false)) {
            throw new RuntimeException('That activity is locked and cannot be moved.');
        }
        $startMinute = $this->toMinutes($target['start']);
        $nowMinute = $this->toMinutes(substr($now, 11, 5));
        if ($startMinute < $nowMinute) {
            throw new RuntimeException('That activity has already started or passed.');
        }

        $endMinute = $this->toMinutes($target['end']);
        if ($endMinute <= $startMinute) {
            $endMinute += 1440;
        }
        $duration = $endMinute - $startMinute;
        $adjusted = array_map(function (array $activity) use ($target, $newStart, $duration) {
            if ((string) $activity['id'] !== (string) $target['id']) {
                return $activity;
            }
            $start = $this->toMinutes($newStart);

            return [...$activity, 'start' => $newStart, 'end' => $this->fromMinutes($start + $duration), 'notes' => 'Moved manually via Telegram.'];
        }, $activities);

        return $this->validateAdjusted($activities, $adjusted, $now);
    }

    public function weeklyRecommendations(array $entries): string
    {
        if (! config('scheduler.gemini.key')) {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }
        $response = $this->generateGeminiContent([
            'system_instruction' => ['parts' => [['text' => 'Review this weekly study and routine progress. Give concise, specific study focus recommendations, identify one achievable improvement, and do not fabricate facts.']]],
            'contents' => [['role' => 'user', 'parts' => [['text' => json_encode($entries, JSON_THROW_ON_ERROR)]]]],
            'generationConfig' => [],
        ])->json('candidates.0.content.parts.0.text');

        return (string) $response;
    }

    private function generateGeminiContent(array $payload): Response
    {
        $models = array_values(array_unique(array_filter([
            config('scheduler.gemini.model'),
            ...config('scheduler.gemini.fallback_models', []),
        ], fn ($model) => is_string($model) && trim($model) !== '')));

        if ($models === []) {
            throw new RuntimeException('GEMINI_MODEL is not configured.');
        }

        foreach ($models as $index => $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            try {
                return Http::timeout(30)->retry(
                    3,
                    fn (int $attempt): int => min(1000 * (2 ** ($attempt - 1)), 4000),
                    fn (\Throwable $exception): bool => $this->shouldRetryGeminiFailure($exception),
                )->post($url.'?key='.config('scheduler.gemini.key'), $payload)->throw();
            } catch (RequestException $exception) {
                $nextModel = $models[$index + 1] ?? null;

                if ($exception->response->status() !== 503 || $nextModel === null) {
                    throw $exception;
                }

                Log::warning('Gemini model returned HTTP 503; trying the configured fallback model.', [
                    'model' => $model,
                    'fallback_model' => $nextModel,
                    'http_status' => 503,
                    'provider_status' => $exception->response->json('error.status'),
                ]);
            }
        }

        throw new RuntimeException('No Gemini model returned a response.');
    }

    private function validateAdjusted(array $original, array $adjusted, string $now): array
    {
        $byId = [];
        foreach ($adjusted as $item) {
            if (isset($item['id'])) {
                $byId[(string) $item['id']] = $item;
            }
        }
        if (count($byId) !== count($original)) {
            throw new RuntimeException('AI changed the number of activities.');
        }
        $nowDate = substr($now, 0, 10);
        $nowMinute = $this->toMinutes(substr($now, 11, 5));
        $ranges = [];
        $result = [];
        foreach ($original as $item) {
            $candidate = $byId[(string) $item['id']] ?? throw new RuntimeException('AI removed an activity.');
            foreach (['id', 'title', 'start', 'end'] as $field) {
                if (! isset($candidate[$field])) {
                    throw new RuntimeException('AI omitted required activity data.');
                }
            }
            if ($candidate['title'] !== $item['title']) {
                throw new RuntimeException('AI renamed an activity.');
            }
            if (($item['locked'] ?? false) && ($candidate['start'] !== $item['start'] || $candidate['end'] !== $item['end'])) {
                throw new RuntimeException('AI moved a locked activity.');
            }
            if ($item['title'] === 'Work Shift' && ($candidate['start'] !== '14:00' || $candidate['end'] !== '23:00')) {
                throw new RuntimeException('AI violated the strict Work Shift rule.');
            }
            if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $candidate['start']) || ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $candidate['end'])) {
                throw new RuntimeException('AI returned invalid time.');
            }
            $start = $this->toMinutes($candidate['start']);
            $end = $this->toMinutes($candidate['end']);
            if ($end <= $start) {
                $end += 1440;
            }
            $oldStart = $this->toMinutes($item['start']);
            if ($oldStart < $nowMinute && substr($now, 0, 10) === $nowDate && ($candidate['start'] !== $item['start'] || $candidate['end'] !== $item['end'])) {
                throw new RuntimeException('AI changed an activity already started.');
            }
            if ($oldStart >= $nowMinute && $this->toMinutes($candidate['start']) < $nowMinute && substr($now, 0, 10) === $nowDate) {
                throw new RuntimeException('AI moved a future activity into the past.');
            }
            $ranges[] = [$start, $end, $candidate['title']];
            $result[] = [...$item, 'start' => $candidate['start'], 'end' => $candidate['end'], 'notes' => $candidate['notes'] ?? null];
        }
        usort($ranges, fn ($a, $b) => $a[0] <=> $b[0]);
        for ($i = 1; $i < count($ranges); $i++) {
            if ($ranges[$i][0] < $ranges[$i - 1][1]) {
                throw new RuntimeException('AI returned overlapping activities.');
            }
        }

        return $result;
    }

    private function toMinutes(string $time): int
    {
        return ((int) substr($time, 0, 2) * 60) + (int) substr($time, 3, 2);
    }

    private function fromMinutes(int $minute): string
    {
        return sprintf('%02d:%02d', intdiv($minute, 60) % 24, $minute % 60);
    }

    private function shouldRetryGeminiFailure(\Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        return $exception instanceof RequestException
            && in_array($exception->response->status(), [429, 500, 502, 503, 504], true);
    }
}
