<?php

namespace App\Http\Controllers\Quotation;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Enums\OrderStatus;
use App\Mail\QuotationStatusMail;
use Illuminate\Support\Facades\Mail;
use App\Enums\QuotationStatus;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Http\Requests\Quotation\StoreQuotationRequest;
use Illuminate\Support\Facades\Request;
use Str;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Dompdf\Dompdf;
use Dompdf\Options;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where("user_id",auth()->id())->count();

        return view('quotations.index', [
            'quotations' => $quotations
        ]);
    }

    public function create()
    {
        Cart::instance('quotation')->destroy();

        return view('quotations.create', [
            'cart' => Cart::content('quotation'),
            'products' => Product::where("user_id",auth()->id())->get(),
            'customers' => Customer::where("user_id",auth()->id())->get(),

            // maybe?
            //'statuses' => QuotationStatus::cases()
        ]);
    }

    public function store(StoreQuotationRequest $request)
    {
        if (count(Cart::instance('quotation')->content()) === 0) {
            return redirect()->back()->with('message', 'Please search & select products!');
        }
        DB::transaction(function () use ($request) {
            $quotation = Quotation::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'customer_id' => $request->customer_id,
                'customer_name' => Customer::findOrFail($request->customer_id)->name,
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount, //* 100,
                'total_amount' => $request->total_amount, //* 100,
                'status' => $request->status,
                'note' => $request->note,
                "uuid" => Str::uuid(),
                "user_id" => auth()->id(),
                'tax_amount' => Cart::instance('quotation')->tax(), //* 100,
                'discount_amount' => Cart::instance('quotation')->discount(), //* 100,
            ]);

            foreach (Cart::instance('quotation')->content() as $cart_item) {
                QuotationDetails::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price, //* 100,
                    'unit_price' => $cart_item->options->unit_price, //* 100,
                    'sub_total' => $cart_item->options->sub_total, //* 100,
                    'product_discount_amount' => $cart_item->options->product_discount, //* 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax, //* 100,
                ]);
                //status = sent, reduce product quantity
                if ($request->status == 1) {
                    Product::where('id', $cart_item->id)->update(['quantity' => DB::raw('quantity-' . $cart_item->qty)]);
                }
            }

            Cart::instance('quotation')->destroy();
        });

        return redirect()
            ->route('quotations.index')
            ->with('success', 'Quotation Created!');
    }

    public function show($uuid)
    {
        $quotation = Quotation::where("user_id",auth()->id())->where('uuid', $uuid)->firstOrFail();

        return view('quotations.show', [
            'quotation' => $quotation,
            'quotation_details' => QuotationDetails::where('quotation_id', $quotation->id)->get()
        ]);
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->update([
            "status" => 2
        ]);
        $quotations = Quotation::where("user_id",auth()->id())->count();

        return redirect()
            ->route('quotations.index', [
                'quotations' => $quotations
            ]);
    }


     /**
     * Generate PDF from a view.
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function generatePdfFromView($view, $data = []) {
        $dompdf = new Dompdf();
        $html = view($view, $data)->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }

    /**
     * Complete quotation and convert to order.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    // complete quotaion method
    public function update(Request $request, $uuid)
    {
        $quotation = Quotation::where("user_id", auth()->id())->where('uuid', $uuid)->firstOrFail();
        $quotation->status = QuotationStatus::SENT;
        $quotation->save();

        // Convert the quotation to an order
        $order = $this->convertToOrder($quotation);

        // Generate PDF content from the customer_quotation view
        $pdfContent = $this->generatePdfFromView('emails.customer_quotation', ['quotation' => $quotation]);

        // Send email with attachment
        Mail::to($quotation->customer->email)
            ->send(new QuotationStatusMail($quotation, $pdfContent));

        return redirect()
            ->route('quotations.index')
            ->with('success', 'Quotation Completed!');

    }


    private function convertToOrder($quotation)
    {
        // Generate a unique invoice number
        $invoiceNo = IdGenerator::generate([
            'table' => 'orders',
            'field' => 'invoice_no',
            'length' => 10,
            'prefix' => 'INV-'
        ]);

        // Create a new Order instance
        $order = new Order([
            'customer_id' => $quotation->customer_id,
            'order_date' => $quotation->date,
            'order_status' => OrderStatus::PENDING, // Assuming OrderStatus::PENDING is the default status
            'total_products' => $quotation->quotationDetails()->sum('quantity'),
            'sub_total' => $quotation->total_amount,
            'vat' => 0, // Adjust based on your business logic
            'total' => $quotation->total_amount,
            'payment_type' => 'HandCash', // Set a default payment type
            'pay' => 0, // Initial payment amount
            'due' => $quotation->total_amount,
            'user_id' => auth()->id(),
            'uuid' => (string) Str::uuid(),
            'invoice_no' => $invoiceNo, // Assign the generated invoice number

        ]);

        // Save the order
        $order->save();

        // Copy quotation details to order details
        foreach ($quotation->quotationDetails as $detail) {
            // Create a new OrderDetails instance for each quotation detail
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unitcost' => $detail->unit_price, // Use appropriate field from QuotationDetail
                'total' => $detail->quantity * $detail->unit_price, // Calculate total
            ]);

        }

        return $order;
    }    

}
