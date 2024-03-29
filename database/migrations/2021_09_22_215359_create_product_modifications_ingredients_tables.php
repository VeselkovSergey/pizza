<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductModificationsIngredientsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_modifications_ingredients', function (Blueprint $table) {
            $table->id();
            $table->integer('product_modification_id')->comment('продукт созданный на основе свойств');
            $table->integer('ingredient_id');
            $table->float('ingredient_amount');
            $table->integer('visible')->default(1)->comment('0 - не видимый, 1 - видимый');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_modifications_ingredients_tables');
    }
}
