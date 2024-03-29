<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('status_id');
            $table->jsonb('client_raw_data');
            $table->jsonb('products_raw_data');
            $table->jsonb('all_information_raw_data');
            $table->integer('courier_id')->nullable();
            $table->integer('courier_telegram_message_id')->nullable();
            $table->integer('order_telegram_message_id')->nullable();
            $table->integer('payment_id')->default(0);
            $table->integer('order_amount');
            $table->integer('total_order_amount');
            $table->json('geo_yandex')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
