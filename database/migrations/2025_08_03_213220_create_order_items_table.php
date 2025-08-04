<?php

// database/migrations/2024_01_01_000004_create_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id'); // ID du produit depuis Strapi
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 8, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->json('product_variants')->nullable(); // taille, couleur, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
