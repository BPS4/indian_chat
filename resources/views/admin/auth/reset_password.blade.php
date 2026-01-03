@extends('admin.pages.auth.layout')

@section('content')
    <!--begin::Main-->
    <div class=" vh-100">
        <div class="row h-100 m-0">
            <!-- Left Side - Welcome Section -->
            <div class="col-md-6 welcome-section text-center text-white d-flex flex-column justify-content-center">
                <h1>Welcome to Avana One!</h1>
                {{-- <p class="mb-3">Welcome to Avana One!</p> --}}
                <p class="profile-para">

                    An Exclusive and Automated One Stop Portal For All Your Sales and Order Management!.</p>
            </div>

            <!-- Right Side - Login Section -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center login-section">
                <div class="login-container p-4 rounded">
                    <div class="text-center mb-4">
                        <img src="{{ asset('media/custom/logo.webp') }}" alt="">
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger" style="background-color:#CC0001; !important" id="error-alert">
                            @foreach ($errors->all() as $error)
                                <div style="font-size: 12px ;">{{ $error }}</div>
                            @break;
                        @endforeach
                    </div>
                @endif
                <form action="{{ route('admin.reset.password') }}" method="post">
                    {{ csrf_field() }}

                    <h5 class="mb-3">New Password</h5>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control rounded" name="new_password" Id="new_password"
                            placeholder="Enter New Password" required>
                        {{-- <span class="input-group-text toggle-password" data-target="#new_password"><i
                                    class="fa fa-eye"></i></span> --}}
                    </div>
                    <h5 class="mb-3">Confirm Password</h5>

                    <div class="input-group mb-3">
                        <input type="password" class="form-control rounded" name="confirm_password"
                            id="confirm_password" placeholder="Enter Confirm Password" required disabled>
                        {{-- <span class="input-group-text toggle-password" data-target="#confirm_password"><i
                                    class="fa fa-eye"></i></span> --}}
                    </div>

                    <div class=" submit-btn">
                        <button type="submit" class="btn"> Reset Password <i
                                class="fas fa-arrow-right"></i></button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>
<!--end::Main-->
@endsection

{{-- Styles Section --}}
@section('styles')
<style>
    .welcome-section {
        background-color: #CC0001;
        /* Red background */
        padding: 50px;
        background-image: url({{ asset('/media/custom/login-bg.png') }});
        /* Use a background image for effect */
        background-size: cover;
        background-repeat: no-repeat;
    }

    .welcome-section h1 {
        font-size: 48px;
        font-weight: bold;
    }

    .welcome-section p {
        font-size: 18px;
        margin-top: 20px;
        color: #fff;
    }

    .profile-para {
        font-size: 14px !important;
    }

    /* Login Section */
    .login-section {
        background-color: #f9f9f9;
        /* Light gray background */
    }

    .login-container {
        border-radius: 10px;
        padding: 30px;
        width: 100%;
    }

    .login-container img {
        max-width: 150px;
    }

    .input-group-text {
        background-color: transparent;
        border: none;
    }

    .input-group-text i {
        font-size: 24px;
        color: #CC0001;
    }

    .btn-danger {
        background-color: #CC0001;
        border-color: #CC0001;
    }

    .btn-danger i {
        margin-left: 10px;
    }

    .submit-btn {
        width: 100%;
        display: flex;
        justify-content: center;
        margin: auto;
        align-items: center;
        text-align: center;
        margin-top: 4rem !important;

    }

    .submit-btn button {
        padding: 10px 40px;
        background-color: #CC0001;
        color: #fff !important;

    }

    .submit-btn button a {
        color: #fff !important;

    }
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
{{-- vendors --}}

<script src="{{ url('/') }}/js/custom.js" type="text/javascript"></script>
{{-- page scripts --}}
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

        $('#new_password, #confirm_password').on('keypress', function(e) {
            if (e.which === 32) {
                e.preventDefault();
            }
        });

        $('#new_password, #confirm_password').on('paste', function(e) {
            e.preventDefault();
            const pasted = (e.originalEvent || e).clipboardData.getData('text');
            const clean = pasted.replace(/\s+/g, '');
            $(this).val(clean);
        });

        $('#passwordForm').on('submit', function(e) {
            e.preventDefault();

            $('.error-msg').text('');
            $('#formMessage').removeClass().text('');

            const newPassword = $('#new_password').val().trim();
            const confirmPassword = $('#confirm_password').val().trim();

            let isValid = true;

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

            this.submit();
        });
    });
</script>


{{-- @if (session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@endif
@if (session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
@endif --}}

<script>
    setTimeout(() => {
        const errorAlert = document.getElementById('error-alert');

        if (errorAlert) errorAlert.style.display = 'none';
    }, 3000); // 3000ms = 3 seconds
</script>
@endsection
