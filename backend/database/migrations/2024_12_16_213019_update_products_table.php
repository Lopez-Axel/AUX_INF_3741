<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('active_compound')->nullable();
            $table->boolean('prescription_required')->default(false);
            $table->string('storage_conditions')->nullable();
            $table->string('barcode')->unique()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stock',
                'min_stock',
                'active_compound',
                'prescription_required',
                'storage_conditions',
                'barcode'
            ]);
        });
    }
};