<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $connection = 'mysqlmngr';

    protected $primaryKey = 'id';
    protected $table = 'adm_users_typesuser';
    protected $fillable = [
        'user_id',
        'app_id',
        'typeuser_id',
        'created_at',
        'updated_at'
    ];
    
}
