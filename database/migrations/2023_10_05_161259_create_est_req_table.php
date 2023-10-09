<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstReqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('est_req', function (Blueprint $table) {
            $table->id('id_est_req');
            $table->unsignedBigInteger('external_id');
            $table->string('provider_comment_n')->nullable();
            $table->string('requester_comment_n')->nullable();
            $table->boolean('is_opened');
            $table->integer('status');
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
        Schema::dropIfExists('est_req');
    }
}
