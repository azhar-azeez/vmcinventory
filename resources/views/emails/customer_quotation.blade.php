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
            <th>Total Amount</th>
            <td>{{ $quotation->total_amount }}</td>
        </tr>
        <!-- Add more rows for other relevant quotation details -->
    </table>

    <h3>Quotation Items</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        @foreach($quotation->quotationDetails as $detail)
        <tr>
            <td>{{ $detail->product->name }}</td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ $detail->unit_price }}</td>
            <td>{{ $detail->quantity * $detail->unit_price }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>