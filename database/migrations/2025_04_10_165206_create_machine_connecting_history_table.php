<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineConnectingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_connecting_history', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('machine_id');
            $table->integer('status')->comment('the status of connecting, 1 if connect success, 0 if connect false');
            $table->timestamp('created_at')->comment('time to checking the status of connect');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_connecting_history');
    }
}
