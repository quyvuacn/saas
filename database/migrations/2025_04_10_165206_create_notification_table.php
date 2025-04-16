<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_id', 255)->nullable();
            $table->integer('machine_id')->nullable();
            $table->integer('merchant_id')->nullable();
            $table->string('uid', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('event_coin')->nullable();
            $table->integer('event_type')->nullable();
            $table->timestamp('published_date')->nullable();
            $table->integer('status')->nullable();
            $table->text('content')->nullable();
            $table->text('brief')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
