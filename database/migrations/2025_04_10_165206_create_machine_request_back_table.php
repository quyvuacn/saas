<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineRequestBackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_request_back', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('request_content')->nullable();
            $table->timestamp('date_receive')->nullable();
            $table->integer('machine_id');
            $table->integer('merchant_id');
            $table->integer('request_by');
            $table->boolean('status')->nullable()->default(false);
            $table->integer('is_deleted')->default(0);
            $table->timestamp('date_return_machine')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_request_back');
    }
}
