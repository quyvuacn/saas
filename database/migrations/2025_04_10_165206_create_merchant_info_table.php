<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_info', function (Blueprint $table) {
            $table->integer('merchant_id')->primary();
            $table->string('merchant_name', 50)->nullable();
            $table->string('marchant_image', 255)->default('default.png');
            $table->string('merchant_company', 255)->nullable();
            $table->text('merchant_address')->nullable();
            $table->smallInteger('machine_number')->nullable();
            $table->smallInteger('alert_new')->nullable();
            $table->timestamp('merchant_request_date')->nullable()->useCurrent();
            $table->timestamp('merchant_active_date')->nullable();
            $table->text('merchant_other_request')->nullable();
            $table->text('merchant_cancel_reason')->nullable();
            $table->timestamp('merchant_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_info');
    }
}
