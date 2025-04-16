<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDebtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_debt', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('ID of the user who has the debt');
            $table->integer('debt')->default(0)->comment('Amount of debt');
            $table->integer('status')->default(0)->comment('0: New, 1: Processing, 2: Done');
            $table->integer('is_deleted')->default(0)->comment('1 if record has been removed, 0 if not');
            $table->integer('is_locked')->default(0)->comment('1 if debt is locked, 0 if unlocked');
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
        Schema::dropIfExists('user_debt');
    }
}
