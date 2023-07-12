<?php

namespace App\Models\AppmanagerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_role';
    protected $table = 'adm_roles';
    public $timestamps = false;
}
