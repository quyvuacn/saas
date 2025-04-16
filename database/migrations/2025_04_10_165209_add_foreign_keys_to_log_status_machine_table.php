<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToLogStatusMachineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_status_machine', function (Blueprint $table) {
            $table->foreign(['machine_id'], 'log_status_machine_ibfk_1')->references(['id'])->on('machine')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_status_machine', function (Blueprint $table) {
            $table->dropForeign('log_status_machine_ibfk_1');
        });
    }
}
