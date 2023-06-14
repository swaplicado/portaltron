<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_rol', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('rol');
            $table->boolean('is_deleted');
            $table->timestamps();
        });

        DB::table('adm_rol')->insert([
            ['id_rol' => 1, 'rol' => 'Admin', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s') ],
            ['id_rol' => 2, 'rol' => 'Proveedor', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s') ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_rol');
    }
}
