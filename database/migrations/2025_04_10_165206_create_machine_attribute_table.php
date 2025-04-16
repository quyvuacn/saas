<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_attribute', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('attribute_name', 100)->comment('Example cpu, ram, rom version...');
            $table->string('value_default', 100)->comment('the default of value');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('created_by');
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('is_deleted')->default(0)->comment('1 if record had been deleted, 0 if not');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_attribute');
    }
}
