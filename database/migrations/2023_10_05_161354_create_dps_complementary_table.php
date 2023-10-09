<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpsComplementaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dps_complementary', function (Blueprint $table) {
            $table->id('id_comp');
            $table->unsignedBigInteger('dps_id');
            $table->unsignedBigInteger('reference_doc_n');
            $table->string('provider_comment_n');
            $table->string('requester_comment_n');
            $table->date('provider_date_n');
            $table->date('requester_date_n');
            $table->boolean('is_opened');
            $table->boolean('is_deleted');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('dps_id')->references('id_dps')->on('dps');
            $table->foreign('reference_doc_n')->references('id_dps')->on('dps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dps_complementary');
    }
}
