<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_request', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('merchant_id');
            $table->integer('machine_request_number')->default(1)->comment('the number of machine request, minimum is 1');
            $table->text('request_content')->comment('explain content of request if need');
            $table->integer('address');
            $table->integer('date_receive')->comment('the date received machine');
            $table->integer('is_audit')->default(0)->comment('1 if audit success, 0 if not');
            $table->text('audit_false_reason')->comment('the reason if audit not success');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('status')->comment('the status of request, is 0 if it is new, 1 if wating for contract, 2 if waiting for success');
            $table->integer('is_delete')->comment('1 if record had been removed, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_request');
    }
}
