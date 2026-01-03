@extends('admin.layout.default')

@section('bookings', 'active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Booking Management
            </h3>
            <p>Manage reservations, check-ins, and cancellations</p>
        </div>
        <div class="card-toolbar">
            <!--begin::Button-->
            <a href="{{ url('/admin/bookings/add') }}" class="btn btn-primary font-weight-bolder">
                + New Booking</a>
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
                            @foreach ($booking_status as $status)
                            <li>
                                <a class="dropdown-item"
                                    href="{{ request()->fullUrlWithQuery(['status' => $status]) }}">
                                    {{ $status }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Hotel Dropdown -->
                    <div class="dropdown">
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
                    </div>

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

                        <th>Booking Id</th>
                        <th>Customer</th>
                        <th>Hotel & Room</th>
                        <th>Dates</th>
                        <th>Guest</th>
                        <th>Amount</th>
                        <th>Created By</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = ($bookings->currentPage() - 1) * $bookings->perPage() + 1; @endphp
                    @forelse($bookings as $booking)
                    <tr>
                        <td>{{ $counter++ }}</td>


                        <td>{{ $booking->booking_id }}</td>
                        <td>
                            <div>{{ $booking->guests[0]->guest_name ?? 'N/A' }}</div>
                            <div class="text-brown small">{{ $booking->guests[0]->email ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $booking->hotel->name ?? '-' }}</div>
                            <div class="text-brown small">
                                {{ $booking->rooms->first()->roomType->room_name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="small">Check-in:
                                {{ \Carbon\Carbon::parse($booking->checkin_date)->format('d/m/Y') }}
                            </div>
                            <div class="small">Check-out:
                                {{ \Carbon\Carbon::parse($booking->checkout_date)->format('d/m/Y') }}
                            </div>

                        </td>
                        <td>
                            <i class="la la-user"></i> {{ $booking->total_guests }}
                        </td>
                        <td>₹ {{ number_format($booking->total_payable, 2) }} </td>
                        <td>{{ $booking->created_by ?? 'N/A' }}</td>

                        <td>{{ $booking->payment->payment_method ?? 'N/A' }}</td>

                        <td>
                            @php
                            $method = $booking->payment->payment_method ?? null;
                            $badgeClass = match ($booking->status) {
                            'confirmed' => 'bg-success',
                            'pending' => 'bg-warning',
                            'cancelled' => 'bg-danger',
                            default => 'bg-secondary',
                            };
                            @endphp

                            @if ($method === 'Cash')
                            {{-- Clickable badge (user can change status) --}}
                            <span class="badge {{ $badgeClass }}" style="cursor: pointer;"
                                data-booking-id="{{ $booking->id }}"
                                data-current-status="{{ $booking->status }}" onclick="showBookingSelect(this)">
                                {{ ucfirst($booking->status) }}
                            </span>
                            @else
                            {{-- Non-clickable badge --}}
                            <span class="badge {{ $badgeClass }}" style="cursor: not-allowed; opacity: 0.6;">
                                {{ ucfirst($booking->status) }}
                            </span>
                            @endif
                        </td>






                        <td>
                            <a href="{{ url('/admin/bookings/booking_details', $booking->id) }}"
                                class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="Edit">
                                {{-- <i class="la la-edit"></i> --}}
                                <i class="la la-eye"></i>
                            </a>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No bookings found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination Links --}}
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>

        </div>




        {{-- {{ $details->links('pagination::bootstrap-5') }} --}}
        <!--end: Datatable-->




                            <!-- Booking Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0" style="border-radius: 15px;">

      <div class="modal-header status-modal-header text-white" style="border-top-left-radius: 15px;border-top-right-radius: 15px;">
        <h5 class="modal-title status-modal-title" id="statusModalLabel">Update Booking Status</h5>
        <button type="button" class="btn-close btn-close-white status-modal-title" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <input type="hidden" id="booking_id">

          <label class="fw-bold mb-2">Select New Status</label>
          <select class="form-select" id="booking_status">
              <option value="confirmed">Confirmed</option>
              <option value="pending">Pending</option>
              <option value="cancelled">Cancelled</option>
          </select>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="updateStatusBtn" class="btn btn-primary">Update Status</button>
      </div>

    </div>
  </div>
</div>





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
<!-- <script> -->
    <!-- // function showBookingSelect(element) {
    //     var bookingId = element.dataset.bookingId;
    //     var currentStatus = element.dataset.currentStatus;

    //     // Create a container div for the select box
    //     var popup = document.createElement('div');
    //     popup.style.position = 'fixed';
    //     popup.style.top = '10%';
    //     popup.style.left = '50%';
    //     popup.style.transform = 'translate(-50%, -50%)';
    //     popup.style.background = '#fff';
    //     popup.style.padding = '20px';
    //     popup.style.border = '1px solid #ccc';
    //     popup.style.zIndex = 1000;
    //     popup.style.boxShadow = '0px 0px 10px rgba(0,0,0,0.3)';

    //     // Create select element
    //     var select = document.createElement('select');
    //     var statuses = ['confirmed', 'pending', 'cancelled'];
    //     statuses.forEach(status => {
    //         var option = document.createElement('option');
    //         option.value = status;
    //         option.text = status.charAt(0).toUpperCase() + status.slice(1);
    //         if (status === currentStatus) option.selected = true;
    //         select.appendChild(option);
    //     });

    //     // Create buttons
    //     var okBtn = document.createElement('button');
    //     okBtn.textContent = 'Update';
    //     okBtn.style.margin = '10px';

    //     var cancelBtn = document.createElement('button');
    //     cancelBtn.textContent = 'Cancel';
    //     cancelBtn.style.margin = '10px';

    //     popup.appendChild(select);
    //     popup.appendChild(okBtn);
    //     popup.appendChild(cancelBtn);
    //     document.body.appendChild(popup);

    //     // Cancel button
    //     cancelBtn.onclick = function() {
    //         document.body.removeChild(popup);
    //     }

    //     // OK button
    //     okBtn.onclick = function() {
    //         var newStatus = select.value;
    //         if (newStatus === currentStatus) {
    //             alert('Status is unchanged!');
    //             return;
    //         }

    //         if (!confirm(`Are you sure you want to change status to "${newStatus}"?`)) return;

    //         // alert(newStatus);

    //         // AJAX request
    //         $.ajax({
    //             url: '/admin/bookings/status/' + bookingId,
    //             type: 'POST',
    //             data: {
    //                 status: newStatus
    //             },
    //             headers: {
    //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //             },
    //             success: function(data) {
    //                 if (data.success) {
    //                     // Update badge
    //                     element.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    //                     element.dataset.currentStatus = newStatus;
    //                     element.classList.remove('bg-success', 'bg-warning', 'bg-danger',
    //                         'bg-secondary');
    //                     switch (newStatus) {
    //                         case 'confirmed':
    //                             element.classList.add('bg-success');
    //                             break;
    //                         case 'pending':
    //                             element.classList.add('bg-warning');
    //                             break;
    //                         case 'cancelled':
    //                             element.classList.add('bg-danger');
    //                             break;
    //                         default:
    //                             element.classList.add('bg-secondary');
    //                     }
    //                     alert('Booking status updated successfully!');
    //                 } else {
    //                     alert('Failed to update booking status!');
    //                 }
    //             },
    //             error: function() {
    //                 alert('Something went wrong!');
    //             }
    //         });

    //         document.body.removeChild(popup);
    //     }
    // } -->
    <script>
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
    document.getElementById("updateStatusBtn").addEventListener("click", function () {
        let bookingId = document.getElementById("booking_id").value;
        let newStatus = document.getElementById("booking_status").value;

        // AJAX
        $.ajax({
            url: '/admin/bookings/status/' + bookingId,
            type: 'POST',
            data: { status: newStatus },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },

            success: function (data) {
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
            error: function () {
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
