<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpsReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dps_references', function (Blueprint $table) {
            $table->id('id_dps_reference');
            $table->unsignedBigInteger('dps_id');
            $table->unsignedBigInteger('reference_doc');
            $table->boolean('is_deleted');
            $table->timestamps();

            $table->foreign('dps_id')->references('id_dps')->on('dps');
            $table->foreign('reference_doc')->references('id_dps')->on('dps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dps_references');
    }
}
