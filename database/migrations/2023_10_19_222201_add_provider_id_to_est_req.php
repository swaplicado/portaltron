<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderIdToEstReq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('est_req', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id_n')->nullable()->after('external_id');

            $table->foreign('provider_id_n')->references('id_provider')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dps', function (Blueprint $table) {
            $table->dropForeign('provider_id_n')
            $table->dropColumn('provider_id_n');
        });
    }
}
