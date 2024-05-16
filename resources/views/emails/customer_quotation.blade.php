<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="col-lg-6 col-sm-6">
        <div class="logo text-left" style="color: maroon; font-size: 24px;"> <!-- Adjust font size and color as needed -->
            VMC ENTERPRISES
        </div>
    </div>
    <img src="https://drscdn.500px.org/photo/1092866560/q%3D50_h%3D450_of%3D1/v2?sig=9493308b70e563e653e668be7f48f2f43b2176f05578cbe1c326d9ba40333f12" alt="Logo" style="max-height: 50px; margin-left: 10px;">

    <h2>Customer Quotation</h2>
    <table>
        <tr>
            <th>Reference</th>
            <td>{{ $quotation->reference }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ $quotation->date }}</td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>{{ $quotation->customer->name }}</td>
        </tr>
        <tr>
            <th>State</th>
            <td>
                @if ($quotation->status == 1)
                    SENT
                @else
                    PENDING
                @endif
            </td>
        </tr>
        <!-- Add more rows for other relevant quotation details -->
    </table>

    <h3>Quotation Items</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Sub Total</th>
  
        </tr>
        @foreach($quotation->quotationDetails as $detail)
        <tr>
            <td>{{ $detail->product->name }}</td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ $detail->unit_price }}</td>
            <td>{{ $detail->quantity * $detail->unit_price }}</td>
     
            
        </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align: right;">Total:</td>
            <td>{{ $quotation->total_amount }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: right;">Discount:</td>
            <td>{{ $quotation->discount_amount }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: right;">Grand Total:</td>
            <td>{{ $quotation->total_amount }}</td>
        </tr>
    </table>

    <ul>
        <li>Company: <a href="https://vmcenterprises.com/">VMC Enterprises</a></li>
        <li>Mail Address: <a href="mailto:vmcenterprises18@gmail.com">vmcenterprises18@gmail.com</a></li>
        <li>Contact us: <a href="tel:076 443 5438">076 443 5438</a></li>
    </ul>
</body>
</html>