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
        Schema::table('stocks', function (Blueprint $table) {
            // Drop wrong foreign keys if they exist
            try {
                $table->dropForeign(['product_id']);
            } catch (\Throwable $e) {
                // ignore if not exists
            }

            try {
                $table->dropForeign(['batch_id']);
            } catch (\Throwable $e) {
            }

            try {
                $table->dropForeign(['location_id']);
            } catch (\Throwable $e) {
            }

            // Recreate correct foreign keys
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_id']);
            } catch (\Throwable $e) {
            }

            try {
                $table->dropForeign(['batch_id']);
            } catch (\Throwable $e) {
            }

            try {
                $table->dropForeign(['location_id']);
            } catch (\Throwable $e) {
            }

            // (optional) restore the previous wrong constraints to batches to revert
            $table->foreign('product_id')->references('id')->on('batches');
            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('location_id')->references('id')->on('batches');
        });
    }
};
