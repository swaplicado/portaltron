<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('id_purchase_order');
            $table->unsignedBigInteger('dps_id');
            $table->text('provider_comment_n')->nullable();
            $table->text('requester_comment_n')->nullable();
            $table->date('provider_date_n')->nullable();
            $table->date('requester_date_n')->nullable();
            $table->boolean('is_opened');
            $table->boolean('is_deleted');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('dps_id')->references('id_dps')->on('dps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
