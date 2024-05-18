<?php

namespace App\Http\Controllers\Product;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Str;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockAlert;
use App\Mail\TestEmail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Events\ProductQuantityUpdated;



class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where("user_id", auth()->id())->count();

        return view('products.index', [
            'products' => $products,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::where("user_id", auth()->id())->get(['id', 'name']);
        $units = Unit::where("user_id", auth()->id())->get(['id', 'name']);
        $productTypes = ['rent', 'retail'];

        if ($request->has('category')) {
            $categories = Category::where("user_id", auth()->id())->whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::where("user_id", auth()->id())->whereSlug($request->get('unit'))->get();
        }

        return view('products.create', [
            'categories' => $categories,
            'units' => $units,
            'productTypes' => $productTypes
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        /**
         * Handle upload image
         */
        $image = "";
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image')->store('products', 'public');
        }

        Product::create([
            "code" => IdGenerator::generate([
                'table' => 'products',
                'field' => 'code',
                'length' => 4,
                'prefix' => 'PC'
            ]),

            'product_image'     => $image,
            'name'              => $request->name,
            'category_id'       => $request->category_id,
            'unit_id'           => $request->unit_id,
            'quantity'          => $request->quantity,
            'buying_price'      => $request->buying_price,
            'selling_price'     => $request->selling_price,
            'quantity_alert'    => $request->quantity_alert,
            'tax'               => $request->tax,
            'tax_type'          => $request->tax_type,
            'notes'             => $request->notes,
            'product_type'      => $request->product_type,
            "user_id" => auth()->id(),
            "slug" => Str::slug($request->name, '-'),
            "uuid" => Str::uuid()
        ]);


        return to_route('products.index')->with('success', 'Product has been created!');
    }

    public function show($uuid)
    {
        $product = Product::where("uuid", $uuid)->firstOrFail();
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit($uuid)
    {
        $product = Product::where("uuid", $uuid)->firstOrFail();
        $productTypes = ['rent', 'retail'];
        return view('products.edit', [
            'categories' => Category::where("user_id", auth()->id())->get(),
            'units' => Unit::where("user_id", auth()->id())->get(),
            'product' => $product,
            'productTypes' => $productTypes
        ]);
    }

    public function update(UpdateProductRequest $request, $uuid)
    {
        $product = Product::where("uuid", $uuid)->firstOrFail();
        $product->update($request->except('product_image'));

        $image = $product->product_image;
        if ($request->hasFile('product_image')) {

            // Delete Old Photo
            if ($product->product_image) {
                unlink(public_path('storage/') . $product->product_image);
            }
            $image = $request->file('product_image')->store('products', 'public');
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name, '-');
        $product->category_id = $request->category_id;
        $product->unit_id = $request->unit_id;
        $product->quantity = $request->quantity;
        $product->buying_price = $request->buying_price;
        $product->selling_price = $request->selling_price;
        $product->quantity_alert = $request->quantity_alert;
        $product->tax = $request->tax;
        $product->product_type = $request->product_type;
        $product->tax_type = $request->tax_type;
        $product->notes = $request->notes;
        $product->product_image = $image;
        $product->save();

        $this->checkStockAlert($product);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been updated!');
    }

    public function destroy($uuid)
    {
        $product = Product::where("uuid", $uuid)->firstOrFail();
        /**
         * Delete photo if exists.
         */
        if ($product->product_image) {
            // check if image exists in our file system
            if (file_exists(public_path('storage/') . $product->product_image)) {
                unlink(public_path('storage/') . $product->product_image);
            }
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }

    public function checkStockAlert(Product $product)
    {
        if ($product->quantity <= $product->quantity_alert) {
            Log::info("Triggering ProductStockLow event for product: " . $product->name);
            event(new ProductQuantityUpdated($product));
        }
    }



    public function getProductsAvailableData()
    {
        /**
         * Pie chart of available products
         */
        $productsData = DB::table('products')
                        ->select('name', 'quantity')
                        ->get();

        return response()->json($productsData);
    }

    ///////////Stock Report///////////

    public function getProductStockReport()
    {
        return view('products.product-report');
    }
    
    public function generateStockReport(Request $request)
    {
        // Define the threshold for low stock quantity
        $lowStockThreshold = 10; // Adjust this threshold as needed

        // Fetch retail products with current stock below the threshold
        $products = Product::where('product_type', 'retail')
                            ->where('quantity', '<', $lowStockThreshold)
                            ->get();

        // Create an empty array to store the stock maintenance report
        $stockReport = [];

        // Iterate over each product to gather relevant information
        foreach ($products as $product) {
            // Fetch product details
            $currentStock = $product->quantity;
            $productName = $product->name;
            $productCode = $product->code;
            $quantityAlert = $product->quantity_alert;

            // Add product details to the stock maintenance report
            $stockReport[] = [
                'product_name' => $productName,
                'current_stock' => $currentStock,
                'code' => $productCode,
                'quantity_alert' => $quantityAlert,
            ];
        }

        // Export stock maintenance report as Excel file
        return $this->exportStockReportToExcel($stockReport);
    }

    private function exportStockReportToExcel($stockReport)
    {
        try {
            // Add column headings
            array_unshift($stockReport, [
                'Product Name',
                'Current Stock',
                'Product Code',
                'Quantity Alert',
            ]);
    
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray($stockReport, null, 'A1');
    
            $writer = new Xlsx($spreadsheet);
    
            $fileName = 'stock_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
    
            $writer->save($filePath);
    
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
