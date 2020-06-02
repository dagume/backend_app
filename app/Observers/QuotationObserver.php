<?php

namespace App\Observers;

use App\Quotation;
use App\Events\authorize_quote;

class QuotationObserver
{
    public function updated (Quotation $quotation)
    {
        event(new StatusLiked('anonymus'));
        //event(new authorize_quote($quotation));
    }
}
