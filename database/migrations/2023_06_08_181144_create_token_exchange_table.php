<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokenExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_exchange', function (Blueprint $table) {
            $table->integer('user_id')->primary()->comment('使用者ID');
            $table->string('grant', 64)->unique()->comment('用來交換token');
            $table->json('tokens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('token_exchange');
    }
}
