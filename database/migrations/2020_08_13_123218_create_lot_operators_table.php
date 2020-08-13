<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_operators', function (Blueprint $table) {
            $table->unsignedBigInteger('lot_id');
            $table->foreign('lot_id')->references('id')->on('lots')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->unsignedBigInteger('operator_id');
            $table->foreign('operator_id')->references('id')->on('operators')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lot_operators');
    }
}
