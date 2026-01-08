<?php

namespace App\Filament\Resources\Batches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            //sort default by expiry_date ascending
            //->defaultSort('expiry_date', 'asc')
            //sort ke-2 = query to sort by expiry_date then product name
            ->modifyQueryUsing(function (Builder $query) {
                return $query->leftJoin('products', 'batches.product_id', '=', 'products.id')
                    ->orderBy('batches.expiry_date', 'asc')
                    ->orderBy('products.name', 'asc')
                    ->select('batches.*');
            })
            ->columns([
                TextColumn::make('batch_code')
                    ->label('Batch Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('manufacture_date')
                    ->label('Manufacture Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
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
