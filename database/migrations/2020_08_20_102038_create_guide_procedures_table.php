<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuideProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guide_procedures', function (Blueprint $table) {
            $table->unsignedBigInteger('guide_id');
            $table->foreign('guide_id')->references('id')->on('guide_sadts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->unsignedBigInteger('procedure_id');
            $table->foreign('procedure_id')->references('id')->on('procedures')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->date('execution_date')->nullable()->default(now()->format('Y-m-d'));
            $table->unsignedFloat('reduction_factor', 3, 2)->nullable()->default(1.00);
            $table->smallInteger('request_amount');
            $table->smallInteger('permission_amount');
            $table->unsignedFloat('unity_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_procedures');
    }
}
