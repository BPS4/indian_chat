<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
        }
        .details, .items {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details td {
            padding: 5px;
        }
        .items th, .items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <!-- Hotel Logo -->
    <img src="{{ public_path('media/logos/hotel-logo.png') }}" alt="Hotel Logo">
    <h2>Payment Receipt</h2>
    <p>Transaction ID: {{ $payment->transaction_id }}</p>
</div>

<table class="details">
    <tr>
        <td><strong>Hotel Name:</strong> {{ $payment->booking?->hotel?->name ?? 'N/A' }}</td>
        <td><strong>Booking ID:</strong> {{ $payment->booking_id }}</td>
    </tr>
    <tr>
        <td><strong>Payment Method:</strong> {{ $payment->payment_method }}</td>
        <td><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td><strong>Status:</strong> {{ $payment->payment_status ? 'Success' : 'Failed' }}</td>
        <td><strong>Total Guests:</strong> {{ $payment->booking?->guests?->count() ?? 0 }}</td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th>Guest Name</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payment->booking?->guests ?? [] as $guest)
            <tr>
                <td>{{ $guest->guest_name }}</td>
                <td>{{ $payment->amount / ($payment->booking?->guests?->count() ?? 1) }} {{ $payment->currency }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="total">Total Amount: {{ $payment->amount }} {{ $payment->currency }}</p>

</body>
</html>
