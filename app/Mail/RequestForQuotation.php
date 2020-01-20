<?php

namespace App\Mail;

use App\User;
use App\Document_reference;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestForQuotation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $contact;
    public $document_reference;

    public function __construct(User $contact, Document_reference $document_reference)
    {
        $this->contact = $contact;
        $this->document_reference = $document_reference;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('provisionalapp09@gmail.com', 'APP Control Ingenieria')
                    ->subject('Solicitud de cotizacion')
                    ->view('mails.RequestForQuotation');
    }
}
