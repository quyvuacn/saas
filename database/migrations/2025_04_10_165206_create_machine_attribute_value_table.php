<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAttributeValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_attribute_value', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('machine_id');
            $table->bigInteger('attribute_id');
            $table->string('attribute_value', 100);
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->integer('is_deleted')->default(0)->comment('1 if it had been removed, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_attribute_value');
    }
}
