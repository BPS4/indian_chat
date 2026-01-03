@extends('admin.layout.default')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@section('Payments', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Payment & Invoice
                </h3>
                <p>Manage transactions, refunds, and payment processing</p>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                {{-- <a href="" class="btn btn-primary font-weight-bolder">
                    <i class="bi bi-download "></i> Export Report</a> --}}
                <!-- <div>
                                        <img src="{{ asset('media/icons/card-icon.png') }}" alt="">
                                    </div> -->


            </div>
        </div>
        <div class="card-body">

            <div class=" py-4 mb-5">
                <div class="row g-3">

                    <!-- Total Revenue -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Total Revenue</span>
                                <!-- <i class="bi bi-house"></i> -->
                                <img src="{{ asset('media/icons/revenue.png') }}" alt="">

                            </div>
                            <h4 class="payment-card-amount py-4">₹{{ $totalPayments['amount'] }}</h4>
                            <p class="payment-card-subtext ">{{ $totalPayments['growth'] }}% from last month</p>
                        </div>
                    </div>

                    <!-- Successful Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title rupees">
                                <span>Successful Payments</span>
                                <img src="{{ asset('media/icons/rupees.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">{{ $successfulPayments['percentage'] }}%</h4>
                            <p class="payment-card-subtext">{{ $successfulPayments['success_count'] }} of
                                {{ $successfulPayments['total_transactions'] }} transactions</p>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Failed Payments</span>
                                <img src="{{ asset('media/icons/pending-payment.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">{{ $failedPayments['failed_count'] }}</h4>
                            <p class="payment-card-subtext">₹{{ $failedPayments['failed_value'] }} total value</p>
                        </div>
                    </div>

                    <!-- Refund Issued -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Refund Issued</span>
                                <img src="{{ asset('media/icons/refund.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">₹{{ $refundPayments['refund_this_month'] }}</h4>
                            <p class="payment-card-subtext">{{ $refundPayments['refund_amount'] }} Refund this Month</p>
                        </div>
                    </div>

                </div>
            </div>

           <form method="GET" action="">
    <div class="card-search mb-5">

        <!-- Search -->
        <div class="w-50 position-relative">
            <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
            <input
                class="form-control ps-25"
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by customer, hotel or booking ID"
            >
        </div>

        <div class="d-flex justify-content-between align-items-center gap-3 drops">

            <!-- Payment Status Filter -->
           @php
    $statusText = [
        '0' => 'Pending',
        '1' => 'Success',
    ];
@endphp

<div class="dropdown">
    <button class="btn btn-lg dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown">
        {{ $statusText[request('payment_status')] ?? 'All Status' }}
    </button>

    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
        <li>
            <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['payment_status' => '']) }}">
                All Status
            </a>
        </li>

        @foreach($booking_status as $status)
            <li>
                <a class="dropdown-item"
                   href="{{ request()->fullUrlWithQuery(['payment_status' => $status]) }}">
                    {{ $statusText[$status] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>


            <!-- Payment Method Filter -->
            <div class="dropdown">
                <button class="btn btn-lg dropdown-toggle" type="button" id="methodDropdown" data-bs-toggle="dropdown">
                    {{ request('payment_method') ?? 'All Methods' }}
                </button>

                <ul class="dropdown-menu" aria-labelledby="methodDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['payment_method' => '']) }}">All Methods</a></li>

                    @foreach($payment_method as $method)
                        <li>
                            <a class="dropdown-item"
                               href="{{ request()->fullUrlWithQuery(['payment_method' => $method]) }}">
                                {{ ucfirst($method) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Date Filter -->
            <div class="dropdown">
                <button class="btn btn-lg dropdown-toggle" type="button" id="dateDropdown" data-bs-toggle="dropdown">
                    {{ request('date_filter') ?? 'All Dates' }}
                </button>

                <ul class="dropdown-menu" aria-labelledby="dateDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['date_filter' => '']) }}">All Dates</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['date_filter' => 'today']) }}">Today</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['date_filter' => 'week']) }}">This Week</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['date_filter' => 'month']) }}">This Month</a></li>
                </ul>
            </div>

        </div>

    </div>
</form>

            <div id="tableView" class="payment-table-container p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 payment-table" id="myTable">
                        <thead class="payment-table-header">
                            <tr>
                                <th> ID</th>
                                <th>Transaction ID</th>
                                <th>Customer</th>
                                <th>Hotel</th>
                                <th>Booking ID</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>{{ $payment->booking?->guests?->first()->guest_name ?? null }}</td>
                                    <td>{{ $payment->booking?->hotel?->name }}</td>
                                    <td>{{ $payment->booking_id }}</td>
                                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                                    <td>{{ $payment->payment_method }} </td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                    <td><span
                                            class=" fw-semibold">{{ $payment->payment_status ? 'Success' : 'Pending' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('admin/Payments/payment-receipt-download', $payment->id) }}" class="text-primary">
                                            <i class="bi bi-download me-2" style="cursor: pointer;"></i>
                                        </a>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                       {{ $payments->links('pagination::bootstrap-5') }}

                </div>
            </div>


            {{-- {{ $details->links('pagination::bootstrap-5') }} --}}
            <!--end: Datatable-->
        </div>
    </div>


    <script>
        function changeStatus() {
            confirm("Do you want to change status?");
        }
    </script>
@endsection

{{-- Styles Section --}}
@section('styles')
    <!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
        $(document).ready(function() {
            // $('#myTable').DataTable();
            // $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
            // $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
            $('#myTable').DataTable({
                aLengthMenu: [
                    [25, 50, 100],
                    [25, 50, 100]
                ],
                pageLength: 25,
                language: {
                    lengthMenu: 'Show _MENU_ entries'
                }
            });
        });

        $('#toggleViewIcon').on('click', function() {
            const tableView = $('#tableView');
            const cardView = $('#cardView');
            const icon = $(this);

            if (tableView.is(':visible')) {
                // Switch to card view
                tableView.hide();
                cardView.show();
                icon.attr('src', '{{ asset('media/icons/table-icon.png') }}');
            } else {
                // Switch to table view
                cardView.hide();
                tableView.show();
                icon.attr('src', '{{ asset('media/icons/card-icon.png') }}');
            }
        });
    </script>
    {{-- vendors --}}
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
                        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
