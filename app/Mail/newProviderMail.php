<?php

namespace App\Mail;

use App\Constants\SysConst;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class newProviderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $rfc)
    {
        $this->provider_name = $provider_name;
        $this->rfc = $rfc;
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
        $typeConf = $mailConfig->where('type', SysConst::MAIL_PROVEEDOR)->first();
        $config = collect($typeConf->config)->first();

        $is_enable = $config->newProviderMail;
        $subject = '[PP] Nuevo registro de proveedor';

        if($is_enable){
            $email = "adrian.aviles.swaplicado@gmail.com";
            return $this->from($email)
                            ->subject($subject)
                            ->view('mails.newProviderMail')
                            ->with('provider_name', $this->provider_name)
                            ->with('rfc', $this->rfc);
        }
    }
}
