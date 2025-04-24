<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notifyTesoreria extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider_name, $provider_rfc, $type = 1)
    {
        $this->provider_name = $provider_name;
        $this->provider_rfc = $provider_rfc;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type == 1) {
            $subject = '[PP] Nuevo registro de proveedor';
        } else {
            $subject = '[PP] Proveedor actualizado';
        }
        $email = "ordenes@aeth.mx";
        return $this->from($email)
                        ->subject($subject)
                        ->view('mails.notifyTesoreria')
                        ->with('provider_name', $this->provider_name)
                        ->with('rfc', $this->provider_rfc)
                        ->with('type', $this->type);
    }
}
