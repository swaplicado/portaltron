<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class voboProviderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type_id, $provider_name, $status, $comments)
    {
        $this->type_id = $type_id;
        $this->provider_name = $provider_name;
        $this->status = $status;
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
        $typeConf = $mailConfig->where('type', $this->type_id)->first();
        $config = collect($typeConf->config)->first();

        switch ($this->status) {
            case 'APROBADO':
                $subject = '[PP] '.$this->provider_name.' aprobado';
                $is_enable = $config->approveProviderMail;
                break;
            case 'RECHAZADO':
                $subject = '[PP] '.$this->provider_name.' rechazado';
                $is_enable = $config->rejectProviderMail;
                break;
            case 'MODIFICAR':
                $subject = '[PP] '.$this->provider_name.' modificar datos';
                $is_enable = $config->modifyProviderMail;
                break;
            
            default:
                break;
        }

        if($is_enable){
            $email = "adrian.aviles.swaplicado@gmail.com";
            return $this->from($email)
                            ->subject($subject)
                            ->view('mails.voboProviderMail')
                            ->with('provider_name', $this->provider_name)
                            ->with('type_id', $this->type_id)
                            ->with('comments', $this->comments)
                            ->with('status', $this->status);
        }
    }
}
