<?php
namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTypeDocs extends Model
{
    protected $table = "request_type_docs";
    protected $primaryKey = "id_request_type_doc";
    protected $fillable = [
        'name',
        'is_default',
        'is_requirement',
        'need_auth',
        'is_deleted',
        'created_by',
        'updated_by',
    ];
}