<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Archilex\AdvancedTables\Filters\AdvancedFilter;
use Archilex\AdvancedTables\Filters\NumericFilter;
use Archilex\AdvancedTables\Filters\Operators\TextOperator;
use Archilex\AdvancedTables\Filters\SelectFilter;
use Archilex\AdvancedTables\Filters\TextFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '=', 'processing')->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::where('status', '=', 'processing')->count() < 10 ? 'warning': 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->default('OR-' . random_int(100000, 999999))
                                ->dehydrated()
                                ->readOnly()
                                ->required(),

                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('shipping_price')
                                ->label('Shipping Costs')
                                ->dehydrated()
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'pending' => "Pending",
                                    'processing' => "Processing",
                                    'completed' => "Completed",
                                    'declined' => "Declined",
                                ]),

                            Forms\Components\MarkdownEditor::make('notes')->required()->columnSpanFull(),
                        ])->columns(),

                    Forms\Components\Wizard\Step::make('Order Items')
                        ->schema([
                            Forms\Components\Grid::make(1)->schema([

                                TableRepeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Product')
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $product = Product::query()->find($state);

                                                $set('unit_price', $product->price ?? 0);
                                            })
                                            ->options(Product::query()->pluck('name', 'id')),

                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->live()
                                            ->dehydrated()
                                            ->required()
                                            ->default(1),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Unit Price')
                                            ->numeric()
                                            ->dehydrated()
                                            ->readOnly()
                                            ->required(),

                                        Forms\Components\Placeholder::make('total_price')
                                            ->label('Total Price')
                                            ->content(function ($get) {
                                                return $get('quantity') * $get('unit_price');
                                            })
                                    ])
                            ])->columns(4),
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->sortable()->toggleable(),


                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_price')
                    ->label('Shipping Cost')
                    ->searchable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Total')->money('UGX')
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date(),
            ])
            ->filters([
                AdvancedFilter::make()->includeColumns()->filters([
                    NumericFilter::make('shipping_price'),
                    TextFilter::make('customer.name')
                        ->relationship(name: 'customer', titleAttribute:'name')
                        ->excludeOperators([
                            TextOperator::CONTAINS,
                            TextOperator::DOES_NOT_CONTAIN
                        ])
                        ->multiple()
                        ->preload(),
                    SelectFilter::make('status')
                        ->options([
                            'processing' => 'Processing',
                            'new' => 'New',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->multiple(),
                ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
