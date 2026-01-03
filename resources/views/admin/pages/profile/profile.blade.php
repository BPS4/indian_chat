@extends('admin.layout.default')

@section('profile', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        {{-- image circle --}}
        {{-- <form action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data" id="imageUploadForm">
            @csrf
            <div class="circle-img" id="profile-image-container">
                @if ($details->profile_image)
                    <img src="{{ asset('uploads/profile/' . $details->id . '/' . $details->profile_image) }}" height="100"
                        width="100" class="rounded-circle" alt="Profile" id="profile-image">
                @else
                    <img src="{{ asset('media/custom/profile.png') }}" alt="Profile" id="profile-image">
                @endif

                <div class="photo-camera">
                    <img src="{{ asset('media/custom/photo-camera.png') }}" alt="Camera Icon">
                </div>
                <!-- Hidden input for file upload -->
                <input type="file" id="imageUpload" name="profile_image" style="display: none;" accept="image/*">
            </div>
        </form> --}}

        {{-- <div class="card-header flex-wrap border-0 pt-3 pb-0">


            <div class="card-title">
                <h3 class="card-label">Profile Information</h3>
            </div>

        </div> --}}
        {{-- <div class="item-center">
            <hr>
        </div> --}}

        {{-- <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12">
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <label>Business Name</label>
                                    <div><input type="text" name="business_name" value="{{ $details->business_name }}"
                                            class="form-control" placeholder="Enter Business Name">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Business Email</label>
                                    <div><input type="text" name="business_email" value="{{ $details->business_email }}"
                                            class="form-control" placeholder="Enter Business Email">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label>Vendor Type</label>
                                    <div>
                                        <select class="form-control" name="vendor_type">
                                            <option value="">Select Vendor Type</option>
                                            <option value="1" @if ($details->vendor_type == 1) selected @endif>Vendor
                                            </option>
                                            <option value="2" @if ($details->vendor_type == 2) selected @endif>
                                                Supplier</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Vendor Code</label>
                                    <div><input type="text" name="vendor_code" value="{{ $details->vendor_code }}"
                                            class="form-control" placeholder="Enter Vendor Code">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Contact Person Name</label>
                                    <div><input type="text" name="contact_person_name"
                                            value="{{ $details->contact_person_name }}" class="form-control"
                                            placeholder="Enter Contact Person Name">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Contact Person Email</label>
                                    <div><input type="text" name="contact_person_email"
                                            value="{{ $details->contact_person_email }}" class="form-control"
                                            placeholder="Enter Contact Person Email">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <div>
                                        <select class="form-control" name="city">
                                            <option value="">Select City</option>
                                            <option value="1" @if ($details->city == 1) selected @endif>
                                                Delhi</option>
                                            <option value="2" @if ($details->city == 2) selected @endif>
                                                Mumbai</option>
                                        </select>
                                    </div>
                                </div>






                                <div class="form-group col-md-12">
                                    <center><button class="btn btn-success">Update</button></center>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}

        <div class="card-header flex-wrap border-0 pt-3 pb-0">


            <div class="card-title">
                <h3 class="card-label">Change Password</h3>
            </div>
            {{-- horizontal line --}}

        </div>
        <div class="item-center">
            <hr>
        </div>

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <!-- Success/Error Message Box -->
                    <div id="formMessage" class="mb-3"></div>

                    <form action="{{ route('admin.password.change') }}" method="POST">
                        @csrf
                        <div class="col-lg-9 col-xl-12">
                            <div class="row align-items-center">

                                <div class="form-group col-md-6">
                                    <label>Current Password</label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control" placeholder="Enter Current Password">
                                        <span class="input-group-text toggle-password" data-target="#current_password"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <div class="error-msg text-danger small" id="error_current_password"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="new_password" class="form-control"
                                            placeholder="Enter New Password">
                                        <span class="input-group-text toggle-password" data-target="#new_password"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <div class="error-msg text-danger small" id="error_new_password"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" id="confirm_password"
                                            class="form-control" placeholder="Enter Confirm Password" disabled>
                                        <span class="input-group-text toggle-password" data-target="#confirm_password"><i
                                                class="fa fa-eye"></i></span>
                                    </div>
                                    <div class="error-msg text-danger small" id="error_confirm_password"></div>
                                </div>

                                {{-- <div class="form-group col-md-12">
                                    <center><button type="submit" class="btn btn-success">Update</button></center>
                                </div> --}}

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn  bg-brown add">Save</button>
                                    <button type="reset" class="btn bg-gray px-5">Cancel</button>
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

    <style>
        .circle-img {
            margin: 2rem 2rem 0rem 2rem;
            position: relative;
        }

        .circle-img img {
            cursor: pointer;
        }

        .photo-camera {
            position: absolute;
            bottom: 0.75rem;
            left: 6rem;
        }
    </style>
@endsection


{{-- Scripts Section --}}
@section('scripts')

    <script>
        // Image Upload
        document.getElementById('profile-image').addEventListener('click', function() {
            document.getElementById('imageUpload').click();
        });

        document.getElementById('imageUpload').addEventListener('change', function() {
            document.getElementById('imageUploadForm').submit();
        });
    </script>

    <script>
        $(document).ready(function() {

            $('.toggle-password').on('click', function() {
                const target = $($(this).data('target'));
                const type = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            $('#new_password').on('input', function() {
                const newPassword = $(this).val().trim();
                $('#confirm_password').prop('disabled', newPassword === '');
            });

            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();

                $('.error-msg').text('');
                $('#formMessage').removeClass().text('');

                const currentPassword = $('#current_password').val().trim();
                const newPassword = $('#new_password').val().trim();
                const confirmPassword = $('#confirm_password').val().trim();

                let isValid = true;

                if (!currentPassword) {
                    $('#error_current_password').text("Current password is required.");
                    isValid = false;
                }

                if (!newPassword) {
                    $('#error_new_password').text("New password is required.");
                    isValid = false;
                }

                if (!confirmPassword) {
                    $('#error_confirm_password').text("Please confirm your new password.");
                    isValid = false;
                } else if (newPassword && confirmPassword !== newPassword) {
                    $('#error_confirm_password').text("Passwords do not match.");
                    isValid = false;
                }

                if (!isValid) return;


            });
        });
    </script>






@endsection
