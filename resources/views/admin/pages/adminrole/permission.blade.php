@extends('admin.layout.default')

@section('role', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <!-- <div class="card-header flex-wrap border-0 pt-6 pb-0">
            <div class="card-title">
                <h3 class="card-label">Edit Category
                </h3>
            </div>
        </div> -->
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    @php
                        $modules = modulesList();
                    @endphp
                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}

                        <div class="w-100 col-lg-9 col-xl-12">
                            <h2> ROLE : {{ $details->role }} </h2>
                            <hr>
                        </div>
                        <div class="col-lg-9 col-xl-12">

                            <div class="row align-items-center col-lg-12 pr-0 mr-0">
                                <table class="table table-bordered" style="    width: 50%;">
                                    <thead>

                                        <th>Approval Module</th>
                                        <th>Approval Access</th>
                                    </thead>
                                    @php
                                        if ($details->permission) {
                                            $existingPermissions = json_decode($details->permission, true);
                                            // dd($existingPermissions);
                                        } else {
                                            $existingPermissions = [];
                                        }
                                    @endphp

                                    <body>
                                        <tr>
                                            <td>Credit Hold Status</td>
                                            <td> <label
                                                    class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" value="1" name="permission_hold_status"
                                                        @if (isset($existingPermissions['permission_hold_status']) && $existingPermissions['permission_hold_status'] == 1) checked @endif
                                                        class="checkBoxView individual_checkbox">
                                                    <span></span>
                                                </label></td>
                                        </tr>
                                        <tr>
                                            <td>Order Quantity</td>
                                            <td> <label
                                                    class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" value="1" name="permission_order_quantity"
                                                        class="checkBoxView individual_checkbox"
                                                        @if (isset($existingPermissions['permission_order_quantity']) && $existingPermissions['permission_order_quantity'] == 1) checked @endif>
                                                    <span></span>
                                                </label></td>
                                        </tr>
                                        <tr>
                                            <td>Order Cancel</td>
                                            <td> <label
                                                    class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" value="1" name="permission_order_cancel"
                                                        class="checkBoxView individual_checkbox"
                                                        @if (isset($existingPermissions['permission_order_cancel']) && $existingPermissions['permission_order_cancel'] == 1) checked @endif>
                                                    <span></span>
                                                </label></td>
                                        </tr>

                                        <tr>
                                            <td>Download Pdf</td>
                                            <td> <label
                                                    class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" value="1" name="permission_download_pdf"
                                                        class="checkBoxView individual_checkbox"
                                                        @if (isset($existingPermissions['permission_download_pdf']) && $existingPermissions['permission_download_pdf'] == 1) checked @endif>
                                                    <span></span>
                                                </label></td>
                                        </tr>

                                    </body>
                                </table>


                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Module Name</th>
                                            <th>
                                                <label
                                                    class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" name="select" value="1" id="ckbCheckView"
                                                        className="Checkbox1" class="Checkbox1">
                                                    <span></span>
                                                    &nbsp; View
                                                </label>

                                            </th>
                                            <th>
                                                <label
                                                    class="checkbox checkbox-lg checkbox-light-primary checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" name="select" value="1"
                                                        id="ckbCheckAddEdit" className="Checkbox1" class="Checkbox1">
                                                    <span></span>
                                                    &nbsp; View | Edit
                                                </label>
                                            </th>
                                            <th>
                                                <label
                                                    class="checkbox checkbox-lg checkbox-light-warning checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" name="select" value="1" id="ckbCheckAdd"
                                                        className="Checkbox1" class="Checkbox1">
                                                    <span></span>
                                                    &nbsp; View | Add
                                                </label>
                                            </th>
                                            <th>
                                                <label
                                                    class="checkbox checkbox-lg checkbox-light-info checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" name="select" value="1" id="ckbCheckEdit"
                                                        className="Checkbox1" class="Checkbox1">
                                                    <span></span>
                                                    &nbsp; View | Add | Edit
                                                </label>
                                            </th>
                                            <th>
                                                <label
                                                    class="checkbox checkbox-lg checkbox-light-danger checkbox-inline flex-shrink-0 m-0 mx-4">
                                                    <input type="checkbox" name="select" value="1" id="ckbCheckDelete"
                                                        className="Checkbox1" class="Checkbox1">
                                                    <span></span>
                                                    &nbsp; View | Add | Edit | Delete
                                                </label>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            if ($details->permission) {
                                                $existingPermissions = json_decode($details->permission, true);
                                            } else {
                                                $existingPermissions = [];
                                            }
                                        @endphp
                                        @if ($modules)
                                            @foreach ($modules as $key => $module)
                                                @php
                                                    $permission = 0;
                                                    if (isset($existingPermissions[$module['slug']])) {
                                                        $permission = $existingPermissions[$module['slug']];
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $module['module_name'] }}
                                                        <input type="hidden" value="{{ $module['slug'] }}"
                                                            name="module_slug[{{ $module['id'] }}]">
                                                    </td>
                                                    <td>
                                                        <label
                                                            class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
                                                            <input type="checkbox" value="1"
                                                                {{ runTimeChecked(1, $permission) }}
                                                                id="Checkbox{{ $module['id'] }}"
                                                                name="permissions[{{ $module['id'] }}]"
                                                                class="checkBoxView individual_checkbox">
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label
                                                            class="checkbox checkbox-lg checkbox-light-primary checkbox-inline flex-shrink-0 m-0 mx-4">
                                                            <input type="checkbox" value="2"
                                                                {{ runTimeChecked(2, $permission) }}
                                                                id="Checkbox{{ $module['id'] }}"
                                                                name="permissions[{{ $module['id'] }}]"
                                                                class="checkBoxViewEdit individual_checkbox" />
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label
                                                            class="checkbox checkbox-lg checkbox-light-warning checkbox-inline flex-shrink-0 m-0 mx-4">
                                                            <input type="checkbox" value="3"
                                                                {{ runTimeChecked(3, $permission) }}
                                                                id="Checkbox{{ $module['id'] }}"
                                                                name="permissions[{{ $module['id'] }}]"
                                                                class="checkBoxViewAdd individual_checkbox" />
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label
                                                            class="checkbox checkbox-lg checkbox-light-info checkbox-inline flex-shrink-0 m-0 mx-4">
                                                            <input type="checkbox" value="4"
                                                                {{ runTimeChecked(4, $permission) }}
                                                                id="Checkbox{{ $module['id'] }}"
                                                                name="permissions[{{ $module['id'] }}]"
                                                                class="checkBoxViewAddEdit individual_checkbox" />
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label
                                                            class="checkbox checkbox-lg checkbox-light-danger checkbox-inline flex-shrink-0 m-0 mx-4">
                                                            <input type="checkbox" value="5"
                                                                {{ runTimeChecked(5, $permission) }}
                                                                id="Checkbox{{ $module['id'] }}"
                                                                name="permissions[{{ $module['id'] }}]"
                                                                class="checkBoxViewAddEditDelete individual_checkbox" />
                                                            <span></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>

                                <div class="form-group col-md-12">
                                    <center><button class="btn btn-success">Update</button></center>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')
    <script>
        $(document).ready(function() {
            $("#ckbCheckView").click(function() {
                $(".Checkbox1").not(this).prop("checked", false);

                $(".checkBoxViewEdit").removeAttr("checked");
                $(".checkBoxViewAdd").removeAttr("checked");
                $(".checkBoxViewAddEdit").removeAttr("checked");
                $(".checkBoxViewAddEditDelete").removeAttr("checked");

                $(".checkBoxView").attr("checked", this.checked);
            });
            $("#ckbCheckAddEdit").click(function() {
                $(".Checkbox1").not(this).prop("checked", false);

                $(".checkBoxView").removeAttr("checked");
                $(".checkBoxViewAdd").removeAttr("checked");
                $(".checkBoxViewAddEdit").removeAttr("checked");
                $(".checkBoxViewAddEditDelete").removeAttr("checked");

                $(".checkBoxViewEdit").attr("checked", this.checked);
            });
            $("#ckbCheckAdd").click(function() {
                $(".Checkbox1").not(this).prop("checked", false);

                $(".checkBoxView").removeAttr("checked");
                $(".checkBoxViewEdit").removeAttr("checked");
                $(".checkBoxViewAddEdit").removeAttr("checked");
                $(".checkBoxViewAddEditDelete").removeAttr("checked");

                $(".checkBoxViewAdd").attr("checked", this.checked);
            });
            $("#ckbCheckEdit").click(function() {
                $(".Checkbox1").not(this).prop("checked", false);

                $(".checkBoxView").removeAttr("checked");
                $(".checkBoxViewEdit").removeAttr("checked");
                $(".checkBoxViewAdd").removeAttr("checked");
                $(".checkBoxViewAddEditDelete").removeAttr("checked");

                $(".checkBoxViewAddEdit").attr("checked", this.checked);
            });
            $("#ckbCheckDelete").click(function() {

                $(".Checkbox1").not(this).prop("checked", false);

                $(".checkBoxView").removeAttr("checked");
                $(".checkBoxViewEdit").removeAttr("checked");
                $(".checkBoxViewAdd").removeAttr("checked");
                $(".checkBoxViewAddEdit").removeAttr("checked");

                $(".checkBoxViewAddEditDelete").attr("checked", this.checked);
            });
            $(".individual_checkbox").click(function() {
                $(this).parents('tr').find("input[type=checkbox]").not(this).removeAttr("checked").prop(
                    "checked", false);
            });
        });
    </script>
@endsection
