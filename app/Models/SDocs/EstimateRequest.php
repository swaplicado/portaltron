<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateRequest extends Model
{
    use HasFactory;

    protected $table = "est_req";
    protected $primaryKey = "id_est_req";
    protected $fillable = [
        'id_est_req',
        'external_id',
        'provider_comment_n',
        'requester_comment_n',
        'is_opened',
        'status',
        'is_deleted',
        'created_by',
        'updated_by'
    ];
}
?>