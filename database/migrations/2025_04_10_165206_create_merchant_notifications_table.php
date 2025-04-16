<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_notifications', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id')->comment('the merchant receive notification');
            $table->string('title', 255)->comment('the title of notification');
            $table->text('content')->comment('the content of notification');
            $table->string('type', 50)->comment('the type of notification');
            $table->integer('is_read')->default(0)->comment('0: unread, 1: read');
            $table->integer('is_deleted')->default(0)->comment('1 is deleted, 0 is not');
            $table->timestamp('time_begin_show')->nullable()->comment('the time begin show notification');
            $table->timestamp('time_end_show')->nullable()->comment('the time end show notification');
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
        Schema::dropIfExists('merchant_notifications');
    }
}
