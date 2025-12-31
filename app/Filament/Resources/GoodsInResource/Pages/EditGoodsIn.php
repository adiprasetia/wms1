<?php

namespace App\Filament\Resources\GoodsInResource\Pages;

use App\Filament\Resources\GoodsInResource\GoodsInResource as GoodsInResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGoodsIn extends EditRecord
{
    protected static string $resource = GoodsInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return GoodsInResource::getUrl('index');
    }
}
