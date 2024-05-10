<?php

namespace App\Console\Commands;

use App\Mail\StockAlert;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckStockAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send stock alert emails for products with low stock';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $lowStockProducts = Product::where(quantity, '<=', 'quantity_alert')->get();

        foreach ($lowStockProducts as $product) {
            Mail::to(config('azharazeez49@gmail.com'))->send(new StockAlert($product));
        }

        $this->info('Stock alert emails sent successfully');
    }
}
