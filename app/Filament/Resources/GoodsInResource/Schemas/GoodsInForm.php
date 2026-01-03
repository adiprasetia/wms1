<?php

namespace App\Filament\Resources\GoodsInResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Rules\Exists;
use Closure;

class GoodsInForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengiriman')
                    ->schema([
                        TextInput::make('reference_number')
                            ->label('Nomor Referensi')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => 'IN-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->required(),

                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan tambahan...')
                            ->columnSpan('full'),
                    ])
                    ->columns(2),

                Section::make('Detail Produk')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->rules([
                                function (): Closure {
                                    return function (string $attribute, mixed $value, Closure $fail) {
                                        $locationId = request()->input('data.location_id');

                                        if (!$locationId) {
                                            return;
                                        }

                                        // Ambil lokasi
                                        $location = \App\Models\Location::find($locationId);
                                        if (!$location) {
                                            $fail('Lokasi tidak ditemukan');
                                            return;
                                        }

                                        // Hitung sisa kuota
                                        $usedQuantity = $location->stocks()->sum('quantity') ?? 0;
                                        $remainingCapacity = $location->capacity - $usedQuantity;

                                        if ($value > $remainingCapacity) {
                                            $fail("Jumlah melebihi sisa kuota. Sisa kuota: {$remainingCapacity}, Anda input: {$value}");
                                        }
                                    };
                                },
                            ]),

                        Select::make('location_id')
                            ->label('Lokasi Penyimpanan')
                            ->relationship('location', 'code')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                    ])
                    ->columns(3),

                Section::make('Informasi Batch')
                    ->schema([
                        TextInput::make('batch_code')
                            ->label('Kode Batch')
                            ->placeholder('Masukkan kode batch atau auto-generate')
                            ->required(),

                        DatePicker::make('manufacture_date')
                            ->label('Tanggal Produksi')
                            ->required(),

                        DatePicker::make('expiry_date')
                            ->label('Tanggal Kadaluarsa')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }
}
