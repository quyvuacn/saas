<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_history', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id');
            $table->integer('machine_id');
            $table->date('date_expiration_begin');
            $table->date('date_expiration_end');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->string('checksum', 255)->comment('make sure all data not change by other program');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_history');
    }
}
