<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderFiscalRegimeIdToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_fiscal_regime_id')->nullable()->after('provider_rfc');
            $table->foreign('provider_fiscal_regime_id')->references('id')->on('fiscal_regime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            // $table->dropForeign(['provider_fiscal_regime_id']);
            $table->dropColumn('provider_fiscal_regime_id');
        });
    }
}
