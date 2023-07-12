<?php

namespace App\Models\AppmanagerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Typesuser extends Model
{
    use HasFactory;

    protected $table = 'adm_typesuser';
    protected $primaryKey = 'id_typesuser';
}
