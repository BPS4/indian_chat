@extends('admin.pages.auth.layout')

@section('content')

<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
        <!--begin::Aside-->
        <div class="login-aside d-flex flex-column flex-row-auto cw-40" style="background-image: url({{asset('/media/custom/login-bg.png')}});">
            <!--begin::Aside Top-->
            <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15" style="padding-top: 15rem !important;">
                <!--begin::Aside header-->
                <!-- <a href="#" class="text-center mb-10">
                    <img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-letter-1.png" class="max-h-70px" alt="" />
                </a> -->
                <!--end::Aside header-->
                <!--begin::Aside title-->

                <h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #fff;">Welcome to Avana </h3>
                <p class="mb-3 text-center" style="color: #fff; font-size:16px; ">Lorem ipsum is placeholder text commonly</p>
                <p class="mt-3 text-center" style="color: #fff; font-size:14px; ">Lorem ipsum is placeholder text commonly used in the graphic, print, and
                    publishing industries for
                    previewing layouts and visual mockups.</p>

                <!--end::Aside title-->
            </div>
            <!--end::Aside Top-->
            <!--begin::Aside Bottom-->

            <!--end::Aside Bottom-->
        </div>
        <!--begin::Aside-->
        <!--begin::Content-->
        <div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
            <!--begin::Content body-->
            <div class="d-flex flex-column-fluid flex-center">
                <!--begin::Signin-->
                <div class="login-form login-signin">
                    <!--begin::Form-->
                    <form class="form" novalidate="novalidate" id="kt_login_signin_form" action="{{url('admin/auth/login')}}" method="post">
                        {{ csrf_field() }}
                        <!--begin::Title-->
                        <div class="pb-13 pt-lg-0 pt-5">
                            <!-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">{{ config('app.name') }}</h3> -->
                             <img src="/public/media/custom/logo.webp" alt="">
                            <span class="text-muted font-size-h4">.</span>
                        </div>
                        <!--begin::Title-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            @error('email')
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            @error('password')
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            @error('error')
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                            @if(request('error'))
                            <div class="alert alert-danger" role="alert">
                                <strong>{{ request('error') }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark">Mobile Number</label>
                            <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="text" name="number" autocomplete="off" isrequired="isrequired" />

                        </div>
                        <!--end::Form group-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <div class="d-flex justify-content-between mt-n5">
                                <label class="font-size-h6 font-weight-bolder text-dark pt-5">OTP</label>

                            </div>
                            <form id="otpForm" class="d-flex justify-content-center">
                        <input type="text" class="otp-input"  maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="otp-input"  maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="otp-input"  maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="otp-input"  maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="otp-input" style="" maxlength="1" pattern="[0-9]" required>
                    </form>
                    <div>
                    <a class="mt-3 opt-resent" style="float: right;
    cursor:pointer; margin-top:3rem;">Resent OTP in 21 sec</a>
                    </div>


                        </div>
                        <!--end::Form group-->
                        <!--begin::Action-->
                        <div class="pb-lg-0 pb-5">
                            <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
                        </div>
                        <!--end::Action-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Signin-->
                <!--begin::Signup-->

                <!--end::Signup-->
                <!--begin::Forgot-->

                <!--end::Forgot-->
            </div>
            <!--end::Content body-->
            <!--begin::Content footer-->

            <!--end::Content footer-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Login-->
</div>
<!--end::Main-->

@endsection

{{-- Styles Section --}}
@section('styles')
<style>
    .cw-40 {
        width: 40%;
    }
    .otp-input{
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

    @media only screen and (max-width: 900px) {
        .cw-40 {
            width: 100%;
   padding-bottom: 40px;
        }
    }
</style>
@endsection


{{-- Scripts Section --}}
@section('scripts')
{{-- vendors --}}

<script src="{{url('/')}}/js/custom.js" type="text/javascript"></script>
{{-- page scripts --}}
@endsection
