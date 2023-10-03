<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocsUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs_url', function (Blueprint $table) {
            $table->id('id_doc_url');
            $table->unsignedBigInteger('prov_doc_id');
            $table->string('url');
            $table->date('date_ini_n')->nullable();
            $table->date('date_fin_n')->nullable();
            $table->boolean('is_deleted')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('prov_doc_id')->references('id_prov_doc')->on('prov_docs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docs_url');
    }
}
