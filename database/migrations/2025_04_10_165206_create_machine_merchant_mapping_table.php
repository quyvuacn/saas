<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineMerchantMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_merchant_mapping', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('merchant_id');
            $table->integer('machine_id');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the admin account, that create this record');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_merchant_mapping');
    }
}
