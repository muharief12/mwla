<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomRegister extends Register
{
    // protected string $view = 'filament.pages.auth.custom-register';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            Select::make('gender')
                ->required()
                ->options([
                    'pria' => 'Pria',
                    'wanita' => 'Wanita'
                ]),
            TextInput::make('no_wa')
                ->required()
                ->placeholder('Awali dengan 62')
                ->maxLength(14),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'dosen';

        return $data;
    }
}
