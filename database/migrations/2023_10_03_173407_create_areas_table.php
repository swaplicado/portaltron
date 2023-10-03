<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id('id_area');
            $table->string('name_area');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted')->default(0);
            
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });

        DB::table('areas')->insert([
            [
                'name_area' => 'Compras',
                'is_active' => 1,
                'is_deleted' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name_area' => 'Mantenimiento',
                'is_active' => 1,
                'is_deleted' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name_area' => 'Proyectos',
                'is_active' => 1,
                'is_deleted' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name_area' => 'Contabilidad',
                'is_active' => 1,
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
        Schema::dropIfExists('areas');
    }
}
