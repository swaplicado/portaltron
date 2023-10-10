<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dps extends Model
{
    use HasFactory;

    protected $table =  "dps";
    protected $primaryKey = "id_dps";
    protected $fillable = [
        'id_dps',
        'type_doc_id',
        'ext_id_year',
        'ext_id_doc',
        'provider_id',
        'pdf_url_n',
        'xml_url_n',
        'status_id',
        'is_deleted',
        'created_by',
        'updated_by'
    ];
}
