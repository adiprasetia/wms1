<?php

namespace App\Filament\Resources\Stocks\Tables;

use Dom\Text;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                TextColumn::make('product.unit')
                    ->label('Unit'),
                TextColumn::make('location.code')
                    ->label('Location')
                    ->sortable(),
                TextColumn::make('batch.batch_code')
                    ->label('Batch')
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
