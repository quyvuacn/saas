<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('account', 100);
            $table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->integer('machine_count')->default(0)->comment('count of number machine this merchant rent, we only removed this merchant if merchant_count is 0');
            $table->integer('parent_id')->default(0)->comment('the main of merchant account, 0 if it is main merchant account');
            $table->integer('status')->default(0)->comment('0 if is new request, 1 if is waiting write contract, 2 if is waiting setup machine, 3 is done... ');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->comment('show last time update this record');
            $table->integer('updated_by')->nullable()->comment('show the merchant id, that updated this record');
            $table->integer('is_deleted')->default(0)->comment('1 if account has been deleted. 0 if account still active');
            $table->string('name', 100)->nullable();
            $table->integer('merchant_code')->nullable();
            $table->rememberToken();
            $table->string('access_token', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant');
    }
}
