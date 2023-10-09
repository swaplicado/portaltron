<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoboDpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vobo_dps', function (Blueprint $table) {
            $table->id('id_vobo');
            $table->unsignedBigInteger('dps_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_accept');
            $table->boolean('is_reject');
            $table->date('date_accept_n');
            $table->date('date_rej_n');
            $table->integer('order');
            $table->integer('check_status');
            $table->boolean('is_deleted');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('dps_id')->references('id_dps')->on('dps');
            $table->foreign('area_id')->references('id_area')->on('areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vobo_dps');
    }
}
