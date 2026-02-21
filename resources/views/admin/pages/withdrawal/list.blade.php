@extends('admin.layout.default')

@section('bookings', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Withdrawal Request
                </h3>
                <p>Manage withdrawal request</p>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                {{-- <a href="{{ url('/admin/bookings/add') }}" class="btn btn-primary font-weight-bolder">
                + New Booking</a> --}}
                <!-- <div>
                                        <img src="{{ asset('media/icons/card-icon.png') }}" alt="">
                                    </div> -->

                <!--end::Button-->
            </div>

        </div>
        <div class="card-body">
            <!--begin: Datatable-->
            <form method="GET" action="{{ url('/admin/bookings/list') }}">
                <div class="card-search mb-5">

                    <!-- Search input -->
                    <div class="w-50 position-relative">
                        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                        <input class="form-control ps-25" type="search" name="search" value="{{ request('search') }}"
                            placeholder="Search by customer, hotel or booking ID" aria-label="Search">
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 drops">

                        <!-- Status Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('status') ?? 'All Status' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All
                                        Status</a>
                                </li>
                                {{-- @foreach ($booking_status as $status)
                            <li>
                                <a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['status' => $status]) }}">
                                    {{ $status }}
                                </a>
                            </li>
                            @endforeach --}}
                            </ul>
                        </div>

                        <!-- Hotel Dropdown -->
                        {{-- <div class="dropdown">
                        <button class="btn btn-lg dropdown-toggle" type="button" id="hotelDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('hotel') ?? 'All Hotels' }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="hotelDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['hotel' => '']) }}">All
                                    Hotels</a></li>
                            @foreach ($hotels as $hotel)
                            <li>
                                <a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['hotel' => $hotel]) }}">
                                    {{ $hotel }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div> --}}

                        <!-- Date Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="dateDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('date') ?? 'All Dates' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dateDropdown">
                                @php
                                    $dates = ['Today', 'This Week', 'This Month'];
                                @endphp
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['date' => '']) }}">All
                                        Dates</a></li>
                                @foreach ($dates as $date)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['date' => $date]) }}">
                                            {{ $date }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>


                </div>
            </form>


            <div id="tableView" class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="myTable">
                    <thead style="background-color: #c1975a; color: #fff;">
                        <tr>
                            <th>Id</th>

                            <th>User Id</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Date</th>

                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = ($withdrawals->currentPage() - 1) * $withdrawals->perPage() + 1; @endphp
                        @forelse($withdrawals as $withdrawal)
                            <tr>
                                <td>{{ $counter++ }}</td>


                                <td>{{ $withdrawal->user_id }}</td>
                                <td>{{ $withdrawal->user->name ?? 'N/A' }}</td>


                                <td>₹ {{ number_format($withdrawal->amount, 2) }} </td>
                                <td>{{ $withdrawal->created_at ?? 'N/A' }}</td>


                                <td>
                                    @php
                                        $method = $withdrawal->payment->payment_method ?? null;
                                        $badgeClass = match ($withdrawal->status) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'rejected' => 'bg-danger',
                                        };
                                    @endphp

                                        <span class="badge {{ $badgeClass }}" style="cursor: pointer;"
                                            data-booking-id="{{ $withdrawal->id }}"
                                            data-current-status="{{ $withdrawal->status }}"
                                            onclick="showBookingSelect(this)">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                   
                                </td>






                                <td>
                                    @if ($withdrawal->status === 'pending')
                                        <form action="{{ route('withdrawal.approve', $withdrawal->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('withdrawal.reject', $withdrawal->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No withdrawals found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Links --}}
                <div class="mt-3">
                    {{ $withdrawals->links() }}
                </div>

            </div>




            {{-- {{ $details->links('pagination::bootstrap-5') }} --}}
            <!--end: Datatable-->




            <!-- Booking Status Change Modal -->
          




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
    </script>
   
        <script >
            function showBookingSelect(element) {
                let bookingId = element.dataset.bookingId;
                let currentStatus = element.dataset.currentStatus;

                // set booking id in hidden input
                document.getElementById("booking_id").value = bookingId;

                // set current status in dropdown
                document.getElementById("booking_status").value = currentStatus;

                // store reference of clicked badge
                window.clickedBadge = element;

                // show modal
                var myModal = new bootstrap.Modal(document.getElementById('statusModal'));
                myModal.show();
            }


        // On Click Update Button
        document.getElementById("updateStatusBtn").addEventListener("click", function() {
            let bookingId = document.getElementById("booking_id").value;
            let newStatus = document.getElementById("booking_status").value;

            // AJAX
            $.ajax({
                url: '/admin/bookings/status/' + bookingId,
                type: 'POST',
                data: {
                    status: newStatus
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

                success: function(data) {
                    if (data.success) {
                        let badge = window.clickedBadge;

                        // Update UI text
                        badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        badge.dataset.currentStatus = newStatus;

                        // Update badge colors
                        badge.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'bg-secondary');
                        badge.classList.add(
                            newStatus === 'confirmed' ? 'bg-success' :
                            newStatus === 'pending' ? 'bg-warning' :
                            newStatus === 'cancelled' ? 'bg-danger' : 'bg-secondary'
                        );

                        // Hide modal
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();

                        // success message
                        alert("Booking status updated successfully!");
                    } else {
                        alert("Failed to update booking status!");
                    }
                },
                error: function() {
                    alert("Something went wrong!");
                }
            });
        });
    </script>




    {{-- vendors --}}
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
                        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
