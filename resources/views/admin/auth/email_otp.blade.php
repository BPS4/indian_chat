@extends('admin.pages.auth.layout')

@section('content')
    <!--begin::Main-->
    <div class="vh-100">
        <div class="row m-0 h-100">
            <!-- Left Side - Welcome Section -->
            <div class="col-md-6 welcome-section text-center text-white d-flex flex-column justify-content-center">
                <h1>Welcome to Avana One!</h1>
                {{-- <p class="mb-3">Lorem ipsum is placeholder text commonly</p> --}}
                <p class="profile-para">An Exclusive and Automated One Stop Portal For All Your Sales and Order Management!.
                </p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger" style="background-color:#CC0001; !important" id="error-alert">
                    @foreach ($errors->all() as $error)
                        <div style="font-size: 12px ;">{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <!-- Right Side - Login Section -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center login-section">
                <div class="login-container p-4 rounded verify-otp">
                    <div class="text-center mb-4">
                        <img src="{{ asset('media/custom/logo.webp') }}" alt="">
                    </div>

                    @php $email = session('email') ; @endphp
                    <p class="mb-4 text-center">Send OTP on this {{ session('loginField') }} <br>
                        @if (session('loginField') == 'mobile')
                            <strong>XXXX{{ substr(session('email'), -4) }}</strong>
                        @else
                            <strong>XXXX{{ substr(session('email'), -14) }}</strong>
                        @endif
                    </p>
                    <a href="{{ route('admin.forgot.password') }}" class="mb-4 change-num">Change
                        {{ ucfirst(session('loginField')) }}</a>

                    <form id="otpForm" action="{{ route('admin.otp.verify') }}" method="post">
                        @csrf
                        <div class="d-flex justify-content-center align-items-center">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]"
                                    required>
                            @endfor
                        </div>

                        <div id="otp-message" class="text-danger text-center mt-2"></div>
                        <div>
                            <a class="mt-3 opt-resent"></a>

                        </div>
                        <div class="submit-btn">
                            <button type="submit" class="btn btn-block">VERIFY OTP <i
                                    class="fas fa-arrow-right"></i></button>
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
            width: 200px;

        }

        .submit-btn button a {
            color: #fff !important;

        }

        .otp-input {
            width: 50px;
            height: 50px;
            margin: 0 10px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .otp-input:focus {
            border-color: var(--red);
            ;
        }

        .verify-otp a {
            color: #042EFF;
            text-decoration: none;
            cursor: pointer;
        }

        .change-num {
            text-align: center !important;
            display: flex;
            justify-content: center;
        }

        .opt-resent {
            float: right;
            margin-right: 12rem;
        }
    </style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}

    <script>
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', (e) => {
                const currentInput = e.target;
                const nextInput = inputs[index + 1];
                const prevInput = inputs[index - 1];

                if (currentInput.value.length > 0 && nextInput) {
                    nextInput.focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                const currentInput = e.target;
                const prevInput = inputs[index - 1];

                if (e.key === "Backspace" && !currentInput.value && prevInput) {
                    prevInput.focus();
                    prevInput.value = ''; // Optional: Clear previous input on backspace
                }
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
        // session expired time
        const sessionExpiredTime = 50; // in seconds
        let timeLeft = sessionExpiredTime;
        let timerInterval = null;

        // start timer when the timer end then show the resend otp link


        function startTimer() {
            timerInterval = setInterval(() => {
                timeLeft--;
                document.querySelector('.opt-resent').textContent = `Resend OTP in ${timeLeft} seconds`;

                if (timeLeft === 0) {
                    clearInterval(timerInterval);
                    // clearOtpSession();
                    const resendOtpForm = document.createElement('form');
                    resendOtpForm.method = 'POST';
                    resendOtpForm.action = "{{ route('admin.send.otp') }}";
                    resendOtpForm.innerHTML = `
                    {{ csrf_field() }}
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="resend-otp btn btn-link">Resend OTP</button>
                `;
                    document.querySelector('.opt-resent').innerHTML = '';
                    document.querySelector('.opt-resent').appendChild(resendOtpForm);
                }
            }, 1000);
        }

        startTimer();
    </script>
    <script>
        $('#otpForm').on('submit', function(e) {
            e.preventDefault();

            let otp = [];
            $('.otp-input').each(function() {
                otp.push($(this).val());
            });

            $.ajax({
                url: "{{ route('admin.otp.verify') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    otp: otp
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#otp-message')
                            // .removeClass('text-danger')
                            // .addClass('text-success')
                            .text('OTP verified successfully!');

                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 1500);

                    } else {
                        $('#otp-message').text(response.message);
                    }
                },
                error: function(xhr) {
                    let msg = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $('#otp-message').text(msg);
                }
            });
        });
    </script>
@endsection
