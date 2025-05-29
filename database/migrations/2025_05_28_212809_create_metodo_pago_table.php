<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetodoPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metodo_pago', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->date('start_date_n')->nullable();
            $table->date('end_date_n')->nullable();
            $table->timestamps();
        });

        DB::table('metodo_pago')->insert([
            [
                'key' => 'PUE',
                'name' => 'Pago en una sola exhibición',
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'PPD',
                'name' => 'Pago en parcialidades o diferido',
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
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
        Schema::dropIfExists('metodo_pago');
    }
}
