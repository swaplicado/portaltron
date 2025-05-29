<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiscalRegimeIdToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('company_fiscal_regime_id')->nullable()->after('company_rfc');
            $table->foreign('company_fiscal_regime_id')->references('id')->on('fiscal_regime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign('company_fiscal_regime_id');
            $table->dropColumn('company_fiscal_regime_id');
        });
    }
}
