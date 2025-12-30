<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->unique(),
                TextInput::make('phone')
                    ->label('Phone')
                    ->required()
                    ->nullable()
                    ->maxLength(14),
                TextInput::make('address')
                    ->label('Address')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }
}
