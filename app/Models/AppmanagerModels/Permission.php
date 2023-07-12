<?php

namespace App\Models\AppmanagerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    
    protected $connection= 'mysqlmngr';
    protected $primaryKey = 'id_permission';
    protected $table = 'adm_permissions';
    public $timestamps = false;
}
