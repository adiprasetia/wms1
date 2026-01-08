<?php

namespace App\Filament\Resources\BarangKeluars\Schemas;

use Dom\Text;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use function Laravel\Prompts\select;

class BarangKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengeluaran')
                    ->schema([
                        TextInput::make('kode_barang_keluar')
                            ->label('Kode Barang Keluar')
                            ->disabled() // tidak diisi oleh user
                            ->dehydrated() //tidak diisi oleh user
                            ->default(fn() => 'OUT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->required(),

                        DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->required(),

                        Select::make('customer_id')
                            ->label('Nama Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->reactive()
                            ->live()
                            ->preload()
                            ->required()
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Assuming Customer model has 'address' attribute
                                $customer = \App\Models\Customer::find($state);
                                $set('address', $customer->address ?? null);
                            }),

                        TextInput::make('address')
                            ->label('Alamat Customer')
                            ->readOnly()
                            ->dehydrated(false),

                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Card untuk detail produk
                Section::make('Detail Produk')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset batch_id ketika produk berubah
                                $set('batch_id', null);
                                $set('available_stock', null);
                                $set('location_id', null);
                                $set('sisa_stok', null);
                                $set('expire_date', null);
                            }),

                        Select::make('batch_id')
                            ->label('Batch')
                            ->relationship(
                                'batch',
                                'batch_code',
                                fn($query, Get $get) => $query->where('product_id', $get('product_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function (Set $set, $state) {
                                // isi batch code di stock_id berdasarkan batch yang dipilih
                                $batch = \App\Models\Batch::find($state);
                                $set('stock_id', $batch->stock_id ?? null);
                                $set('expire_date', $batch->expiry_date ?? null);

                                $stock = \App\Models\Stock::find($state);
                                $set('available_stock', $stock->quantity ?? null);
                                $set('location_id.code', $stock->location_id ?? null);
                            }),

                        TextInput::make('location_id')
                            ->label('Lokasi')
                            ->readOnly(),

                        TextInput::make('expire_date')
                            ->label('Tanggal Expire')
                            ->readOnly(),

                        Group::make([
                            TextInput::make('quantity')
                                ->label('Jumlah')
                                ->required()
                                ->live()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $availableStock = $get('available_stock') ?? 0;
                                    $quantity = $get('quantity') ?? 0;
                                    $set('sisa_stok', $availableStock - $quantity);
                                }),

                            TextInput::make('available_stock')
                                ->label('Stok')
                                ->readOnly()
                                ->dehydrated(false),


                            TextInput::make('sisa_stok')
                                ->label('Sisa')
                                ->readOnly()
                                ->dehydrated(false),

                        ])->columns(3),


                    ])
                    ->columns(2),

            ]);
    }
}
