<?php

namespace App\Observers;

use App\Quotation;
use App\Events\authorize_quote;

class QuotationObserver
{
    public function updated (Quotation $update_quo)
    {
        dd($update_quo);
        event(new authorize_quote($update_quo));
        //event(new authorize_quote($quotation));
    }
}
