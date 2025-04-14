<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadDpsComplementaryConfig extends Model
{
    use HasFactory;

    protected $table = 'upload_dps_complementary_config';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'date_ini',
        'date_end',
        'time_ini',
        'time_end',
        'time_zone',
        'fiscal_type',
        'provider_id'
    ];
}
