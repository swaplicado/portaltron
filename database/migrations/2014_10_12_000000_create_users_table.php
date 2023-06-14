<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('names');
            $table->string('full_name');
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->rememberToken();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_deleted');
            $table->timestamps();

            $table->foreign('rol_id')->references('id_rol')->on('adm_rol');
            $table->foreign('provider_id')->references('id_provider')->on('providers');
        });

        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'admin',
                'email' => 'adrian.aviles@swaplicado.com.mx',
                'password' => \Hash::make('123456'),
                'first_name' => 'admin',
                'last_name' => 'admin',
                'names' => 'admin',
                'full_name' => 'Admin',
                'rol_id' => 1,
                'provider_id' => null,
                'remember_token' => null,
                'is_active' => 1,
                'is_deleted' => 0,
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
        Schema::dropIfExists('users');
    }
}
