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

                    <form action="{{ route('admin.register') }}" method="POST">
                        @csrf

                        <h5 class="mb-3">Name</h5>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control rounded" name="name" id="name"
                                placeholder="Enter Name" required>
                        </div>

                        <h5 class="mb-3">Email</h5>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control rounded" name="email" placeholder="Enter Email"
                                required>
                        </div>
                        @if (!empty($roles))
                            <h5 class="mb-3">Role</h5>
                            <div class="input-group mb-3">
                                <select class="form-control rounded" name="role" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}
                                    ">
                                            {{ $role->role }} </option>
                                    @endforeach

                                </select>
                            </div>
                        @endif

                        <h5 class="mb-3">Password</h5>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control rounded" name="password" placeholder="Enter Password"
                                required>
                        </div>

                        <h5 class="mb-3">Confirm Password</h5>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control rounded" name="password_confirmation"
                                placeholder="Confirm Password" required>
                        </div>

                        @if ($errors->any())
                            <div class="w-100" style="color: red; text-align: left;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="submit-btn ">
                            <button type="submit" class="btn btn-primary">
                                Register <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center pt-5">
                        <p style="pe-5">Already have an account <a href="{{ route('admin.login') }}"> login <i
                                    class="fas fa-arrow-right text-dark"></i></a>
                        </p>
                    </div>




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
    <script>
        document.getElementById('name').addEventListener('input', function() {

            this.value = this.value.replace(/[0-9]/, '');


        });
    </script>
    @if (session('success'))
        <script>
            toastr.success("{{ session('success') }}");
        </script>
    @endif
    @if (session('error'))
        <script>
            toastr.error("{{ session('error') }}");
        </script>
    @endif
    {{-- page scripts --}}
@endsection
