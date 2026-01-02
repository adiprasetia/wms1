<?php

namespace App\Filament\Resources\GoodsInResource\Tables;

use App\Models\Batch;
use App\Models\Stock;
use App\Models\GoodsIn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\StockMovement;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class GoodsInTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('Nomor Referensi')
                    ->searchable(),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('batch_code')
                    ->label('Batch Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location.code')
                    ->label('Lokasi')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name'),
                SelectFilter::make('location')
                    ->label('Location')
                    ->relationship('location','code')
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (GoodsIn $record) => $record->status === 'pending'),

                Action::make('process')
                    ->label('Proses Masuk')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (GoodsIn $record) => $record->status === 'pending')
                    ->action(function (GoodsIn $record) {
                        static::processGoodsIn($record);
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Berhasil!')
                            ->body('Barang masuk telah diproses. Batch, Stock, dan History telah dibuat.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
}
