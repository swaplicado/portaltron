<?php namespace App\Utils;

use App\Models\Quotations\Quotation;

class QuotationsUtils {
    public static function getlQuotationsByUser($user_id){
        $lQuotations = Quotation::join('providers as p', 'p.id_provider', '=', 'provider_id')
                                ->where('p.user_id', $user_id)
                                ->select(
                                    'id_quotation',
                                    'provider_id',
                                    'pdf_original_name',
                                    'folio_system',
                                    'folio_user',
                                    'description',
                                )
                                ->get();

        return $lQuotations;
    }
}