<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientsInSupplyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredients_in_supply', function (Blueprint $table) {
            $table->id();
            $table->integer('supply_id');
            $table->integer('ingredient_id');
            $table->decimal('amount_ingredient');
            $table->decimal('price_ingredient');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredients_in_supply_tables');
    }
}
