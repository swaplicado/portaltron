<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class providerAccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = "apps.swaplicado@gmail.com";
        return $this->from($email)
                        ->subject('[PP] Activación de cuenta')
                        ->view('mails.providerAccountActivation')
                        ->with('route', $this->route);
    }
}
