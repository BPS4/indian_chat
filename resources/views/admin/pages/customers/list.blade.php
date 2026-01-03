@extends('admin.layout.default')

@section('customers', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Customers
                </h3>
                <p>View and manage customer profiles and booking history</p>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                {{-- <a href="{{ url('/admin/hotels/add') }}" class="btn btn-primary font-weight-bolder">
                    + Add Customer</a> --}}
                <!-- <div>
                            <img src="{{ asset('media/icons/card-icon.png') }}" alt="">
                        </div> -->
                {{-- <div>
                    <img src="{{ asset('media/icons/card-icon.png') }}" alt="" id="toggleViewIcon"
                        style="cursor:pointer;">
                </div> --}}
                <!--end::Button-->
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
            <form method="GET" action="{{ url('/admin/customers/list') }}">
                <div class="card-search mb-5">
                    <!-- Search input -->
                    <div class="w-75 position-relative">
                        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                        <input class="form-control ps-25" type="search" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or email" aria-label="Search">
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 drops">

                        <!-- Status Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('status') ?? 'All Status' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All
                                        Status</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['status' => 'Active']) }}">Active</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['status' => 'Inactive']) }}">Inactive</a></li>

                            </ul>
                        </div>

                        <!-- Location Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="locationDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('location') ?? 'All Location' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="locationDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['location' => '']) }}">All Location</a></li>

                                @foreach ($locations as $city)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['location' => $city]) }}">
                                            {{ $city }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>


                </div>
            </form>


            <div id="tableView">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th> </th>
                            <th class="custom_sno">Customer</th>
                            <th>Contact</th>
                            <th>Location </th>
                            <th>Join Date </th>
                            <th>Booking </th>
                            <th>Total Spend </th>
                            <td>Status</td>
                            <!-- <th>On Hold </th> -->
                            <th class="custom_action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <img src="{{ asset('media/users/customer-ico.png') }}" alt="image" />
                                </td>
                                <td>
                                    {{ $user->name }}
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">{{ $user->email }}</div>
                                        <div class="text-muted">{{ $user->mobile }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->city ?? 'N/A' }}</td>
                                <td>{{ $user->created_at->format('d-m-Y') }}</td>
                                <td>{{ $user->bookings->count() }}</td>
                                <td>₹ {{ $user->booking_payments->sum('amount') }}</td>
                                <td>
                                    @if ($user->status == '1')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('/admin/customers/view/' . $user->id) }}"
                                        class="border border-0 bg-transparent" data-bs-toggle="tooltip"
                                        title="View Details">
                                        <img src="{{ asset('media/icons/eye.png') }}" class="w-20" alt="">
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                {{ $users->links('pagination::bootstrap-5') }}
            </div>

            <div id="cardView" class="row" style="display:none;">

                <div class="row g-4">

                    <!-- Hotel Card -->
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>

                    <!-- Duplicate for demo -->
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="hotel-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="hotel-name">Grand Plaza Hotel</div>
                                <span class="status-badge">Active</span>
                            </div>
                            <div class="hotel-location mb-2"><span><img src="{{ asset('media/icons/location1.png') }}"
                                        alt=""> </span> Noida, IND</div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img
                                            src="{{ asset('media/icons/emoji_star.png') }}" alt=""></span> 4.5
                                </div>
                                <div class=" text-gray">250 Rooms</div>
                            </div>

                            <div class="d-flex flex-wrap mb-4 gap-2">
                                <span class="feature-btn">Wifi</span>
                                <span class="feature-btn">Pool</span>
                                <span class="feature-btn">Arcade</span>
                                <span class="feature-btn">Restaurant</span>
                                <span class="feature-btn">Parking</span>
                                <span class="feature-btn">Breakfast</span>
                            </div>

                            <div class="pt-2 mb-1 text-muted monthly-revenue">Monthly Revenue</div>
                            <div class="revenue mb-5">Rs 100,000</div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-delete "><i class="la la-trash me-1"></i> Delete</button>
                                <button class="btn btn-edit ">Edit</button>
                            </div>
                        </div>
                    </div>

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


        // $(document).ready(function() {
        //     $('#myTable').DataTable({
        //         aLengthMenu: [
        //             [25, 50, 100],
        //             [25, 50, 100]
        //         ],
        //         pageLength: 25,
        //         language: {
        //             lengthMenu: 'Show _MENU_ entries'
        //         }
        //     });

        //     // Activate Bootstrap tooltips
        //     $('[data-bs-toggle="tooltip"]').tooltip();
        // });
    </script>
    {{-- vendors --}}
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
            <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
