<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentsToVoboDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vobo_docs', function (Blueprint $table) {
            $table->text('comments')->nullable()->after('date_rej_n');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vobo_docs', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
    }
}
