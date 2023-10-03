<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers_companies', function (Blueprint $table) {
            $table->id('id_prov_comp');
            $table->unsignedBigInteger('prov_id');
            $table->unsignedBigInteger('company_id');
            $table->boolean('is_deleted')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('prov_id')->references('id_provider')->on('providers');
            $table->foreign('company_id')->references('id_company')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers_companies');
    }
}
