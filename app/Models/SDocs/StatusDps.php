<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDps extends Model
{
    use HasFactory;

    protected $table = "status_dps";
    protected $primaryKey = "id_status_dps";
    protected $fillable = [
        'id_status_dps',
        'name',
        'type_doc_id',
        'is_deleted'
    ];

}
