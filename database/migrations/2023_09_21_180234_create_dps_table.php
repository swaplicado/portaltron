<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dps', function (Blueprint $table) {
            $table->id('id_dps');
            $table->unsignedBigInteger('type_doc_id');
            $table->bigInteger('ext_id_year');
            $table->bigInteger('ext_id_doc');
            $table->string('pdf_url_n')->nullable();
            $table->string('xml_url_n')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->boolean('is_deleted');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
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
        Schema::dropIfExists('dps');
    }
}
