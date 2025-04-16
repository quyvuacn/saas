<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogActionMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_action_merchant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('account_id');
            $table->integer('merchant_id')->nullable()->comment('ID of the merchant performing the action');
            $table->string('action')->comment('The action performed (e.g., login, update, etc.)');
            $table->text('details')->nullable()->comment('Additional details about the action');
            $table->string('ip_address')->nullable()->comment('IP address of the merchant');
            $table->string('user_agent')->nullable()->comment('User agent/browser information');
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
        Schema::dropIfExists('log_action_merchant');
    }
}
