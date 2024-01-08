<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = "Shop";

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->unique(ignoreRecord: true)->afterStateUpdated(function(string $operation, $state, Forms\Set $set){
                                        if ($operation !== 'create'){
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Pricing & Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->label('SKU (Stock Keeping Unit)'),

                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')
                                    ->required(),

                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(5000000),
                                Forms\Components\Select::make('type')
                                    ->rules('nullable', 'in:deliverable,downloadable')
                                    ->options([
                                    'downloadable' => 'Downloadable',
                                    'deliverable' => 'Deliverable',
                                ])
                            ])->columns(2)
                    ]),


                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visibility')
                                    ->helperText('Enable or disable product visibility')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Enable or disable product\'s featured status'),

                                Forms\Components\DatePicker::make('published_at')
                                    ->label('Availability')
                                    ->default(now()),
                            ]),

                        Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->directory('form-attachments')
                                    ->preserveFilenames()
                                    ->image()
                                    ->imageEditor(),
                            ])->collapsible(),

                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->required(),
                                Forms\Components\Select::make('categories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->required()
                            ])->collapsible()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->sortable()
                    ->label('Visibility')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->sortable()
                    ->label('Published At')
                    ->date()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->falseLabel('Only Hidden Products')
                    ->trueLabel('Only Visible Products')
                    ->native(false),

                Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
