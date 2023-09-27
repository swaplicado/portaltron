<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDoc extends Model
{
    use HasFactory;

    protected $table = "type_doc";
    protected $primaryKey = "id_type";
    protected $fillable = [
        'name_type',
        'ext_id_n',
        'is_deleted',
        'created_by',
        'updated_by'
    ];
}
