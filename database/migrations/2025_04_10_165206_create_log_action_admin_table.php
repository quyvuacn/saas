<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogActionAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_action_admin', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('account_id')->index('log_action_admin_ibfk_1');
            $table->string('action', 255);
            $table->json('parameter')->nullable();
            $table->json('content_request')->nullable();
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_action_admin');
    }
}
