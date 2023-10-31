<?php

namespace App\Mail;

use App\Constants\SysConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class rejectDpsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $doc_type_id, $doc_type_name, $dps_folio, $comments)
    {
        $this->provider_name = $provider_name;
        $this->doc_type_id = $doc_type_id;
        $this->doc_type_name = $doc_type_name;
        $this->dps_folio = $dps_folio;
        $this->comments = $comments;
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

        if($config->rejectDpsMail){
            $email = "adrian.aviles.swaplicado@gmail.com";
            return $this->from($email)
                            ->subject('[PP] Rechazo de factura')
                            ->view('mails.rejectDpsMail')
                            ->with('provider_name', $this->provider_name)
                            ->with('doc_type_id', $this->doc_type_id)
                            ->with('doc_type_name', $this->doc_type_name)
                            ->with('dps_folio', $this->dps_folio)
                            ->with('comments', $this->comments);
        }
    }
}
