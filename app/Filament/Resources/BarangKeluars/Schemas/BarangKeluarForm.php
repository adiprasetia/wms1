<?php

namespace App\Filament\Resources\BarangKeluars\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;

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
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => 'OUT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->required(),

                        DatePicker::make('tanggal_keluar')
                        ->label('Tanggal Keluar')
                        ->required(),

                        Select::make('customer_id')
                        ->label('Customer')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->nullable(),
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

                        Select::make('stock_id')
                            ->label('Stok')
                            ->relationship('stock', 'product_id')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('location_id')
                            ->label('Lokasi')
                            ->relationship('location', 'code')
                            ->searchable()
                            ->preload()
                            ->required(),

                    ])
                    ->columns(2),

            ]);
    }
}
