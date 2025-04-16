<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCoinRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_coin_request', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->comment('the user request coin');
            $table->integer('coin_amount')->comment('the amount of coin request');
            $table->text('note')->nullable()->comment('the note of request');
            $table->integer('status')->default(0)->comment('0: pending, 1: approved, 2: rejected');
            $table->integer('approved_by')->nullable()->comment('the admin approved this request');
            $table->timestamp('approved_at')->nullable()->comment('the time approved this request');
            $table->integer('is_deleted')->default(0)->comment('1 is deleted, 0 is not');
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
        Schema::dropIfExists('user_coin_request');
    }
}
