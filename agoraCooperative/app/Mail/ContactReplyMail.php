<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $reponse;

    /**
     * Create a new message instance.
     */
    public function __construct($contact, $reponse)
    {
        $this->contact = $contact;
        $this->reponse = $reponse;
    }

    /**
     * Build the message.
     * Cette méthode est la plus compatible et résoudra l'erreur "Invalid View"
     */
    public function build()
    {
        return $this->subject('Réponse à votre message : ' . $this->contact->sujet)
                    ->view('emails.contact-reply'); 
    }
}