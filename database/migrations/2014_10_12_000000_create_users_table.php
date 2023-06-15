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
            $table->id()->comment('會員編號');
            $table->string('client_id', 36)->comment('客戶ID');
            $table->string('name', 32)->comment('姓名');
            $table->string('email', 128)->comment('Email')->index();
            $table->string('isd_code', 10)->comment('國碼');
            $table->string('phone', 16)->comment('手機號碼')->index();
            $table->string('password', 128)->nullable()->default(null)->comment('密碼');
            $table->bigInteger('phone_verified_at')->nullable()->default(null)->comment('手機驗證時間');
            $table->bigInteger('email_verified_at')->nullable()->default(null)->comment('信箱驗證時間');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態，0:停用、1：啟用');
            $table->unsignedBigInteger('revoke_time')->nullable()->default(null)->comment('註銷時間');
            $table->rememberToken();
            $table->timestamps();
            $table->unique(['client_id', 'phone', 'email'], 'unique_client_phone_email');
        });
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
