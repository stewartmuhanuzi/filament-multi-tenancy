<?php

namespace App\Filament\Imports;

use App\Models\Task;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TaskImporter extends Importer
{
    protected static ?string $model = Task::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('team')
                ->example('Frontend')
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('name')
                ->example('Fix Loader')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('due_date')
                ->example('2022-01-01')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('status')
                ->example('done')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'in:todo,in_progress,done']),
            ImportColumn::make('body')
                ->example('The loader on the homepage isn\'t working.')
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Task
    {
        // return Task::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Task();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your task import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
