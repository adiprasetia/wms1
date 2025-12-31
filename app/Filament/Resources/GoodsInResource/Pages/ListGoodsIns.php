<?php

namespace App\Filament\Resources\GoodsInResource\Pages;

use App\Filament\Resources\GoodsInResource\GoodsInResource as GoodsInResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoodsIns extends ListRecords
{
    protected static string $resource = GoodsInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Input Barang Masuk'),
        ];
    }
}
