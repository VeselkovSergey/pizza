<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('patronymic')->nullable();
            $table->string('surname')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->integer('role_id')->default(1);
            $table->string('telegram_chat_id')->nullable();
            $table->tinyInteger('is_employee')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        \App\Models\User::create([
            'name' => 'Администратор',
            'email' => 'Администратор',
            'phone' => 'Администратор',
            'email_verified_at' => time(),
            'phone_verified_at' => time(),
            'password' => '4NQJLOz3VBzZq?xlLNL*?e|*nez4cJ9#',
            'role_id' => 999,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
