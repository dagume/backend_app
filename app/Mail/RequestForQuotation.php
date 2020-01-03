<?php

namespace App\Mail;

use App\User;
use App\Quotation;
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
    public $quotation;

    public function __construct(User $contact, Quotation $quotation)
    {
        $this->contact = $contact;
        $this->quotation = $quotation;
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
