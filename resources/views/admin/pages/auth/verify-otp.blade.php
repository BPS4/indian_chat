@extends('admin.pages.auth.layout')

@section('content')

<!--begin::Main-->
<div class="vh-100">
    <div class="row m-0 h-100">
        <!-- Left Side - Welcome Section -->
        <div class="col-md-6 welcome-section text-center text-white d-flex flex-column justify-content-center">
            <h1>Welcome to Avana One!</h1>
            {{-- <p class="mb-3">Lorem ipsum is placeholder text commonly</p> --}}
            <p class="profile-para">An Exclusive and Automated One Stop Portal For All Your Sales and Order Management!.</p>
        </div>

        <!-- Right Side - Login Section -->
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center login-section">
            <div class="login-container p-4 rounded verify-otp">
                <div class="text-center mb-4">
                    <img src="{{asset('media/custom/logo.webp')}}" alt="">
                </div>

                <p class="mb-4 text-center">Send OTP on this Mobile Number <br> <strong>XXXXX{{ substr($mobile, -4) }}</strong></p>
                <a  href="{{url('admin/login')}}"
                class="mb-4 change-num">Change Mobile Number</a>

                <form id="otpForm" class=" " action="{{url('admin/auth/verifyOtp')}}" method="post">
                    {{ csrf_field() }}

                    <input type="hidden" name="hash" value="{{ $hash }}">
                       <input type="hidden" name="mobile"  value="{{ $mobile }}" >

                    {{-- opt will be the combine of below number --}}
                    <div class="d-flex justify-content-center align-items-center">


                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    <input type="text" class="otp-input" name="otp[]" maxlength="1" pattern="[0-9]" pattern="\d" inputmode="numeric" required>
                    </div>
                    <script>
                        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
                            input.addEventListener('input', function () {
                                this.value = this.value.replace(/[^0-9]/g, ''); // Only digits
                                if (this.value && index < inputs.length - 1) {
                                    inputs[index + 1].focus(); // Move to next
                                }
                            });

                            input.addEventListener('keydown', function (e) {
                                if (e.key === 'Backspace' && !this.value && index > 0) {
                                    inputs[index - 1].focus(); // Move back on backspace
                                }
                            });

                            input.addEventListener('paste', function (e) {
                                e.preventDefault(); // Disable paste
                            });
                        });
                    </script>


                    @if ($errors->any())
                            <div class="w-100" style="color: red; text-align: center; margin-top:20px">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                <div>
                <a class="mt-3 opt-resent"></a>
                </div>
                <div class="submit-btn">
                    <button type="submit" class="btn btn-block">VERIFY OTP <i class="fas fa-arrow-right"></i></button>
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
  background-image: url({{asset('/media/custom/login-bg.png')}});
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
  background-color:#CC0001;
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
            prevInput.value = '';  // Optional: Clear previous input on backspace
        }
    });
});

</script>

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
                const resendOtpForm = document.createElement('form');
                resendOtpForm.method = 'POST';
                resendOtpForm.action = "{{url('admin/auth/resendOtp')}}";
                resendOtpForm.innerHTML = `
                    {{ csrf_field() }}
                    <input type="hidden" name="hash" value="{{ $hash }}">
                    <button type="submit" class="resend-otp btn btn-link">Resend OTP</button>
                `;
                document.querySelector('.opt-resent').innerHTML = '';
                document.querySelector('.opt-resent').appendChild(resendOtpForm);
            }
        }, 1000);
    }

    startTimer();



</script>

<script src="{{url('/')}}/js/custom.js" type="text/javascript"></script>
{{-- page scripts --}}
@endsection
