<?php

namespace App\Filament\Resources\GoodsInResource;

use App\Models\GoodsIn;
use App\Filament\Resources\GoodsInResource\Pages\{ListGoodsIns, CreateGoodsIn, EditGoodsIn};
use App\Filament\Resources\GoodsInResource\Schemas\GoodsInForm;
use App\Filament\Resources\GoodsInResource\Tables\GoodsInTable;
use App\Models\Batch;
use App\Models\Stock;
use App\Models\StockMovement;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use BackedEnum;
use Enum;
use UnitEnum;

class GoodsInResource extends Resource
{
    protected static ?string $model = GoodsIn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowDownRight;

    protected static ?string $navigationLabel = 'Barang Masuk';
    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return GoodsInForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodsInTable::configure($table);
    }

    public static function processGoodsIn(GoodsIn $goodsIn): void
    {
        try {
            // 1. Cek apakah batch sudah ada
            $batch = Batch::where('batch_code', $goodsIn->batch_code)
                ->where('product_id', $goodsIn->product_id)
                ->first();

            // Jika batch belum ada, buat batch baru
            if (!$batch) {
                $batch = Batch::create([
                    'product_id' => $goodsIn->product_id,
                    'batch_code' => $goodsIn->batch_code,
                    'manufacture_date' => $goodsIn->manufacture_date,
                    'expiry_date' => $goodsIn->expiry_date,
                    'quantity' => $goodsIn->quantity,
                ]);
            } else {
                // Jika batch sudah ada, update quantity-nya
                $batch->update([
                    'quantity' => $batch->quantity + $goodsIn->quantity,
                ]);
            }

            // 2. Buat atau update Stock record
            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $goodsIn->product_id,
                    'batch_id' => $batch->id,
                    'location_id' => $goodsIn->location_id,
                ],
                ['quantity' => 0]
            );

            $stock->update([
                'quantity' => $stock->quantity + $goodsIn->quantity,
            ]);

            // 3. Catat history dalam StockMovement
            StockMovement::create([
                'date' => now()->toDateString(),
                'product_id' => $goodsIn->product_id,
                'batch_id' => $batch->id,
                'location_id' => $goodsIn->location_id,
                'type' => 'IN',
                'quantity' => $goodsIn->quantity,
                'reference' => $goodsIn->reference_number,
            ]);

            // 4. Update status GoodsIn menjadi completed
            $goodsIn->update([
                'batch_id' => $batch->id,
                'status' => 'completed',
            ]);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error!')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoodsIns::route('/'),
            'create' => CreateGoodsIn::route('/create'),
            'edit' => EditGoodsIn::route('/{record}/edit'),
        ];
    }
}
