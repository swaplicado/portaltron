<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class nextStepVoboDpsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $provider_rfc, $type_dps)
    {
        $this->provider_name = $provider_name;
        $this->provider_rfc = $provider_rfc;
        $this->type_dps = $type_dps;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type_dps == 2) {
            $subject = '[PP] Nuevo comprobante de pago';
        } elseif ($this->type_dps == 3) {
            $subject = '[PP] Nueva nota de crédito';
        }
        $email = "ordenes@aeth.mx";
        return $this->from($email)
                        ->subject($subject)
                        ->view('mails.nextStepVoboDps')
                        ->with('provider_name', $this->provider_name)
                        ->with('rfc', $this->provider_rfc)
                        ->with('type_dps', $this->type_dps);
    }
}
