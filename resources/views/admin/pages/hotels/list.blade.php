@extends('admin.layout.default')

@section('hotels', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Message Management
                </h3>
                <p>Manage your messages</p>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                <a href="{{ url('/admin/message/add') }}" class="btn btn-primary font-weight-bolder">
                    + Add Message</a>
                <!-- <div>
                                                        <img src="{{ asset('media/icons/card-icon.png') }}" alt="">
                                                    </div> -->
                {{-- <div>
                    <img src="{{ asset('media/icons/card-icon.png') }}" alt="" id="toggleViewIcon"
                        style="cursor:pointer;">
                </div> --}}

            </div>


            <!-- <form action="" method="get" class="w-100">
                                                    <div class="row col-lg-12 pl-0 pr-0">
                                                        <div class="col-sm-3">
                                                            <div class="dataTables_length">
                                                                <label>Status</label>
                                                                <select name="status" value="" class="form-control">
                                                                    <option value="">All Status</option>
                                                                    <option value="0" @if (request('status') == '0') {{ runTimeSelection(0, request('status')) }} @endif>InActive</option>
                                                                    <option value="1" @if (request('status') == '1') {{ runTimeSelection(1, request('status')) }} @endif>Active</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-5">
                                                            <div class="dataTables_length">
                                                                <label cla>&#160; </label>
                                                                <button type="submit" class="btn btn-success mt-7" data-toggle="tooltip" title="Apply Filter">Filter</button>
                                                                <a href="{{ url('/admin/inventory/list') }}" class="btn btn-default mt-7" data-toggle="tooltip" title="Reset Filter">Reset</a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form> -->



        </div>


        <div class="card-body">
            <!--begin: Datatable-->

            <form method="GET" action="{{ url('/admin/hotels/list') }}">

                <div class="card-search mb-5">
                    <!-- Search input -->
                    <div class="w-75 position-relative">
                        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                        <input class="form-control ps-25" type="search" placeholder="Search by Hotel name" name="search"
                            value="{{ request('search') }}" aria-label="Search">
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 drops">

                        <!-- Status Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('status') ?? 'All Status' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ url('/admin/hotels/list') . '?search=' . request('search') . '&status=' }}">
                                        All Status
                                    </a></li>
                                <li><a class="dropdown-item"
                                        href="{{ url('/admin/hotels/list') . '?search=' . request('search') . '&status=Active' }}">
                                        Active
                                    </a></li>
                               
                            </ul>
                        </div>



                        <!-- Location Dropdown -->
                       


                    </form>


                </div>
            </div>

            <div id="tableView">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th class="custom_sno"> Id</th>
                         
                            <th>Calling no</th>
                            <th>website link</th>
                            <th>State</th>
                            <th>city</th>
                            <th>Total Users</th>
                          
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($hotels as $hotel)
                            <tr>

                                {{-- <td class="custom_sno">{{ $hotel->id }} </td> --}}
                                          <td>{{ $loop->iteration }}</td>
                                <td>{{ $hotel->calling_number }} </td>
                                <td>{{ $hotel->website_link }}</td>
                                <td>{{ $hotel->state }} </td>
                                <td>{{ $hotel->city }} </td>   
                                <td>{{ $hotel->total_users }} </td>     

                               
                        @endforeach



                    </tbody>
                </table>
                {{ $hotels->links('pagination::bootstrap-5') }}

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
                icon.attr('src', '{{ asset('media/icons/hotel-card-icon.png') }}');
            } else {
                // Switch to table view
                cardView.hide();
                tableView.show();
                icon.attr('src', '{{ asset('media/icons/card-icon.png') }}');
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleHotelStatus(element) {
            // Ask for confirmation
            var confirmChange = confirm("Are you sure you want to change the hotel status?");
            if (!confirmChange) {
                return; // Stop the script if user clicks "Cancel"
            }

            var hotelId = $(element).data('hotel-id');

            $.ajax({
                url: '/admin/hotels/status/' + hotelId + '/toggle-status',
                type: 'POST',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success) {
                        // Update the badge text
                        if (data.new_status === 'active') {
                            $(element).text('Active')
                                .removeClass('badge-inactive')
                                .addClass('badge-active')
                                .css('background-color', '#20c997');
                        } else {
                            $(element).text('Maintenance')
                                .removeClass('badge-active')
                                .addClass('badge-inactive')
                                .css('background-color', 'red');
                        }
                    } else {
                        alert('Failed to update status!');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Something went wrong!');
                }
            });
        }
    </script>




    {{-- vendors --}}
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
                                        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
