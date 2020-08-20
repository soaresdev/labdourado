<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuideSadtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guide_sadts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->foreign('lot_id')->references('id')->on('lots')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->string('provider_number')->nullable();
            $table->string('main_number')->nullable();
            $table->date('permission_date')->nullable();
            $table->string('password')->nullable();
            $table->date('password_expiration')->nullable();
            $table->string('guide_operator_number')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->enum('rn', [
                'N',
                'S'
            ])->nullable()->default('N');
            $table->enum('type_requester', [
                'provider',
                'doctor'
            ])->nullable()->default('doctor');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->foreign('doctor_id')->references('id')->on('doctors')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->enum('character_treatment', [
                '1',
                '2'
            ])->nullable()->default('1');
            $table->date('request_date')->nullable();
            $table->text('clinical_indication')->nullable();
            $table->enum('type_treatment', [
                '01',
                '02',
                '03',
                '04',
                '05',
                '06',
                '07',
                '08',
                '09',
                '10',
                '11',
                '13',
                '14',
                '15',
                '16',
                '17',
                '18',
                '19',
                '20',
                '21',
                '22',
            ])->nullable()->default('05');
            $table->enum('accident_indication', [
                '0',
                '1',
                '2',
                '9'
            ])->nullable()->default('9');
            $table->unsignedFloat('total', 10, 2)->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guide_sadts');
    }
}
