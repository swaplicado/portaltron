<?php

namespace App\Models\SProviders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SProvider extends Model
{
    use HasFactory;

    protected $table  = 'providers';
    protected $primaryKey  = 'id_provider';
    protected $fillable = [
        'provider_name',
        'provider_short_name',
        'provider_rfc',
        'provider_email',
        'external_id',
        'is_active',
        'is_deleted',
        'created_by',
        'updated_by',
    ];
}
