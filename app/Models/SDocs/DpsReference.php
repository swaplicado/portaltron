<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DpsReference extends Model
{
    use HasFactory;

    protected $table = "dps_references";
    protected $primaryKey = "id_dps_reference";
    protected $fillable = [
        'dps_id',
        'reference_doc',
        'reference_serie_n',
        'reference_num_ref_n',
        'reference_folio_n',
        'is_deleted',
    ];
}
