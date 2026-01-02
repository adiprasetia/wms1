<?php

namespace App\Filament\Resources\Locations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                //kuantitas barang yang sudah dipakai dari lokasi ini
                TextColumn::make('quantity_used')
                    ->label('Quantity Dipakai')
                    ->getStateUsing(function ($record) {
                        return $record->stocks()->sum('quantity') ?? 0;
                    })
                    ->numeric(),
                //sisa kapasitas lokasi = capacity - quantity_used
                TextColumn::make('quantity_remaining')
                    ->label('Sisa')
                    ->getStateUsing(function ($record) {
                        $totalQuantity = $record->sum('capacity');
                        $usedQuantity = $record->stocks()->sum('quantity') ?? 0;
                        return $totalQuantity - $usedQuantity;
                    })
                    ->numeric(),
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
