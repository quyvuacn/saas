<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 255);
            $table->string('model', 255);
            $table->string('code', 255)->unique()->comment('unique code for machine');
            $table->integer('number_tray')->default(0);
            $table->timestamp('date_added')->comment('date add this machine');
            $table->text('machine_system_info');
            $table->text('machine_note')->nullable()->comment('the special info note for this machine, example the merchant name request this machine...');
            $table->text('machine_address')->nullable();
            $table->integer('status')->comment('the status of machine, is 1 if machine availiable, 2 if had been forent, 0 if other problem');
            $table->integer('status_connecting')->comment('1 if success, 0 if connect false');
            $table->timestamp('created_at')->useCurrent()->comment('the time created this record');
            $table->integer('created_by')->comment('the vti account id, that create this record');
            $table->timestamp('updated_at')->nullable()->comment('the time updated this record');
            $table->integer('updated_by')->nullable()->comment('the vti account, that update this record');
            $table->integer('is_deleted')->default(0)->comment('1 if this machine had been deleted, 0 if not');
            $table->integer('merchant_id')->nullable();
            $table->string('username', 255)->nullable();
            $table->string('mqtt_topic', 255)->nullable();
            $table->string('checksum', 255)->nullable()->comment('the checksum all record, that to make sure the database not change by other application');
            $table->string('device_id', 255)->nullable()->unique()->comment('unique identifier for the device');
            $table->string('access_token', 255)->nullable();
            $table->string('password', 255)->nullable()->comment('Password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine');
    }
}
