<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginTokenMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $token;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Construye el mensaje del correo.
     */
    public function build()
    {
        return $this->subject('Tu código de verificación de acceso')
                    ->view('emails.login-token'); 
}