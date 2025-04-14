<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadDpsComplementaryConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_dps_complementary_config', function (Blueprint $table) {
            $table->id();
            $table->date('date_ini');
            $table->date('date_end');
            $table->string('time_ini')->nullable();
            $table->string('time_end')->nullable();
            $table->string('time_zone')->nullable();
            $table->integer('fiscal_type')->nullable(); //para saber si es persona fisica, moral o ambas
            $table->bigInteger('provider_id')->nullable();
            $table->boolean('is_deleted')->default(0);
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
        Schema::dropIfExists('upload_dps_complementary_config');
    }
}
