<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListIssues extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = IssueResource::class;

    #[On('issue-created')]
    public function refresh() {}

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IssueResource\Widgets\CreateIssueWidget::class,
        ];
    }
}
