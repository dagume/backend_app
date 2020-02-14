<?php

namespace App\Mail;

use App\User;
use App\Document_reference;
use App\Quotation;
use App\Order;
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
    public $document_reference;
    public $quotation;
    public $order;

    public function __construct( Document_reference $document_reference, Quotation $quotation, Order $order)
    {
        $this->document_reference = $document_reference;
        $this->quotation = $quotation;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ( $this->order->state == 0) 
        {
            return $this->from('provisionalapp09@gmail.com', 'APP Control Ingenieria')
                    ->subject('Solicitud de cotizacion')
                    ->view('mails.RequestForQuotation');
        }
        return $this->from('provisionalapp09@gmail.com', 'APP Control Ingenieria')
                    ->subject('Orden de compra')
                    ->view('mails.BuyOrder');
       
        
    }
}
