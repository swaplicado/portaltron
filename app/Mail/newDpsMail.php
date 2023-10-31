<?php

namespace App\Mail;

use App\Constants\SysConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class newDpsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $doc_type_id, $doc_type_name, $dps_folio, $lReferences)
    {
        $this->provider_name = $provider_name;
        $this->doc_type_id = $doc_type_id;
        $this->doc_type_name = $doc_type_name;
        $this->dps_folio = $dps_folio;
        $this->lReferences = $lReferences;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = \App\Utils\Configuration::getConfigurations();
        $mailConfig = collect($config->mailConfig);
        $typeConf = $mailConfig->where('type', $this->doc_type_id)->first();
        $config = collect($typeConf->config)->first();
        if($config->newDpsMail){
            $lConstants = [
                'FACTURA' => SysConst::DOC_TYPE_FACTURA,
                'NOTA_CREDITO' => SysConst::DOC_TYPE_NOTA_CREDITO,
                'COMPLEMENTO_PAGO' => SysConst::DOC_TYPE_COMPLEMENTO_PAGO,
            ];
    
            $references = "";
            foreach($this->lReferences as $index => $val){
                $references = $references.$val;
                if($index < (count($this->lReferences) - 1)){
                    $references = $references.", ";
                }
            }
            $email = "adrian.aviles.swaplicado@gmail.com";
            return $this->from($email)
                            ->subject('[PP] Nueva factura '.$this->provider_name)
                            ->view('mails.newDpsMail')
                            ->with('provider_name', $this->provider_name)
                            ->with('doc_type_id', $this->doc_type_id)
                            ->with('doc_type_name', $this->doc_type_name)
                            ->with('dps_folio', $this->dps_folio)
                            ->with('references', $references)
                            ->with('lConstants', $lConstants);
        }
    }
}
