<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFolioNToDps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dps', function (Blueprint $table) {
            $table->string('folio_n')->nullable()->after('provider_id_n');
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
            $table->dropColumn('folio_n');
        });
    }
}
