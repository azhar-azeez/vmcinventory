<?php

namespace App\Listeners;

use App\Models\Product;
use App\Events\ProductQuantityUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockAlert;
use Illuminate\Support\Facades\Log;


class SendStockAlertEmail implements ShouldQueue
{

    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductQuantityUpdated $event)
    {
        $products = $event->products; 

        Log::info("SendStockAlertEmail listener executed for product: " . $event->products->name);

        if ($products->quantity <= $products->quantity_alert) {
            Mail::to('azharazeez49@gmail.com')->send(new StockAlert($products));
        }
    }

}
