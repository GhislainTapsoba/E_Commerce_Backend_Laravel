<?php
// database/migrations/2024_01_01_000001_create_delivery_zones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('delivery_time_min')->nullable(); // en minutes
            $table->integer('delivery_time_max')->nullable();
            $table->json('coordinates')->nullable(); // pour délimiter la zone géographiquement
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_zones');
    }
};