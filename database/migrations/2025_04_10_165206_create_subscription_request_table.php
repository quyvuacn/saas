<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('merchant_id');
            $table->integer('machine_id');
            $table->decimal('request_price', 10);
            $table->integer('request_month');
            $table->integer('payment_method')->default(1)->comment('1: Bank transfer, 2: Cash');
            $table->date('date_expire_option')->default('2025-04-03');
            $table->text('other_info')->nullable();
            $table->integer('status')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_request');
    }
}
