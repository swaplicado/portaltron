<?php
namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoboDoc extends Model
{
    protected $table = "vobo_docs";
    protected $primaryKey = "id_vobo";
    protected $fillable = [
        'doc_url_id',
        'area_id',
        'user_id',
        'is_accept',
        'is_reject',
        'date_accept_n',
        'date_rej_n',
        'order',
        'check_status',
        'is_deleted',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}