<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Location Code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rack')
                    ->label('Rack')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slot')
                    ->label('Slot')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
