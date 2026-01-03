<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang_keluar')->unique();
            $table->date('tanggal_keluar');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('stock_id')->constrained();
            $table->integer('quantity');
            $table->foreignId('location_id')->constrained();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
