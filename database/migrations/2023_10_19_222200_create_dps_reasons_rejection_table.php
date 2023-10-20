<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpsReasonsRejectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dps_reasons_rejection', function (Blueprint $table) {
            $table->id('id_dps_reason_rejection');
            $table->unsignedBigInteger('type_doc_id_n')->nullable();
            $table->text('reason');
            $table->bigInteger('count_usage')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();

            $table->foreign('type_doc_id_n')->references('id_type')->on('type_doc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dps_reasons_rejection');
    }
}
