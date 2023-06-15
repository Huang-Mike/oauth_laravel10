<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('code_id', 64)->comment('時效碼')->index();
            $table->string('primary_key', 128)->comment('會員主鍵');
            $table->string('otp_code', 12)->comment('驗證碼');
            $table->boolean('revoked')->default(0)->comment('是否註銷，0:否、1:是');
            $table->unsignedBigInteger('expired_at')->default(0)->index()->comment('有效期限');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
}
