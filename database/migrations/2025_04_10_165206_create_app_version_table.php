<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_version', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('version');
            $table->string('code');
            $table->text('brief');
            $table->string('link');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by')->comment('the merchant account, that create this record');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_deleted')->default(0)->comment('1 if this app version had been deleted, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_version');
    }
}
