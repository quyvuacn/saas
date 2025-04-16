<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMachineRequestBackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_request_back', function (Blueprint $table) {
            $table->foreign(['machine_id'], 'machine_request_back_ibfk_1')->references(['id'])->on('machine')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['merchant_id'], 'machine_request_back_ibfk_2')->references(['id'])->on('merchant')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['request_by'], 'machine_request_back_ibfk_3')->references(['id'])->on('user')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_request_back', function (Blueprint $table) {
            $table->dropForeign('machine_request_back_ibfk_1');
            $table->dropForeign('machine_request_back_ibfk_2');
            $table->dropForeign('machine_request_back_ibfk_3');
        });
    }
}
