<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsDpsComplementary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dps_complementary', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_doc_n')->nullable()->change();
            $table->string('provider_comment_n')->nullable()->change();
            $table->string('requester_comment_n')->nullable()->change();
            $table->date('provider_date_n')->nullable()->change();
            $table->date('requester_date_n')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
