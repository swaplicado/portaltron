<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestTypeDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_type_docs', function (Blueprint $table) {
            $table->id('id_request_type_doc');
            $table->string('name');
            $table->boolean('is_default')->default(0);
            $table->boolean('is_requirement')->default(0);
            $table->boolean('need_auth')->default(1);
            $table->boolean('is_deleted')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

        });

        DB::table('request_type_docs')->insert([
            [
                'name' => 'Opinión de cumplimiento de obligación fiscales',
                'is_default' => 1,
                'is_requirement' => 1,
                'need_auth' => 1,
                'is_deleted' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Constancia de situación fiscal',
                'is_default' => 1,
                'is_requirement' => 1,
                'need_auth' => 1,
                'is_deleted' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_docs');
    }
}
