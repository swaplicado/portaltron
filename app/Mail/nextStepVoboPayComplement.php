<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class nextStepVoboPayComplement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $provider_rfc)
    {
        $this->provider_name = $provider_name;
        $this->provider_rfc = $provider_rfc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '[PP] Nuevo comprobante de pago';
        $email = "ordenes@aeth.mx";
        return $this->from($email)
                        ->subject($subject)
                        ->view('mails.nextSteoVoboPayComplement')
                        ->with('provider_name', $this->provider_name)
                        ->with('rfc', $this->provider_rfc);
    }
}
