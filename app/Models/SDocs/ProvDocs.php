<?php
namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvDocs extends Model
{
    protected $table = "prov_docs";
    protected $primaryKey = "id_prov_doc";
    protected $fillable = [
        'request_type_doc_id',
        'prov_id',
        'days_periodicity',
        'is_deleted',
        'created_by',
        'updated_by',
    ];
}