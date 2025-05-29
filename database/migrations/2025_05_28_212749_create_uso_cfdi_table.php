<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsoCfdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uso_cfdi', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->boolean('fisica')->default(0);
            $table->boolean('moral')->default(0);
            $table->date('start_date_n')->nullable();
            $table->date('end_date_n')->nullable();
            $table->timestamps();
        });

        DB::table('uso_cfdi')->insert([
            [
                'key' => 'G01',
                'name' => 'Adquisición de mercancías',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'G02',
                'name' => 'Devoluciones, descuentos o bonificaciones',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'G03',
                'name' => 'Gastos en general',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I01',
                'name' => 'Construcciones',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I02',
                'name' => 'Mobiliario y equipo de oficina por inversiones',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I03',
                'name' => 'Equipo de transporte',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I04',
                'name' => 'Equipo de computo y accesorios',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I05',
                'name' => 'Dados, troqueles, moldes, matrices y herramental',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I06',
                'name' => 'Comunicaciones telefónicas',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'I08',
                'name' => 'Otra maquinaria y equipo',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D01',
                'name' => 'Honorarios médicos, dentales y gastos hospitalarios',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D02',
                'name' => 'Gastos médicos por incapacidad o discapacidad',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D03',
                'name' => 'Gastos funerales',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D04',
                'name' => 'Donativos',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D05',
                'name' => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D06',
                'name' => 'Aportaciones voluntarias al SAR',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D07',
                'name' => 'Primas por seguros de gastos médicos',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'D10',
                'name' => 'Pagos por servicios educativos (colegiaturas)',
                'fisica' => 1,
                'moral' => 0,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'S01',
                'name' => 'Sin efectos fiscales',
                'fisica' => 1,
                'moral' => 1,
                'start_date_n' => '2022-01-01',
                'end_date_n' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'CN01',
                'name' => 'Nómina',
                'fisica' => 1,
                'moral' => 1,
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
        Schema::dropIfExists('uso_cfdi');
    }
}
