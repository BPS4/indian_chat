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

                    <h5 class="mb-3">Mobile Number</h5>
                    <form action="{{ url('admin/auth/login') }}" method="post">
                        {{ csrf_field() }}
                        <div class="input-group mb-3">
                            <input type="text" class="form-control rounded" name="mobile" id="mobile"
                                placeholder="Enter Mobile Number" maxlength="10" pattern="\d{10}" inputmode="numeric"
                                required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">

                        </div>
                        @if ($errors->any())
                            <div class="w-100" style="color: red; text-align: left;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif




                        <div class=" submit-btn">

                            <button type="submit" class="btn"> SEND OTP <i class="fas fa-arrow-right"></i></button>
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
@endsection
