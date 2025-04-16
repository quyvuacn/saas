<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHistoryPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_history_payment', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('transaction_id', 255)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('uid', 255)->nullable()->unique()->comment('unique identifier for the payment record');
            $table->string('machine_command_id', 255)->nullable();
            $table->string('app_command_id', 255)->nullable();
            $table->integer('machine_id')->nullable()->comment('the machine that user use to make transaction');
            $table->integer('merchant_id')->nullable();
            $table->text('products')->nullable();
            $table->integer('transaction_type')->comment('1 is buy, 2 is recharge via office, 3 is recharge via bank...');
            $table->integer('purchase_type')->nullable();
            $table->integer('transaction_coin')->nullable()->comment('the coin use for this transaction');
            $table->integer('total_product')->nullable();
            $table->string('transaction_device', 50)->nullable()->comment('the device name use to transaction');
            $table->string('transaction_ip_address', 20)->nullable()->comment('the ip address use to make transaction');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('finish_at')->nullable();
            $table->string('machine_checksum', 255)->nullable();
            $table->string('app_checksum', 255)->nullable();
            $table->text('error_message')->nullable();
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('supported_by')->nullable();
            $table->timestamp('supported_at')->nullable();
            $table->string('checksum', 255)->nullable()->comment('the checksum all record, that to make sure the database not change by other application');
            $table->integer('is_deleted')->default(0)->comment('1 if record had been removed, 0 if is not');
            $table->string('status', 20)->default('SUCCESS')->comment('Status of the transaction: SUCCESS, FAILED, etc.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_history_payment');
    }
}
