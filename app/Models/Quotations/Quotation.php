<?php

namespace App\Models\Quotations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';
    protected $primaryKey = 'id_quotation';
    protected $fillable = [
        'provider_id',
        'folio_system',
        'folio_user',
        'description',
        'pdf_path',
        'pdf_original_name',
        'created_by',
        'updated_by',
    ];
}
