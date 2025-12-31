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
        Schema::create('goods_ins', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('batch_id')->nullable()->constrained();
            $table->string('batch_code');
            $table->date('manufacture_date');
            $table->date('expiry_date');
            $table->integer('quantity');
            $table->foreignId('location_id')->constrained();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_ins');
    }
};
