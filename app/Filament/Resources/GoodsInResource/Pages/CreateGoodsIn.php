<?php

namespace App\Filament\Resources\GoodsInResource\Pages;

use App\Filament\Resources\GoodsInResource\GoodsInResource as GoodsInResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGoodsIn extends CreateRecord
{
    protected static string $resource = GoodsInResource::class;

    protected function getRedirectUrl(): string
    {
        return GoodsInResource::getUrl('index');
    }
}
