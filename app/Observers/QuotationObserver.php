<?php

namespace App\Observers;

use App\Quotation;

class QuotationObserver
{
    public function updated (Quotation $quotation)
    {
        event(new authorize_quote($quotation));
    }
}
