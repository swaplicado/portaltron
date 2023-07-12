<?php

namespace App\Models\AppmanagerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SApp extends Model
{
    use HasFactory;

    protected $connection= 'mysqlmngr';
    protected $table = 'adm_apps';
    protected $primaryKey = 'id_app';
    public $timestamps = false;
}
