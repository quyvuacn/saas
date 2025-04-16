<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_statistic', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('uid', 255)->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('sum_coin_recharge')->nullable();
            $table->integer('sum_coin_consume')->nullable();
            $table->integer('sum_product')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_statistic');
    }
}
