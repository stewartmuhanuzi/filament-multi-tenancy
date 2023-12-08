<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Imports\TaskImporter;
use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(TaskImporter::class)
                ->label('Import Tasks')
                ->color('success')
                ->maxRows(100000)
        ];
    }
}
