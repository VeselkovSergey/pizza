<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('hash_name')->comment('Хеш имя');
            $table->string('original_name')->comment('Имя');
            $table->string('extension')->comment('Расширение файла');
            $table->string('type')->comment('Тип файла');
            $table->string('disk')->comment('Хранилище');
            $table->string('path')->comment('Путь в хранилище');
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
        Schema::dropIfExists('files');
    }
}
