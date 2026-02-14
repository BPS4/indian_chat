@extends('admin.layout.default')

@section('investment', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Investments
                </h3>
                <p>View and manage investment Of all Customers</p>
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



                    </div>


                </div>
            </form>


            <div id="tableView">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th class="custom_sno">Name</th>
                            <th class="custom_sno">Details</th>

                            {{-- <th class="custom_sno">Profile Pic</th> --}}
                            <th>Country</th>
                            <th>State </th>
                            <th>City </th>
                            <th>Joning Date </th>
                            <th>Investment Amount </th>
                            <th>Receipt</th>
                            <td>Status</td>
                            <td>Action</td>
                            <!-- <th>On Hold </th> -->
                            {{-- <th class="custom_action">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>



                        @foreach ($investments as $investment)
                            <tr>

                                <td>
                                    {{ $investment->user_id }}
                                </td>

                                <td>
                                    {{ $investment->name ?? 'N/A'}}
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">{{ $investment->email ?? 'N/A' }}</div>
                                        <div class="font-weight-bold">{{ $investment->mobile ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>{{ $investment->country ?? 'N/A' }}</td>
                                <td>{{ $investment->state ?? 'N/A' }}</td>
                                <td>{{ $investment->city ?? 'N/A' }}</td>

                                <td>{{ $investment->created_at->format('d-m-Y') }}</td>
                                <td>{{ $investment->amount ?? 'N/A' }}</td>
                                <td>
                                    @if ($investment->payment_receipt)
                                        <a href="{{ asset($investment->payment_receipt) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            View Receipt
                                        </a>
                                    @else
                                        <span class="text-muted">No Receipt</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($investment->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif ($investment->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif ($investment->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="text-muted
                                        @endif
                                </td>
                                <td>
                                    @if ($investment->status === 'pending')
                                        <form action="{{ route('investment.approve', $investment->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('investment.reject', $investment->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">No Action</span>
                                    @endif
                                </td>



                            </tr>
                        @endforeach
                    </tbody>

                </table>

                {{ $investments->links('pagination::bootstrap-5') }}
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
