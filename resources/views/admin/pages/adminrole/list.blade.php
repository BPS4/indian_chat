@extends('admin.layout.default')

@section('role','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">User Role List
                <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
            </h3>
        </div>
        <div class="card-toolbar">
            {{-- @include('admin.layout.partials.filters.common-filter') --}}
            <!--begin::Button-->
            <a href="{{url('/admin/role/add')}}" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Add User Role</a>
            <!--end::Button-->
        </div>
        <!-- <div class="row col-lg-12 pl-0 pr-0">

            <div class="col-sm-5">
                <div class="dataTables_length">
                    <label cla>&#160; </label>
                    <button type="submit" class="btn btn-success" data-toggle="tooltip" title="Apply Filter" style="margin-top: 20px;">Filter</button>
                </div>
            </div>
        </div> -->
    </div>
    <div class="card-body">
        <!--begin: Datatable-->
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th class="custom_sno">SNo.</th>
                    <th>Role Name</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>


                @if(count($roles) > 0)
                @foreach($roles as $key => $value)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$value->role }}</td>
                    <!-- <td>
                        <a href="javascript:void(0)" data-url="{{url('admin/role/update-status/'.$value->id.'/'.$value->status)}}" onclick="changeStatus(this)">
                            <span class="label label-lg font-weight-bold label-light-{{($value->status == 1) ? 'success' : 'danger'}} label-inline">
                                {{($value->status == 1) ? 'Active' : 'InActive'}}
                            </span>
                        </a>
                    </td> -->
                    <td>
                        <a href="{{url('/admin/role/edit/'.$value->id)}}" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-edit"></i>
                        </a>
                        <a href="{{url('/admin/role/permissions/'.$value->id)}}" class="btn btn-sm btn-clean btn-icon" title="View Permissions" data-toggle="tooltip">
                            <i class="la la-user"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif

            </tbody>
        </table>
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
        $('#myTable').DataTable();
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    });
</script>
{{-- vendors --}}
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

{{-- page scripts --}}
<!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
