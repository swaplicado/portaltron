<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'adm_rol';
    protected $primaryKey = 'id_rol';
    protected $fillable = [
        'rol',
        'is_deleted',
    ];
}
