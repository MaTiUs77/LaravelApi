<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->data["origin"] == "siep_pwa")
        {
            return self::pwaContactForm();
        }
    }

    private function pwaContactForm(){
        return $this->from('siep.pwa@siep.com','Siep Pwa')
            ->view('emails.contact_request')
            ->subject("PWA | Petición de Contacto")
            ->with(["data"=>$this->data]);
    }
}
