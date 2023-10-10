<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DpsComplementary extends Model
{
    use HasFactory;

    protected $table = "dps_complementary";
    protected $primaryKey = "id_comp";
    protected $fillable = [
        'dps_id',
        'reference_doc_n',
        'provider_comment_n',
        'requester_comment_n',
        'provider_date_n',
        'requester_date_n',
        'is_opened',
        'is_deleted',
        'created_by',
        'updated_by',
    ];
}
