<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueResource\Pages;
use App\Filament\Resources\IssueResource\RelationManagers;
use App\Filament\Resources\IssueResource\Widgets\CreateIssueWidget;
use App\Models\Issue;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Task Management";

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('title')->required()->label('Issue Subject'),
                                MarkdownEditor::make('description')->required()->label('Issue Description'),
                            ])
                    ]),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                DatePicker::make('due_date')
                                    ->label('Due Date'),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'open' => 'Open',
                                        'in_progress' => 'In Progress',
                                        'done' => 'Done',
                                    ]),
                                Select::make('priority')
                                    ->label('Priority')
                                    ->options([
                                        'high' => 'High',
                                        'medium' => 'Medium',
                                        'low' => 'low',
                                    ]),

                                FileUpload::make('attachments')
                                    ->image()
                                    ->preserveFilenames()
                                    ->imageEditor(),
                            ])
                    ]),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('due_date')->date()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Created By'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'done' => 'success',
                        default => 'info'
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->limit(5)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('priority')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListIssues::route('/'),
            'create' => Pages\CreateIssue::route('/create'),
            'edit' => Pages\EditIssue::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CreateIssueWidget::class,
        ];
    }
}
