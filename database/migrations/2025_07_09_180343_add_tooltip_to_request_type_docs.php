<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTooltipToRequestTypeDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_type_docs', function (Blueprint $table) {
            $table->string('tooltip', 255)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_type_docs', function (Blueprint $table) {
            $table->dropColumn('tooltip');
        });
    }
}
