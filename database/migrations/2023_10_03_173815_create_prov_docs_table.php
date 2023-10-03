<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prov_docs', function (Blueprint $table) {
            $table->id('id_prov_doc');
            $table->unsignedBigInteger('request_type_doc_id');
            $table->unsignedBigInteger('prov_id');
            $table->bigInteger('days_periodicity')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('request_type_doc_id')->references('id_request_type_doc')->on('request_type_docs');
            $table->foreign('prov_id')->references('id_provider')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prov_docs');
    }
}
