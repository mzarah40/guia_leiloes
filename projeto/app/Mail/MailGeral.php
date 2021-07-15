<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Imovel;

class MailGeral extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $cliAviso;
    private $imovAviso;

    public function __construct(\stdClass $cliAviso, array $imovelAviso)
    {
        //
        $this->cliAviso  = $cliAviso;
        $this->imovAviso = $imovelAviso;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject("E-mail GuiaLeilões");
        $this->to($this->cliAviso->email, "Guia Leilões");

        return $this->markdown('mail.MailGeral', [
            'imovel_aviso'  => $this->imovAviso
        ]);

        
    }
}
