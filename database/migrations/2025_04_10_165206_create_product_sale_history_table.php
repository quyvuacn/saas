<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSaleHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sale_history', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('merchant_id');
            $table->integer('machine_id');
            $table->integer('user_id')->comment('the user id, who buy this product');
            $table->integer('product_id');
            $table->integer('price');
            $table->integer('payment_method')->comment('1 if pay via cash, 2 if scan qr, 3 if provider qr');
            $table->integer('created_at');
            $table->integer('status')->default(0)->comment('status of sale action, 1 if success,  2 if false, 0 if unknow');
            $table->string('checksum', 255)->comment('the md5 of info in this record, it make sure the data not change by other program');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sale_history');
    }
}
