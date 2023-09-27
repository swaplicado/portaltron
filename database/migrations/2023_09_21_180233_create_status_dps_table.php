<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusDpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_dps', function (Blueprint $table) {
            $table->id('id_status_dps');
            $table->string('name');
            $table->unsignedBigInteger('type_doc_id');
            $table->boolean('is_deleted');
            $table->timestamps();

            $table->foreign('type_doc_id')->references('id_type')->on('type_doc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_dps');
    }
}
