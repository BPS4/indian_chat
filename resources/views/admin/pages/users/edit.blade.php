@extends('admin.layout.default')

@section('user', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12">
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <label>Name</label>
                                    <div><input type="text" name="name" value="{{ $details->name }}"   title="Only letters and spaces are allowed"
                                            isrequired="required" class="form-control" placeholder="Enter User Name"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email/User Name </label>
                                    <div><input type="email" name="email" value="{{ $details->email }}"
                                            isrequired="required" class="form-control" placeholder="Enter Email"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Mobile No.</label>
                                    <div><input type="text" name="mobile" value="{{ $details->mobile }}"
                                            isrequired="required" class="form-control number" placeholder="Enter Mobile Number">
                                    </div>
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <label>Password </label>
                                    <div><input type="password" name="password" value="{{ $details->password }}" isrequired="required" class="form-control" placeholder="Enter Password"></div>
                                </div> --}}
                                <div class="form-group col-md-6">
                                    <label>User Role</label>

                                    <select class="form-control" name="role_id" id="role_id" isrequired="required">
                                        <option value="">Select Role</option>
                                        @if ($roles = getSystemRoles())
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ runTimeSelection($details->role_id, $role->id) }}>{{ $role->role }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                               

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
        $('#post').change(function() {
            var role_id = $(this).val();
            if (role_id == 2) {
                $('#general_manager').removeClass('d-none');
                $('#regional_manager').addClass('d-none');
            } else if (role_id == 3) {
                $('#regional_manager').removeClass('d-none');
                $('#general_manager').addClass('d-none');
            } else {
                $('#general_manager').addClass('d-none');
                $('#regional_manager').addClass('d-none');
            }
        });
    });
</script>
<script src="{{asset('multiselect/bootstrap.multiselect.js')}}"></script>
<script>
    $('#division_id').multiselect({
            nonSelectedText: 'Select Department',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
        $('#city_id').multiselect({
            nonSelectedText: 'Select City',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
        $('#distributor_id').multiselect({
            nonSelectedText: 'Select Distributor',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
</script>
<script>
     $(document).ready(function() {
        $('#post').change(function() {
            var post = $(this).val();
            if (post != 1) {
                $('#city').removeClass('d-none');
            } else {
                $('#city').addClass('d-none');
            }
            if (post == 3) {
                $('#distributor').removeClass('d-none');
            } else {
                $('#distributor').addClass('d-none');
            }
        });
    });
    $(document).ready(function() {
        $('#user_type').change(function() {
            var user_type = $(this).val();
            if (user_type == 1) {
                $('#post').parent().addClass('d-none');

            } else {
                $('#post').parent().removeClass('d-none');
                $('#business_name').removeClass('d-none');
            }
        });
    });
</script>
<script>
    // if post is territory manager then hide canCreateBudget
    $(document).ready(function() {
        $('#post').change(function() {
            var post = $(this).val();
            if (post == 3) {
                $('#canCreateBudget').addClass('d-none');
            } else {
                $('#canCreateBudget').removeClass('d-none');
            }
        });
    });
</script>
<script>
    // when user type is distributor then hide AutoSummationBudget
    $(document).ready(function() {
        $('#user_type').change(function() {
            var user_type = $(this).val();
            if (user_type == 1) {
                $('#autoSummationBudget').addClass('d-none');
                $('#canCreateBudget').addClass('d-none');
            } else {
                $('#autoSummationBudget').removeClass('d-none');
                $('#canCreateBudget').removeClass('d-none');
            }
        });
    });
</script>




@endsection
