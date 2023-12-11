<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Task Management";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')->required()->label('Task Name'),
                            Forms\Components\MarkdownEditor::make('body')->required()->label('Task Description'),
                        ])
                ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Due Date'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'todo' => 'Todo',
                                        'in_progress' => 'In Progress',
                                        'done' => 'Done',
                                ]),
                                Forms\Components\Select::make('label_id')
                                    ->relationship('labels', 'name')
                                    ->multiple()
                                    ->required(),

                                Forms\Components\Select::make('user_id')
                                    ->label('Assign Task To')
                                    ->relationship('users', 'name')
                                    ->multiple()
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('body')
                    ->limit(5)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('labels.name')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Assigned To'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
