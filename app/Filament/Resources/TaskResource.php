<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')->label('Task Name'),
                            Forms\Components\MarkdownEditor::make('body')->label('Task Description'),
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
                                        'todo' => 'To Do',
                                        'in_progress' => 'In Progress',
                                        'done' => 'Done',
                                ]),
                                Forms\Components\Select::make('label_id')
                                    ->relationship('labels', 'name')
                                    ->multiple()
                                    ->required()
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('due_date')->date(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\BadgeColumn::make('labels.name')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return $state === 'Feature' ? 'success' : 'danger';
                    }),
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
