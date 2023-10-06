<?php
namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocsUrl extends Model
{
    protected $table = "docs_url";
    protected $primaryKey = "id_doc_url";
    protected $fillable = [
        'prov_doc_id',
        'url',
        'date_ini_n',
        'date_fin_n',
        'is_deleted',
        'created_by',
        'updated_by',
    ];
}