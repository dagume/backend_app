<?php

namespace App\Observers;

use App\Product;
use App\Events\StatusLiked;

class ProductObserver
{
    public function created (Product $product)
    {
        event(new StatusLiked('anonymus'));
    }
}
