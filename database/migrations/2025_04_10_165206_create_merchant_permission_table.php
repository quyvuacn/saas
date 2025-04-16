<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_permission', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id');
            $table->string('permission_code', 100);
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the account GRANT permission for user');
            $table->timestamp('updated_at')->nullable()->comment('the update time');
            $table->integer('updated_by')->nullable()->comment('the account use to update this record');
            $table->integer('status')->comment('the status of permission, its is 1 if enable, 0 if disable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_permission');
    }
}
