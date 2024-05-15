<?php

namespace App\Http\Controllers\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rent;
use App\Models\Product;
use App\Models\Customer;
use App\Models\RentDetails;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Requests\Rent\RentStoreRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Str;
use Carbon\Carbon;

class RentalController extends Controller
{
    //List Rentals
    public function index()
    {
        $rents = Rent::where('user_id', auth()->id())->count();

        return view('rents.index', [
            'rents' => $rents
        ]);
    }


    public function create()
    {
        // Get products that are available for rent
        $availableProducts = Product::where('user_id', auth()->id())
        ->where('product_type', 'rent')
        ->whereNotIn('id', function($query) {
            $query->select('product_id')
                ->from('rent_details')
                ->join('rents', 'rent_details.rent_id', '=', 'rents.id')
                ->where('rents.return_date', '>=', now()->toDateString());
        })
        ->with(['category', 'unit'])
        ->get();

        $products = Product::where('user_id', auth()->id())
        ->where('product_type', 'rent')
        ->with(['category', 'unit'])
        ->get();

        $invalid_products = Product::where('user_id', auth()->id())
        ->where('product_type', 'retail')
        ->get();
        $invalidProductIds = $invalid_products->pluck('id')->toArray();

        $customers = Customer::where('user_id', auth()->id())->get(['id', 'name']);


        $carts = Cart::content();

        foreach ($carts as $cart) {
            if (in_array($cart->id, $invalidProductIds)) {
                Cart::remove($cart->rowId);
            }
        }

        $carts = Cart::content();
        
        $carts->tax = 0;

        return view('rents.create', [
            'products' => $availableProducts,
            'customers' => $customers,
            'carts' => $carts,
        ]);

    }

    public function store(RentStoreRequest $request)
    {
        $rent = Rent::create([
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
            'pay' => $request->pay,
            'rent_date' => $request->rent_date,
            'return_date' => $request->return_date,
            'total_products' => Cart::count(),
            'sub_total' => Cart::subtotal(),
            'vat' => Cart::tax(),
            'total' => Cart::total(),
            'invoice_no' => IdGenerator::generate([
                'table' => 'rents',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]),
            'user_id' => auth()->id(),
            'uuid' => Str::uuid(),
        ]);

        // Create Order Details
        $contents = Cart::content();
        $contents->tax = 0;
        $rDetails = [];

        foreach ($contents as $content) {
            $rDetails['rent_id'] = $rent['id'];
            $rDetails['product_id'] = $content->id;
            $rDetails['quantity'] = $content->qty;
            $rDetails['per_day_price'] = $content->price;
            $rDetails['total'] = $content->subtotal;
            $rDetails['created_at'] = Carbon::now();

            RentDetails::insert($rDetails);
        }

        // Delete Cart Sopping History
        Cart::destroy();

        return redirect()
            ->route('rents.index')
            ->with('success', 'Rental has been created!');
    }


    public function show($uuid)
    {
        $rent = Rent::where('uuid', $uuid)->firstOrFail();
        $rent->loadMissing(['customer', 'details'])->get();


        // Calculate day count
        $dayCount = 1;
        if ($rent->rent_date->eq($rent->return_date)) {
            // If rent date and return date are the same, it's considered as one day
            $dayCount = 1;
        } else {
            // Otherwise, calculate the number of days between the dates
            $dayCount = $rent->return_date->diffInDays($rent->rent_date) + 1;
        }
        $rent->days = $dayCount;

        return view('rents.show', [
            'rent' => $rent
        ]);
    }

    public function downloadInvoice($uuid)
    {
        $rent = Rent::with(['customer', 'details'])->where('uuid', $uuid)->firstOrFail();

        // Calculate day count
        $dayCount = 1;
        if ($rent->rent_date->eq($rent->return_date)) {
            // If rent date and return date are the same, it's considered as one day
            $dayCount = 1;
        } else {
            // Otherwise, calculate the number of days between the dates
            $dayCount = $rent->return_date->diffInDays($rent->rent_date) + 1;
        }
        $rent->days = $dayCount;

        return view('rents.print-invoice', [
            'rent' => $rent,
        ]);
    }

    public function destroy($uuid)
    {
        $rent = Rent::where('uuid', $uuid)->firstOrFail();
        $rent->delete();

        return redirect()
        ->route('rents.index')
        ->with('success', 'Rental has been deleted!');
    }

}
