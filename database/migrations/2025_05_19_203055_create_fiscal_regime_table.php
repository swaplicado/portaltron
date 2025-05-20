<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiscalRegimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiscal_regime', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->boolean('fisica')->default(0);
            $table->boolean('moral')->default(0);
            $table->date('start_date_n')->nullable();
            $table->date('end_date_n')->nullable();
            $table->timestamps();
        });

        DB::table('fiscal_regime')->insert([
            [
                'key' => 601,
                'name' => 'General de Ley Personas Morales',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 603,
                'name' => 'Personas Morales con Fines no Lucrativos',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 605,
                'name' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 606,
                'name' => 'Arrendamiento',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 607,
                'name' => 'Régimen de Enajenación o Adquisición de Bienes',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 608,
                'name' => 'Demás ingresos',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 610,
                'name' => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 611,
                'name' => 'Ingresos por Dividendos (socios y accionistas)',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 612,
                'name' => 'Personas Físicas con Actividades Empresariales y Profesionales',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 614,
                'name' => 'Ingresos por intereses',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 615,
                'name' => 'Régimen de los ingresos por obtención de premios',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 616,
                'name' => 'Sin obligaciones fiscales',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 620,
                'name' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 621,
                'name' => 'Incorporación Fiscal',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 622,
                'name' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 623,
                'name' => 'Opcional para Grupos de Sociedades',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 624,
                'name' => 'Coordinados',
                'fisica' => 0,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 625,
                'name' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 626,
                'name' => 'Régimen Simplificado de Confianza',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiscal_regime');
    }
}
