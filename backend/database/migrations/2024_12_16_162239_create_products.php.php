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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->date('expiration_date');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });
        //Nombre: Univ. Lopez Bustamante Axel Rolando
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('products');
    }
};
