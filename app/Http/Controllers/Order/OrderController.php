<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use App\Mail\StockAlert;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Str;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->count();

        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
    
        $invalid_products = Product::where('user_id', auth()->id())
        ->where('product_type', 'rent')
        ->get();
        $invalidProductIds = $invalid_products->pluck('id')->toArray();

        $products = Product::where('user_id', auth()->id())
        ->where('product_type', 'retail')
        ->with(['category', 'unit'])
        ->get();
        
        $customers = Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $carts = Cart::content();

        foreach ($carts as $cart) {
            if (in_array($cart->id, $invalidProductIds)) {
                Cart::remove($cart->rowId);
            }
        }

        $carts = Cart::content();

        $carts->tax = 0;
        return view('orders.create', [
            'products' => $products,
            'customers' => $customers,
            'carts' => $carts,
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
            'pay' => $request->pay,
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING->value,
            'total_products' => Cart::count(),
            'sub_total' => Cart::subtotal(),
            'vat' => Cart::tax(),
            'total' => Cart::total(),
            'invoice_no' => IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]),
            'due' => (Cart::total() - $request->pay),
            'user_id' => auth()->id(),
            'uuid' => Str::uuid(),
        ]);

        // Create Order Details
        $contents = Cart::content();
        $contents->tax = 0;
        $oDetails = [];

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order['id'];
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->subtotal;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);
        }

        // Delete Cart Sopping History
        Cart::destroy();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been created!');
    }

    public function show($uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $order->loadMissing(['customer', 'details'])->get();
        return view('orders.show', [
            'order' => $order
        ]);
    }

    public function update($uuid, Request $request)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        // TODO refactoring

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order->id)->get();

        $stockAlertProducts = [];

        foreach ($products as $product) {
            $productEntity = Product::where('id', $product->product_id)->first();
            $newQty = $productEntity->quantity - $product->quantity;
            if ($newQty < $productEntity->quantity_alert) {
                $stockAlertProducts[] = $productEntity;
            }
            $productEntity->update(['quantity' => $newQty]);
        }

        if (count($stockAlertProducts) > 0) {
            $listAdmin = [];
            foreach (User::all('email') as $admin) {
                $listAdmin [] = $admin->email;
            }
            Mail::to($listAdmin)->send(new StockAlert($stockAlertProducts));
        }
        $order->update([
            'order_status' => OrderStatus::COMPLETE,
            'due' => '0',
            'pay' => $order->total
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy($uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $order->delete();
    }

    public function downloadInvoice($uuid)
    {
        $order = Order::with(['customer', 'details'])->where('uuid', $uuid)->firstOrFail();
        // TODO: Need refactor
        //dd($order);

        //$order = Order::with('customer')->where('id', $order_id)->first();
        // $order = Order::
        //     ->where('id', $order)
        //     ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        $order->update([
            'order_status' => 2
        ]);
        $orders = Order::where('user_id',auth()->id())->count();

        return redirect()
            ->route('orders.index', [
                'orders' => $orders
            ])
            ->with('success', 'Order has been canceled!');
    }



    public function getMonthlyRevenueData() {
        // Query to get daily revenue data
        $revenueData = DB::table('orders')
                        ->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(total) as revenue'))
                        ->groupBy('date')
                        ->get();

        return response()->json($revenueData);
    }

    public function getTopSoldProductsData(Request $request)
    {
        // Query to get top sold products data
        $topSoldProductsData = OrderDetails::join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5) // Adjust this limit as needed
            ->get();

        return response()->json($topSoldProductsData);
    }

    public function getSoldByTypeData()
    {
        // Initialize arrays
        $dataByDate = [];

        // Query to get sold quantities by product type
        $soldByTypeData = Product::select(DB::raw('DATE(created_at) as date'), 'product_type', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('date', 'product_type')
            ->get();

        // Organize data by date and product type
        foreach ($soldByTypeData as $entry) {
            $date = $entry->date;
            $productType = $entry->product_type;
            $quantity = $entry->total_quantity;

            // Add quantity to data array
            if (!isset($dataByDate[$date])) {
                $dataByDate[$date] = ['rent' => 0, 'retail' => 0];
            }

            $dataByDate[$date][$productType] += $quantity;
        }

        // Prepare data for response
        $dates = array_keys($dataByDate);
        $rentQuantities = array_column($dataByDate, 'rent');
        $retailQuantities = array_column($dataByDate, 'retail');

        $data = [
            'dates' => $dates,
            'rentQuantities' => $rentQuantities,
            'retailQuantities' => $retailQuantities,
        ];

        return response()->json($data);
    }

}
