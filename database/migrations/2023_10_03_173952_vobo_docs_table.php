<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VoboDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vobo_docs', function (Blueprint $table) {
            $table->id('id_vobo');
            $table->unsignedBigInteger('doc_url_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_accept');
            $table->boolean('is_reject');
            $table->date('date_accept_n')->nullable();
            $table->date('date_rej_n')->nullable();
            $table->integer('order');
            $table->boolean('is_deleted')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('area_id')->references('id_area')->on('areas');
            $table->foreign('doc_url_id')->references('id_doc_url')->on('docs_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vobo_docs');
    }
}
