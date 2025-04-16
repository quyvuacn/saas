<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_list', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('position_id')->default(0)->comment('the position of product in machine');
            $table->integer('tray_id')->default(0)->comment('the tray number in machine');
            $table->integer('merchant_id');
            $table->integer('machine_id');
            $table->integer('product_id');
            $table->integer('product_price')->comment('each machine have a price for product, it can diff the default price. Example: price for A at machine 1 is 10.00, for machine 2 is 15.000');
            $table->integer('product_item_number')->comment('the number of slot in a ray');
            $table->integer('product_item_current')->comment('the current of item availiable');
            $table->integer('product_order')->default(0)->comment('the order of product in tray');
            $table->integer('status')->default(1)->comment('1 if active, 0 if inactive');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the merchant account, who create this record');
            $table->timestamp('updated_at')->nullable()->comment('time updated this record, example time to change product price');
            $table->integer('updated_by')->nullable()->comment('the merchant id, who change this record');
            $table->timestamp('updated_last_count')->nullable()->comment('the time auto sync data from machine with service');
            $table->integer('is_deleted')->default(0)->comment('1 if record had been deleted, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_list');
    }
}
