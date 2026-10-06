<?php

namespace App\Console\Commands;

use App\Models\DailySchedule;
use App\Services\DailyScheduleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateDailySchedule extends Command
{
    protected $signature = 'schedule:generate {--date= : Local schedule date (YYYY-MM-DD)} {--force : Replace an existing date with its current template}';
    protected $description = 'Generate the baseline daily schedule from the matching weekday template';

    public function handle(DailyScheduleGenerator $generator): int
    {
        $date = Carbon::parse($this->option('date') ?? 'today', config('scheduler.timezone'))->startOfDay();
        $weekday = strtolower($date->englishDayOfWeek);
        $existing = DailySchedule::whereDate('schedule_date', $date->toDateString())->first();

        if ($existing && ! $this->option('force')) {
            $this->info("A schedule already exists for {$date->toDateString()}; keeping its check-ins and adjustments. Use --force to rebuild it.");

            return self::SUCCESS;
        }

        $schedule = $generator->generateForDate($date, (bool) $this->option('force'));

        if (! $schedule) {
            $this->error("No active schedule template for {$weekday}. Run schedule:seed-templates first.");

            return self::FAILURE;
        }

        $this->info("Generated a schedule for {$date->toDateString()} ({$weekday}).");

        return self::SUCCESS;
    }
}
