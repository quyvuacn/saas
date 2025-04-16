<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email', 255);
            $table->string('account', 255)->comment('the string unique, that can use to login to admin account');
            $table->string('password', 255);
            $table->integer('is_required_change_password')->comment('1 if need change this password, 0 if not');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the id of admin account, that create this record');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->comment('the account use to update this record');
            $table->integer('is_deleted')->default(0)->comment('1 if this account had been deleted, 0 if not');
            $table->integer('status')->default(0);
            $table->string('name', 100)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
