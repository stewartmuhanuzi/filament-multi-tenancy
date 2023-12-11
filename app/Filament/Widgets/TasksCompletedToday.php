<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Task ;

class TasksCompletedToday extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tasks Completed Today',
                number_format(Task::query()->where('status', 'done')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->count())),
            Stat::make('Tasks Completed Last 30 Days',
                number_format(Task::query()->where('status', 'done')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->count())),
            Stat::make('Uncompleted Tasks',
                number_format(Task::query()->whereIn('status', ['in_progress', 'todo'])
                    ->count())),
        ];
    }
}
