<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceToDpsReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dps_references', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_doc')->nullable()->change();
            $table->string('reference_folio_n')->nullable()->after('reference_doc');
            $table->string('reference_num_ref_n')->nullable()->after('reference_doc');
            $table->string('reference_serie_n')->nullable()->after('reference_doc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dps_references', function (Blueprint $table) {
            $table->dropColumn('reference_folio_n');
            $table->dropColumn('reference_serie_n');
        });
    }
}
