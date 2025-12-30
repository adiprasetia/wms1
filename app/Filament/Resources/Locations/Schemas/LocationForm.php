<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use function Laravel\Prompts\text;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Location Code')
                    ->unique()
                    ->required()
                    ->maxLength(255),
                TextInput::make('rack')
                    ->label('Rack')
                    ->required()
                    ->maxLength(10),
                TextInput::make('slot')
                    ->label('Slot')
                    ->required()
                    ->maxLength(10),
                TextInput::make('capacity')
                    ->label('Capacity')
                    ->required()
                    ->integer(),
            ]);
    }
}
