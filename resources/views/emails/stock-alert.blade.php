<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Stock Alert') }}</title>
</head>
<body>
    <h3>{{ __('The following products are running low on stock:') }}</h3>
    <ul>
        @foreach ($products as $product)
            <li>
                {{ __('Product Name: ' . $product->name) }}<br>
                {{ __('Current Stock: ' . $product->quantity) }}<br>
                {{ __('Alert If Below: ' . $product->quantity_alert) }}<br>
            </li>
        @endforeach
    </ul>
</body>
</html>