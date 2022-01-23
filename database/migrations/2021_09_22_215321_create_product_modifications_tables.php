<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductModificationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_modifications', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('modification_id');
//            $table->float('cost_price');
//            $table->float('margin');
            $table->decimal('selling_price');
            $table->tinyInteger('stop_list')->default(0);
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
        Schema::dropIfExists('product_modifications_tables');
    }
}
