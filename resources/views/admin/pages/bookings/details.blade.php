@extends('admin.layout.default')
@section('bookings', 'active menu-item-open')

@section('content')
<div class="card card-custom">
    <div class="card-body">
        <div class="container">

            <h2 class="mb-4">Booking Details</h2>

            {{-- ============================
                BOOKING SUMMARY
            ============================ --}}
            <div class="card mb-4">
                <div class="card-header">Booking Summary</div>
                <div class="card-body">

                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Booking ID</th>
                                <td>{{ $booking->booking_id }}</td>
                            </tr>
                            <tr>
                                <th>User</th>
                                <td>{{ $booking->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Hotel</th>
                                <td>{{ $booking->hotel->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Check-in</th>
                                <td>{{ $booking->checkin_date }}</td>
                            </tr>
                            <tr>
                                <th>Check-out</th>
                                <td>{{ $booking->checkout_date }}</td>
                            </tr>
                            <tr>
                                <th>Total Nights</th>
                                <td>{{ $booking->total_nights }}</td>
                            </tr>
                            <tr>
                                <th>Total Guests</th>
                                <td>{{ $booking->total_guests }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ ucfirst($booking->status) }}</td>
                            </tr>
                            <tr>
                                <th>Base Room Charge</th>
                                <td>₹{{ number_format($booking->base_room_charges) }}</td>
                            </tr>
                            <tr>
                                <th>Taxes</th>
                                <td>₹{{ number_format($booking->taxes) }}</td>
                            </tr>
                            <tr>
                                <th>Hotel Discount</th>
                                <td>₹{{ number_format($booking->hotel_discount) }}</td>
                            </tr>
                            <tr>
                                <th>Coupon Discount</th>
                                <td>₹{{ number_format($booking->coupon_discount) }}</td>
                            </tr>
                            <tr>
                                <th>Total Payable</th>
                                <td>₹{{ number_format($booking->total_payable) }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>


            {{-- ============================
                ROOM DETAILS
            ============================ --}}
            <div class="card mb-4">
                <div class="card-header">Room Details</div>
                <div class="card-body">

                    @if($booking->rooms->isEmpty())
                        <p>No rooms found.</p>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Room Type</th>
                                    <th>Room Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->rooms as $index => $room)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $room->roomType->room_name ?? 'N/A' }}</td>
                                    <td>{{ $room->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>


            {{-- ============================
                ADDONS DETAILS
            ============================ --}}
            <div class="card mb-4">
                <div class="card-header">Addons Details</div>
                <div class="card-body">

                    @if($booking->addons->isEmpty())
                        <p>No addons found.</p>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Addon Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->addons as $index => $addon)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $addon->addon_name }}</td>
                                    <td>₹{{ number_format($addon->addon_price, 2) }}</td>
                                    <td>{{ $addon->quantity }}</td>
                                    <td>₹{{ number_format($addon->addon_price * $addon->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>


            {{-- ============================
                PAYMENT DETAILS
            ============================ --}}
            <div class="card mb-4">
                <div class="card-header">Payment Details</div>
                <div class="card-body">

                    @if($booking->payment)
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Payment ID</th>
                                <td>{{ $booking->payment->id }}</td>
                            </tr>
                            <tr>
                                <th>Amount Paid</th>
                                <td>₹{{ number_format($booking->payment->amount) }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td>{{ $booking->payment->payment_method }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $booking->payment->status }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                        <p>No payment record found.</p>
                    @endif

                </div>
            </div>


            {{-- ============================
                GUEST DETAILS
            ============================ --}}
            <div class="card mb-4">
                <div class="card-header">Guest Details</div>
                <div class="card-body">

                    @if($booking->guests->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Guest Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->guests as $index => $guest)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $guest->guest_name }}</td>
                                    <td>{{ $guest->email ?? 'N/A' }}</td>
                                    <td>{{ $guest->mobile ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No guests added.</p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
