<?php

namespace App\Models\SDocs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrders extends Model
{
    use HasFactory;

    protected $table = "purchase_orders";
    protected $primaryKey = "id_purchase_order";
    protected $fillable = [
        'id_purchase_order',
        'dps_id',
        'provider_comment_n',
        'requester_comment_n',
        'provider_date_n',
        'requester_date_n',
        'is_opened',
        'is_deleted',
        'created_by',
        'updated_by'
    ];
}
