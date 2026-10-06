<?php

namespace App\Http\Controllers;

use App\Models\DayReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DayReminderController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now(config('scheduler.timezone'))->toDateString();

        return view('section', [
            'page' => 'reminders',
            'today' => $today,
            'reminders' => DayReminder::query()
                ->orderBy('reminder_date')
                ->orderBy('reminder_time')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DayReminder::create($this->validated($request));

        return redirect()->route('reminders')->with('status', 'Pengingat tanggal berhasil ditambahkan.');
    }

    public function update(Request $request, DayReminder $reminder): RedirectResponse
    {
        $reminder->update([...$this->validated($request), 'notified_at' => null]);

        return redirect()->route('reminders')->with('status', 'Pengingat berhasil diperbarui.');
    }

    public function destroy(DayReminder $reminder): RedirectResponse
    {
        $reminder->delete();

        return redirect()->route('reminders')->with('status', 'Pengingat berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'reminder_date' => ['required', 'date_format:Y-m-d'],
            'reminder_time' => ['required', 'date_format:H:i'],
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $date = Carbon::createFromFormat('!Y-m-d', $data['reminder_date'], config('scheduler.timezone'));
        abort_unless($date && $date->toDateString() === $data['reminder_date'], 422);
        $data['title'] = trim($data['title']);
        if ($data['title'] === '') {
            throw ValidationException::withMessages(['title' => 'Nama pengingat wajib diisi.']);
        }

        return $data;
    }
}
