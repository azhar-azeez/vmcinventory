<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public array $products; // Expect an array of Product instances
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */

     public function __construct(array $products) {
         $this->products = $products; // Store the list of products
     }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Stock Alert: Product Quantity Low')
                    ->view('emails.stock-alert', ['products' => $this->products]);

    }
    
}
