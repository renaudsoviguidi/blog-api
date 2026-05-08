<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationNewCompte extends Mailable
{
    use Queueable, SerializesModels;

    public $contenu;
    public $subject;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($contenu)
    {
        //
        $this->contenu = $contenu;
        $this->subject = $contenu['subject']; 
        $this->email = $contenu['email'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME")),
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.email_new_compte',
            with: [
                'nom_prenoms' => $this->contenu['nom_prenoms'],
                'email' => $this->contenu['email'],
                'activationUrl' => $this->contenu['activation_url'],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
