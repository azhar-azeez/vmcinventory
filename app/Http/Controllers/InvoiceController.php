<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\StoreRentInvoiceRequest;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function create(StoreInvoiceRequest $request, Customer $customer)
    {
        $customer = Customer::where('id', $request->get('customer_id'))
            ->first();

        $carts = Cart::content();

        return view('invoices.create', [
            'customer' => $customer,
            'carts' => $carts
        ]);
    }

    public function create_rent(StoreRentInvoiceRequest $request, Customer $customer){

        $customer = Customer::where('id', $request->get('customer_id'))
            ->first();
        $carts = Cart::content();

        $rent_date = Carbon::parse($request->rent_date);
        $return_date = Carbon::parse($request->return_date);

        // Calculate day count
        if ($rent_date->eq($return_date)) {
            // If rent date and return date are the same, it's considered as one day
            $dayCount = 1;
        } else {
            // Otherwise, calculate the number of days between the dates
            $dayCount = $return_date->diffInDays($rent_date) + 1;
        }

// Loop through each item in the cart
foreach($carts as $item) {
    // Get the rental period (number of days) for the item
    $rentalPeriod = $dayCount ?? 1; // Default to 1 day if not set

    // Calculate subtotal for the item based on rental period
    $subtotal = $item->price * $rentalPeriod;

    // Update the subtotal for the item in the cart
    Cart::update($item->rowId, ['subtotal' => $subtotal]);
}

        return view('invoices.r_create', [
            'customer' => $customer,
            'carts' => $carts,
            'rent_date' => $rent_date,
            'return_date' => $return_date
        ]);
    }
}
