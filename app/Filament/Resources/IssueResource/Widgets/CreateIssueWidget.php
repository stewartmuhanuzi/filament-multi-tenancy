<?php

namespace App\Filament\Resources\IssueResource\Widgets;

use App\Filament\Resources\IssueResource\Pages\ListIssues;
use App\Models\Issue;
use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class CreateIssueWidget extends Widget implements HasForms
{
    use InteractsWithForms, InteractsWithPageTable;

    protected static string $view = 'filament.resources.issue-resource.widgets.create-issue-widget';

    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];

    public $attachments;
    public $title;
    public $description;
    public $due_date;
    public $status;
    public $priority;

    protected function getTablePage(): string
    {
        return ListIssues::class;
    }

    public function create(): void
    {
        $user = auth()->user();
        $tenant = Filament::getTenant(Team::class);

        // Set the created_by and team_id directly in the form state
        $this->form->state['created_by'] = $user->id;
        $this->form->state['team_id'] = $tenant->id;

        // Validate the form
        $this->form->validate();

        // Create the issue
        Issue::create($this->form->getState());

        // Fill the form after creating the issue
        $this->form->fill();

        // Dispatch event
        $this->dispatch('issue-created');
    }


    public function mount(): void
    {
        $this->form->fill();
    }

    public function getState(): array
    {
        // Include created_by and team_id in the form state
        return array_merge(parent::getState(), [
            'created_by' => $this->form->state['created_by'],
            'team_id' => $this->form->state['team_id'],
        ]);
    }

    public function form(Form $form): Form
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
}
