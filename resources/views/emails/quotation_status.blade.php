<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation Status</title>
</head>
<body>
    <p>Dear Customer,</p>
    <p>Your quotation with reference: {{ $quotation->reference }} has been updated.</p>
    <p>Status: {{ $quotation->status }}</p>
    <p>Thank you.</p>
</body>
</html>