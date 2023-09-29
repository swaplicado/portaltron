<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_providers', function (Blueprint $table) {
            $table->id('id_status_providers');
            $table->string('name');
            $table->timestamps();
        });

        \DB::table('status_providers')->insert([
            ['id_status_providers' => 1, 'name' => 'Pendiente'],
            ['id_status_providers' => 2, 'name' => 'Aprobado'],
            ['id_status_providers' => 3, 'name' => 'Rechazado'],
            ['id_status_providers' => 4, 'name' => 'Pendiente de modificar'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_providers');
    }
}
