@extends('admin.layout.default')

@section('Offers','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Offers
            </h3>
            <p>Manage Offer and Discounts</p>
        </div>
        <div class="card-toolbar">

            <a href="{{url('/admin/Offers/add')}}" class="btn btn-primary font-weight-bolder">
                + Add Offer</a>



        </div>
    </div>


    <div class="card-body">

        <div class="card-search mb-5">
            <div class="w-75 position-relative">
                <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                <input
                    class="form-control ps-25"
                    type="search"
                    placeholder="Search by title or ID"
                    aria-label="Search">
            </div>

        </div>

        <div id="tableView" class="offer-table-container p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 offer-table" id="myTable">
                    <thead class="offer-table-header">
                        <tr>
                            <th>Offer ID</th>
                            <th>Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Discount Value</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#MH90877</td>
                            <td>Diwali Offer</td>
                            <td>25 Nov 2025</td>
                            <td>25 Feb 2026</td>
                            <td><span class="text-success fw-semibold">Active</span></td>
                            <td>70%</td>
                                  <td>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="view">
                                    <i class="la la-eye"></i>
                                </button>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="Edit">
                                    <i class="la la-edit"></i>
                                </button>
                                <button class="btn btn-sm text-danger border-0" data-bs-toggle="tooltip" title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#MH90877</td>
                            <td>Holi Offer</td>
                            <td>25 Nov 2025</td>
                            <td>25 Dec 2025</td>
                            <td><span class="text-warning fw-semibold">Inactive</span></td>
                            <td>500</td>
                                  <td>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="view">
                                    <i class="la la-eye"></i>
                                </button>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="Edit">
                                    <i class="la la-edit"></i>
                                </button>
                                <button class="btn btn-sm text-danger border-0" data-bs-toggle="tooltip" title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#MH90877</td>
                            <td>Birthday Offer</td>
                            <td>25 Nov 2025</td>
                            <td>25 Nov 2028</td>
                            <td><span class="text-success fw-semibold">Active</span></td>
                            <td>33%</td>
                            <!-- <td class="text-center">
            <i class="bi bi-eye me-2"></i>
            <i class="bi bi-pencil-square me-2"></i>
            <i class="bi bi-trash"></i>
          </td> -->
                            <td>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="view">
                                    <i class="la la-eye"></i>
                                </button>
                                <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip" title="Edit">
                                    <i class="la la-edit"></i>
                                </button>
                                <button class="btn btn-sm text-danger border-0" data-bs-toggle="tooltip" title="Delete">
                                    <i class="la la-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/dashboard-ico.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/location1.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/location1.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/location1.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/location1.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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
                        <div class="hotel-location mb-2"><span><img src="{{asset('media/icons/location1.png')}}" alt=""> </span> Noida, IND</div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2 star"><span><img src="{{asset('media/icons/emoji_star.png')}}" alt=""></span> 4.5</div>
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

    $('#toggleViewIcon').on('click', function() {
        const tableView = $('#tableView');
        const cardView = $('#cardView');
        const icon = $(this);

        if (tableView.is(':visible')) {
            // Switch to card view
            tableView.hide();
            cardView.show();
            icon.attr('src', '{{ asset("media/icons/table-icon.png") }}');
        } else {
            // Switch to table view
            cardView.hide();
            tableView.show();
            icon.attr('src', '{{ asset("media/icons/card-icon.png") }}');
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