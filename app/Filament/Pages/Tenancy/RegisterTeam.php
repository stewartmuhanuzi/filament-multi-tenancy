<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->columnSpanFull(),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::query()->create($data);

        $team->members()->attach(auth()->user());

        return $team;
    }
}
