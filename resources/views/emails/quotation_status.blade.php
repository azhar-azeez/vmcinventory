<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation Status</title>
</head>
<body>
    <img src="https://drscdn.500px.org/photo/1092866560/q%3D50_h%3D450_of%3D1/v2?sig=9493308b70e563e653e668be7f48f2f43b2176f05578cbe1c326d9ba40333f12" width="110" height="60" alt="VMC Logo">
    <p>Dear Customer,</p>
    <p>Your quotation with reference: {{ $quotation->reference }} has been updated.</p>
    <p>Status: {{ $quotation->status }}</p>
    <p>We have attached your quotation details down below. Please reply to this email for Approval</p>
    <p>Thank you. Have a wonderful day</p>

</body>
</html>