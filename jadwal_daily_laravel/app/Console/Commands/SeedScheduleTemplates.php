<?php

namespace App\Console\Commands;

use App\Models\ScheduleTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedScheduleTemplates extends Command
{
    protected $signature = 'schedule:seed-templates';
    protected $description = 'Create or update the personal weekly schedule templates';

    public function handle(): int
    {
        $gym = [
            ['title'=>'Sleep & Recovery','start'=>'01:00','end'=>'08:30'], ['title'=>'Morning Routine & Breakfast','start'=>'08:30','end'=>'09:30'],
            ['title'=>'Data Science Study Block','start'=>'09:30','end'=>'11:30'], ['title'=>'Free Time & Chores','start'=>'11:30','end'=>'12:00'],
            ['title'=>'Lunch & Work Prep','start'=>'12:00','end'=>'13:00'], ['title'=>'Commute to Work','start'=>'13:00','end'=>'14:00'],
            ['title'=>'Work Shift','start'=>'14:00','end'=>'23:00','locked'=>true], ['title'=>'Gym Session','start'=>'23:00','end'=>'00:00'],
            ['title'=>'Shower, Snack & Wind Down','start'=>'00:00','end'=>'01:00'],
        ];
        $nonGym = [
            ['title'=>'Extended Sleep','start'=>'00:00','end'=>'08:00'], ['title'=>'Morning Routine & Breakfast','start'=>'08:00','end'=>'09:00'],
            ['title'=>'Extended Study & Coding','start'=>'09:00','end'=>'11:30'], ['title'=>'Personal Time / Hobbies','start'=>'11:30','end'=>'12:00'],
            ['title'=>'Lunch & Work Prep','start'=>'12:00','end'=>'13:00'], ['title'=>'Commute to Work','start'=>'13:00','end'=>'14:00'],
            ['title'=>'Work Shift','start'=>'14:00','end'=>'23:00','locked'=>true], ['title'=>'Head Home & Wind Down','start'=>'23:00','end'=>'00:00'],
        ];
        $weekend = [
            ['title'=>'Sleep & Recovery','start'=>'01:00','end'=>'09:00'], ['title'=>'Morning Routine & Breakfast','start'=>'09:00','end'=>'10:00'],
            ['title'=>'Weekend Focus Block','start'=>'10:00','end'=>'12:30'], ['title'=>'Lunch & Rest','start'=>'12:30','end'=>'14:00'],
            ['title'=>'Hobbies, Socializing & Free Time','start'=>'14:00','end'=>'18:00'], ['title'=>'Dinner, Evening Entertainment & Early Wind Down','start'=>'18:00','end'=>'23:59'],
        ];
        foreach ([
            ['Gym Days', ['monday','tuesday','thursday','friday'], $gym],
            ['Non-Gym Workday', ['wednesday'], $nonGym],
            ['Saturday Off', ['saturday'], array_replace($weekend, [2 => ['title'=>'Data Science Project Work','start'=>'10:00','end'=>'12:30']])],
            ['Sunday Off', ['sunday'], array_replace($weekend, [2 => ['title'=>'Weekly Review','start'=>'10:00','end'=>'12:30']])],
        ] as [$name, $days, $activities]) {
            $template = ScheduleTemplate::firstOrNew(['name' => $name]);
            if (! $template->exists) {
                $template->days_of_week = $days;
                $template->activities = $activities;
                $template->active = true;
            }

            $stableActivities = array_map(fn (array $activity) => [
                ...$activity,
                'id' => $activity['id'] ?? (string) Str::uuid(),
                'locked' => ($activity['title'] ?? '') === 'Work Shift' || ($activity['locked'] ?? false),
            ], $template->activities ?? []);
            $template->activities = $stableActivities;
            $template->save();
        }
        $this->info('Schedule templates are ready.');
        return self::SUCCESS;
    }
}
