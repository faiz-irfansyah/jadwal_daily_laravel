<?php

namespace App\Http\Controllers;

use App\Models\ScheduleTemplate;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __invoke(): View
    {
        return view('section', [
            'page' => 'settings',
            'timezone' => config('scheduler.timezone'),
            'services' => [
                'Gemini · penyesuaian jadwal dan ulasan mingguan' => (bool) config('scheduler.gemini.key'),
                'Telegram · pengingat dan perintah' => (bool) (config('scheduler.telegram.bot_token') && config('scheduler.telegram.chat_id') && config('scheduler.telegram.webhook_secret')),
            ],
            'templates' => ScheduleTemplate::where('active', true)->orderBy('name')->get(),
        ]);
    }
}
