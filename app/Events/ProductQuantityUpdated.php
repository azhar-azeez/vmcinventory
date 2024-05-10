<?php

namespace App\Events;


use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductQuantityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $products;

    /**
     * Create a new event instance.
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

}
