<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = "List Members";

    protected static ?string $title = "Members";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Member'),
        ];
    }
}
