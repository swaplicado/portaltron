<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class nextStepVoboProviderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $provider_rfc, $area_name)
    {
        $this->provider_name = $provider_name;
        $this->provider_rfc = $provider_rfc;
        $this->area_name = $area_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '[PP] Nuevo registro de proveedor';
        $email = "apps.swaplicado@gmail.com";
        return $this->from($email)
                        ->subject($subject)
                        ->view('mails.nextStepVoboProvider')
                        ->with('provider_name', $this->provider_name)
                        ->with('rfc', $this->provider_rfc)
                        ->with('area_name', $this->area_name);
    }
}
