<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('salt', 255)->nullable();
            $table->string('firebase_token', 255)->nullable();
            $table->string('uid', 255)->nullable()->unique();
            $table->string('access_token', 255)->nullable();
            $table->string('full_name', 255)->nullable()->comment('the full name of user');
            $table->string('phone_number', 20)->nullable();
            $table->string('department', 255)->nullable()->comment('the department of user');
            $table->integer('coin')->default(0)->comment('show the active coin of user');
            $table->integer('merchant_id')->index('user_merchant_id_foreign')->comment('show the merchant, it provider product to user');
            $table->integer('is_credit_account')->default(0)->comment('1 if is a credit account, 0 if not');
            $table->integer('credit_quota')->default(0)->comment('show the credit quota of account');
            $table->integer('status')->default(1)->comment('the status of user, 0 if is new register, 1 if have been approved');
            $table->timestamp('credit_updated_at')->nullable()->comment('the time update credit this record');
            $table->integer('credit_updated_by')->nullable()->comment('show the merchant account it, that updated this record');
            $table->integer('is_deleted')->default(0)->comment('1 is deleted, 0 is not');
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
        Schema::dropIfExists('user');
    }
}
