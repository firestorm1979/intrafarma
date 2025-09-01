<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Notificacion extends Mailable
{
    use Queueable, SerializesModels;
    public $data, $gptest;
    /**
     * Create a new message instance.
     */
    public function __construct($data, $gptest)
    {
        $this->data = $data;
        $this->gptest = $gptest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificacion prueba',
        );
    }
    public function build()
    {
        return $this->markdown('admin.mails.notificacion')
                ->with('maildata', $this->data, $this->gptest);

    }

    /**
     * Get the message content definition.
     */
   

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
