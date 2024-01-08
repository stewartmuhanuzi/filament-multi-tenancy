<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    use AdvancedTables;
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus-circle')
                ->iconButton()
                ->tooltip('Create New Order'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => ListRecords\Tab::make(),
            'This Week' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek())),
            'This Month' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek())),
            'This Year' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek())),
            'Last Year' => ListRecords\Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek())),
        ];
    }

    public function getPresetViews(): array
    {
        return [
            'processing' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'processing'))->icon('heroicon-o-heart')->color('warning')->badge(Order::query()->where('status', 'processing')->count()),
            'delivered' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'delivered'))->icon('heroicon-o-shopping-cart')->color('success')->badge(Order::query()->where('status', 'delivered')->count()),
            'pending' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending'))->icon('heroicon-o-currency-bangladeshi')->color('primary')->badge(Order::query()->where('status', 'pending')->count()),
            'shipped' => PresetView::make()
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'shipped'))->color('danger')->badge(Order::query()->where('status', 'shipped')->count()),
        ];
    }
}
