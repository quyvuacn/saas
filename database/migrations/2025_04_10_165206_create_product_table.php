<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id')->comment('the merchant man');
            $table->string('name', 255);
            $table->integer('price_default')->default(0);
            $table->string('image', 255)->nullable();
            $table->text('brief');
            $table->text('product_description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the merchant account, that created this record, that not same the merchant id because 1 merchant id can contain multi merchant account');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_deleted')->default(0)->comment('1 if record had been removed, 0 if is not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
