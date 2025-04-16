<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_ads', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id')->comment('the id of merchant');
            $table->string('title', 255)->default('')->comment('the title of advertisement');
            $table->text('content')->nullable()->comment('the content of advertisement');
            $table->string('image', 255)->nullable()->comment('the image of advertisement');
            $table->timestamp('start_date')->nullable()->comment('the time begin show advertisement');
            $table->timestamp('end_date')->nullable()->comment('the time end show advertisement');
            $table->integer('status')->default(1)->comment('the status of advertisement, 1 is active, 0 is inactive');
            $table->integer('is_deleted')->default(0)->comment('1 is deleted, 0 is not');
            $table->integer('created_by')->comment('the merchant id, that create this record');
            $table->integer('updated_by')->nullable()->comment('the merchant id, that update this record');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('checksum', 255)->nullable()->comment('the checksum all record, that to make sure the database not change by other application');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_ads');
    }
}
