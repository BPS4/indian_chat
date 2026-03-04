@extends('admin.layout.default')

@section('customers', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Customers
                </h3>
                <p>View and manage customer profiles and Total Earning</p>
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
                            <th>Id</th>
                            <th class="custom_sno">Name</th>
                            <th class="custom_sno">Details</th>

                            {{-- <th class="custom_sno">Profile Pic</th> --}}
                            <th>Country</th>
                            <th>State </th>
                            <th>City </th>
                            <th>Joning Date </th>
                            {{-- <th>Total Earning </th> --}}
                            <th>Pan Card</th>
                            <th>Aadhaar Front</th>
                            <th>Aadhaar Back</th>
                            <th>Kyc Status</th>
                            <th>Bank</th>
                            <td>Status</td>
                            <!-- <th>On Hold </th> -->
                            {{-- <th class="custom_action">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <img src="{{ asset('media/users/customer-ico.png') }}" alt="image" />
                                </td>
                                <td>
                                    {{ $user->user_id ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $user->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">{{ $user->email }}</div>
                                        <div class="font-weight-bold">{{ $user->mobile }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->country ?? 'N/A' }}</td>
                                <td>{{ $user->state ?? 'N/A' }}</td>
                                <td>{{ $user->city ?? 'N/A' }}</td>
                                {{-- <td>
                                </td> --}}
                                <td>{{ $user->created_at->format('d-m-Y') }}</td>
                                {{-- <td>{{ $user->wallet_amount ?? 'N/A' }}</td> --}}
                                <td>
                                    @if ($user->pan_card)
                                        <a href="javascript:void(0)" onclick="showImage('{{ asset($user->pan_card) }}')">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>
                                    @if ($user->aadhaar_front)
                                        <a href="javascript:void(0)"
                                            onclick="showImage('{{ asset($user->aadhaar_front) }}')">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>
                                    @if ($user->aadhaar_back)
                                        <a href="javascript:void(0)"
                                            onclick="showImage('{{ asset($user->aadhaar_back) }}')">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>

<td>
    <a href="javascript:void(0)"
       onclick="openKycModal({{ $user->id }}, '{{ $user->kyc_status }}')">

        @if ($user->kyc_status == 'pending')
            <span class="badge bg-warning text-dark">Pending</span>
        @elseif ($user->kyc_status == 'approved')
            <span class="badge bg-success">Approved</span>
        @elseif ($user->kyc_status == 'rejected')
            <span class="badge bg-danger">Rejected</span>
        @else
            <span class="badge bg-warning ">Not Submitted</span>
        @endif

    </a>
</td>

 <td>
                                    <button
                                        class="btn btn-sm btn-success"
                                        title="view"
                                        onclick="openBankModal({{ $user->id }})">
                                        <i class="fa fa-eye"></i>
                                    </button>


                                </td>

                                

                                <td>
                                    @if ($user->status == '1')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                {{-- <td class="text-center">
                                    <a href="{{ url('/admin/customers/view/' . $user->id) }}"
                                        class="border border-0 bg-transparent" data-bs-toggle="tooltip"
                                        title="View Details">
                                        <img src="{{ asset('media/icons/eye.png') }}" class="w-20" alt="">
                                    </a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                {{ $users->links('pagination::bootstrap-5') }}
            </div>

            <!-- Image View Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Document Preview</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="previewImage" src="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>


            <script>
                function showImage(imageUrl) {
                    document.getElementById('previewImage').src = imageUrl;
                    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    myModal.show();
                }
            </script>


     <!--  kyc status change modal -->

<div class="modal fade" id="kycModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update KYC Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="kyc_user_id">

                <div class="mb-3">
                    <label>Status</label>
                    <select id="kyc_status" class="form-control">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Reject Reason (if rejected)</label>
                    <textarea id="kyc_reason" class="form-control"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="updateKycStatus()">Submit</button>
            </div>

        </div>
    </div>
</div>


<script>
function openKycModal(userId, currentStatus) {

    document.getElementById('kyc_user_id').value = userId;
    document.getElementById('kyc_status').value = currentStatus;
    document.getElementById('kyc_reason').value = '';

    var myModal = new bootstrap.Modal(document.getElementById('kycModal'));
    myModal.show();
}


function updateKycStatus() {

    let userId = document.getElementById('kyc_user_id').value;
    let status = document.getElementById('kyc_status').value;
    let reason = document.getElementById('kyc_reason').value;

    fetch("{{ route('customers.kyc.update') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            user_id: userId,
            status: status,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}
</script>


<!-- Bank Details Modal -->
<div class="modal fade" id="bankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Bank Name:</strong> <span id="bank_name">N/A</span></div>
                <div class="mb-2"><strong>Account Number:</strong> <span id="account_number">N/A</span></div>
                <div class="mb-2"><strong>IFSC Code:</strong> <span id="ifsc_code">N/A</span></div>
                <div class="mb-2"><strong>Account Holder:</strong> <span id="account_holder_name">N/A</span></div>

                <div id="bank_document_wrap" class="mt-3" style="display:none;">
                    <label>Document</label>
                    <div class="text-center">
                        <img id="bank_document" src="" class="img-fluid" alt="bank document" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openBankModal(userId) {
        fetch('/admin/customers/bank/' + userId)
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data || !data.status) {
                    alert(data.message || 'No bank details found');
                    return;
                }

                var bank = data.bank;
                document.getElementById('bank_name').innerText = bank.bank_name || 'N/A';
                document.getElementById('account_number').innerText = bank.account_number || 'N/A';
                document.getElementById('ifsc_code').innerText = bank.ifsc_code || 'N/A';
                document.getElementById('account_holder_name').innerText = bank.account_holder_name || 'N/A';

                if (bank.document) {
                    // Ensure correct asset path
                    var docUrl = bank.document.startsWith('http') ? bank.document : ('/' + bank.document).replace(/\/\\+/g, '/');
                    document.getElementById('bank_document').src = docUrl;
                    document.getElementById('bank_document_wrap').style.display = 'block';
                } else {
                    document.getElementById('bank_document_wrap').style.display = 'none';
                }

                var myModal = new bootstrap.Modal(document.getElementById('bankModal'));
                myModal.show();
            })
            .catch(function (err) {
                console.error(err);
                alert('Failed to load bank details');
            });
    }
</script>



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
