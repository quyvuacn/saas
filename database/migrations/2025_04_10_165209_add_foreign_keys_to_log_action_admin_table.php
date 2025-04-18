<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToLogActionAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_action_admin', function (Blueprint $table) {
            $table->foreign(['account_id'], 'log_action_admin_ibfk_1')->references(['id'])->on('admin')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_action_admin', function (Blueprint $table) {
            $table->dropForeign('log_action_admin_ibfk_1');
        });
    }
}
