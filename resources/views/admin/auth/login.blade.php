@extends('admin.pages.auth.layout')

@section('content')

<style>
    .internal-container{
        width: 70% !important;
    }
    .img-div {
        margin-bottom: 50px !important;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .img-div img{
        width :100px
    }
    .password{
        margin-top: 25px;
    }
    .submit{
        border-radius: 5px;

    }
</style>

    <!--begin::Main-->
    <div class=" vh-100">
        <div class="row h-100 m-0">
            <!-- Left Side - Welcome Section -->
            <div class="col-md-6 welcome-section text-center text-white d-flex flex-column justify-content-center">
                <h1>Welcome to My Indian Bussiness!</h1>
                {{-- <p class="mb-3">Welcome to Avana One!</p> --}}
                <p class="profile-para">

                    An Exclusive and Automated One Stop Portal For All Bussiness Promotion Management!.</p>
            </div>

            <!-- Right Side - Login Section -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center login-section">
                <div class="login-container internal-container p-4 rounded">
                    <div class="text-center mb-4 img-div">
                        <img src="{{ asset('media/logos/hotel-logo.png') }}" alt="">
                    </div>

                    <h5 class="mb-3">Email or Phone</h5>
                    <form action="{{ route('admin.login') }}" method="post">
                        {{ csrf_field() }}
                        <div class="input-group mb-3">
                            <input type="text" class="form-control rounded" name="email"
                                placeholder="Enter Email or Phone" required>
                        </div>
                        <h5 class="mb-3 password" >Password</h5>

                        <div class="input-group mb-3 position-relative">
                            <input type="password" class="form-control rounded" name="password" id="password"
                                placeholder="Enter Password" required>
                            {{-- <span class="position-absolute input-group-text"
                                style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"
                                onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span> --}}
                        </div>

                        @if ($errors->any())
                            <div class="w-100" style="color: #CC0001; text-align: left;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        <div class=" submit-btn">
                            <button type="submit" class="btn submit"> Submit <i class="fas fa-arrow-right" style="color: white; margin-left: 7px; "></i></button>
                        </div>
                    </form>
                    <div class="text-center pt-5">
                        <p style="margin-top:5px"><a href="{{ route('admin.forgot.password') }}"> Forget Password
                                {{-- <i class="fas fa-arrow-right text-dark"></i> --}}
                            </a>
                        </p>
                    </div>
                    {{-- <div class="text-center pt-5">
                        <p style="margin-top:-13px !important">Do'not have an account <a
                                href="{{ route('admin.register') }}"> Register
                                <i class="fas fa-arrow-right text-dark"></i></a>
                        </p>
                    </div> --}}

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


    {{-- <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script> --}}

    <script src="{{ url('/') }}/js/custom.js" type="text/javascript"></script>
    {{-- page scripts --}}
@endsection
