<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build(){
        return $this->view('emails.verify_email')
                    ->from('postmaster@visas-premier.com')
                    ->subject('Confirma tu cuenta en Visas Premier')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token
                    ]);

    }
}
