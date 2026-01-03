@extends('admin.layout.default')

@section('customers', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="customer-profile">
                    @if ($user)
                       @php
                            $totalSpent = $user->bookings->sum(fn($b) => $b->payment->amount ?? 0);
                            $totalBookings = $user->bookings->count();
                        @endphp

                        <!-- Header -->
                        <div class="profile-header">
                            <img src="{{ asset('media/icons/bussiness-man.png') }}" alt="User">
                            <div>
                                <h4>{{ $user->name ?? 'N/A' }}</h4>
                                <p class="text-muted mb-0">Customer profile and booking history</p>
                            </div>
                        </div>

                        <!-- Info Cards -->
                        <div class="info-card">
                            <div class="row text-center text-md-start">
                                <!-- Contact Info -->
                                <div class="col-md-4 contact-info">
                                    <h6 class="fw-bold mb-5">Contact Information</h6>
                                    <p><span><img src="{{ asset('media/icons/call.png') }}" alt=""></span>
                                        {{ $user->mobile ?? 'N/A' }}</p>
                                    <p><span><img src="{{ asset('media/icons/mail.png') }}" alt=""></span>
                                        {{ $user->email ?? 'N/A' }}</p>
                                    <p><span><img src="{{ asset('media/icons/ep_location.png') }}" alt=""></span>
                                        {{ $user->location ?? 'N/A' }}</p>
                                </div>

                                <!-- Booking Stats -->
                                <div class="col-md-4 contact-info booking-stats">
                                    <h6 class="fw-bold mb-5">Booking Stats</h6>
                                    <p>Total Booking<br><strong class="float-start">{{ $totalBookings }}</strong></p>
                                    <p>Total Spent<br><strong
                                            class="float-start">₹{{ number_format($totalSpent, 2) }}</strong></p>
                                    <p>Join Date<br><strong
                                            class="float-start">{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</strong>
                                    </p>
                                </div>

                                <!-- Loyalty Points -->
                                <div class="col-md-4 contact-info">
                                    <h6 class="fw-bold mb-5">Loyalty Points</h6>
                                    <div class="loyalty-circle">
                                        <h4 class="mb-0">0</h4>
                                        <small class="text-muted">Current Balance</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking History -->
                        <div class="booking-history info-card">
                            <h6 class="fw-bold mb-3 fs-4">Booking History</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Room</th>
                                            <th>Check-In</th>
                                            <th>Check-Out</th>
                                            <th>Amount</th>
                                            <th>Transaction Id</th>
                                            <th>Payment Status</th>
                                            {{-- <th>Actions</th> --}}
                                        </tr>
                                    </thead>
                                   <tbody>
    @forelse ($user->bookings as $booking)

        <tr>
            <td>{{ $booking->booking_id }}</td>

            <td>
                {{-- Show room type (first room) --}}
                {{ $booking->rooms->first()->roomType->room_name ?? 'N/A' }}
            </td>

            <td>
                {{ $booking->checkin_date
                    ? \Carbon\Carbon::parse($booking->checkin_date)->format('d-m-Y')
                    : 'N/A' }}
            </td>

            <td>
                {{ $booking->checkout_date
                    ? \Carbon\Carbon::parse($booking->checkout_date)->format('d-m-Y')
                    : 'N/A' }}
            </td>

            <td>
                {{ $booking->payment->amount
                    ? '₹' . number_format($booking->payment->amount, 2)
                    : 'N/A' }}
            </td>

            <td>{{ $booking->payment->transaction_id ?: 'N/A' }}</td>

            <td>
                @if ($booking->payment)
                    @if ($booking->payment->payment_status == 1)
                        Confirmed
                    @elseif ($booking->payment->payment_status == 0)
                        Pending
                    @else
                        N/A
                    @endif
                @else
                    N/A
                @endif
            </td>
        </tr>

    @empty
        <tr>
            <td colspan="7" class="text-center">No Data Found</td>
        </tr>
    @endforelse
</tbody>


                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p>No customer data found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
