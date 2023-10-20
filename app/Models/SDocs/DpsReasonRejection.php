<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DpsReasonRejection extends Model
{
    use HasFactory;

    protected $table = "dps_reasons_rejection";
    protected $primaryKey = "id_dps_reason_rejection";
    protected $fillable = [
        'type_doc_id_n',
        'reason',
        'count_usage',
        'is_active',
        'is_deleted',
    ];
}
