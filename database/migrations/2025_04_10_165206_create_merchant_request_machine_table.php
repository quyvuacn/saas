<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantRequestMachineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_request_machine', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id');
            $table->string('title', 255);
            $table->integer('machine_request_count')->comment('count of number machine request');
            $table->date('machine_date_receive');
            $table->string('machine_position', 255)->comment('the location to use vending machine.');
            $table->text('machine_other_request');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the merchant account, that create request');
            $table->timestamp('updated_at')->nullable()->comment('time to update this record');
            $table->integer('updated_by')->nullable()->comment('the merchant account, that use to update this record');
            $table->timestamp('approved_at')->nullable()->comment('the time approved this request');
            $table->integer('approved_by')->nullable()->comment('the admin account, that use to update this record');
            $table->integer('status')->default(0)->comment('the status of record, is 0 if it create new, 1 if it had been audit, 2 if it is success.');
            $table->integer('is_deleted')->default(0)->comment('1 is merchant deleted this record, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_request_machine');
    }
}
