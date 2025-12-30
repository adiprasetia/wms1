<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use function Laravel\Prompts\text;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->label('Barcode')
                    ->nullable()
                    ->maxLength(15),
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('unit')
                    ->label('Unit')
                    ->required()
                    ->maxLength(10),
                TextInput::make('category')
                    ->label('Category')
                    ->nullable()
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->default('active')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ]);
    }
}
