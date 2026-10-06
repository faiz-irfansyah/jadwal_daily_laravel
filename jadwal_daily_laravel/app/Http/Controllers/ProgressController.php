<?php

namespace App\Http\Controllers;

use App\Models\WeeklyProgressLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $weekStart = Carbon::now(config('scheduler.timezone'))->startOfWeek(Carbon::MONDAY);
        $log = WeeklyProgressLog::whereDate('week_start', $weekStart->toDateString())->first();
        $entries = collect($log?->entries ?? [])->reverse()->values();

        return view('section', ['page' => 'progress', 'weekStart' => $weekStart, 'entries' => $entries, 'recommendations' => $log?->recommendations]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['entry' => ['required', 'string', 'max:500']]);
        $now = Carbon::now(config('scheduler.timezone'));
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $log = WeeklyProgressLog::whereDate('week_start', $weekStart)->first();
        if (! $log) $log = new WeeklyProgressLog(['week_start' => $weekStart, 'entries' => []]);
        $entries = $log->entries ?? [];
        $entries[] = ['logged_at' => $now->toIso8601String(), 'text' => trim($validated['entry'])];
        $log->entries = $entries;
        $log->save();

        return redirect()->route('progress')->with('status', 'Catatan progres berhasil disimpan.');
    }
}
