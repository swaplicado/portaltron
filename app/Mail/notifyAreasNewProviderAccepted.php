<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notifyAreasNewProviderAccepted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $provider_rfc, $is_accepted)
    {
        $this->provider_name = $provider_name;
        $this->provider_rfc = $provider_rfc;
        $this->is_accepted = $is_accepted;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Nuevo proveedor aceptado";

        $email = "apps.swaplicado@gmail.com";
        return $this->from($email)
                        ->subject($subject)
                        ->view('mails.notifyAreasNewProviderAccepted')
                        ->with('provider_name', $this->provider_name)
                        ->with('rfc', $this->provider_rfc)
                        ->with('is_accepted', $this->is_accepted);
    }
}
