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
                    ->disabled()
                    ->helperText('Auto-generated from Rack and Slot (format: RACK-SLOT)')
                    ->maxLength(255),
                TextInput::make('rack')
                    ->label('Rack')
                    ->required()
                    ->maxLength(10)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $slot = $get('slot');
                        if ($state && $slot) {
                            $set('code', sprintf('%s-%s', $state, $slot));
                        } elseif ($state) {
                            $set('code', $state);
                        }
                    }),
                TextInput::make('slot')
                    ->label('Slot')
                    ->required()
                    ->maxLength(10)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $rack = $get('rack');
                        if ($rack && $state) {
                            $set('code', sprintf('%s-%s', $rack, $state));
                        } elseif ($state) {
                            $set('code', $state);
                        }
                    }),
                TextInput::make('capacity')
                    ->label('Capacity')
                    ->required()
                    ->integer(),
            ]);
    }
}
