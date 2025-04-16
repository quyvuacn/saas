<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHistoryActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_history_action', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('cmd_id', 255)->nullable();
            $table->integer('user_id')->comment('the user id, that make action');
            $table->integer('status')->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('ip', 255)->nullable();
            $table->string('time', 255)->nullable();
            $table->string('action', 100)->comment('show the name of action, example: LOGIN, LOGOUT, CHANGE_PASSWD....');
            $table->dateTime('created_at')->useCurrent();
            $table->string('device', 50)->nullable()->comment('show the device machine name, example: Iphone 6, Samsung A10...');
            $table->string('ip_address', 20)->nullable();
            $table->string('checksum', 255)->comment('the md5 of other column in this recode, that make sure all info in this record not change by other programming.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_history_action');
    }
}
