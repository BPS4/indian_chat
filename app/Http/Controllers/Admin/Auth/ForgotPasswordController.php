<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Mail\SendOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    public function showForgotForm()
    {

        return view('admin.auth.forget_password');
    }

    public function sendOtp(Request $request)
    {
        $input = $request->input('email');
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);

        $loginField = $isEmail ? 'email' : 'mobile';

        $rules = [
            'email' => ['required'],
        ];

        if ($isEmail) {
            $rules['email'][] = 'email';
            $rules['email'][] = 'exists:users,email';
        } else {
            $rules['email'][] = 'digits:10';
            $rules['email'][] = 'exists:users,mobile';
        }

        $messages = [
            'email.required' => 'Email or mobile number is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'This :attribute is not registered in our system.',
            'email.digits' => 'Mobile number must be exactly 10 digits.',

        ];

        // $request->validate($rules, $messages);

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();

            return redirect()->back()
                ->with('error', $firstError)
                ->withInput();
        }


        // $otp = rand(100000, 999999);
        $otp = 123456;

        Session::put(['otp' => $otp, 'otp_created_at' => now()]);


        if ($loginField === 'mobile') {
            Helper::sendOtpToPhone($input, $otp);
            Session::put('email', $input);
        } else {
            Mail::to($input)->send(new SendOtpMail($otp, $input));
            Session::put('email', $input);
        }
        Session::put('loginField', $loginField);

        return redirect()->route('admin.verify.otp')
            ->with('success', 'OTP sent to your ' . $loginField . '.');
    }


    public function showVerifyForm()
    {
        return view('admin.auth.email_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|array']);

        $otp = implode('', $request->otp);

        if (session()->has('otp') && session()->has('otp_created_at')) {
            $otpCreatedAt = Carbon::parse(session('otp_created_at'));
            $minutesDiff = $otpCreatedAt->diffInMinutes(now(), false);

            if ($minutesDiff > 10 || $minutesDiff < 0) {
                session()->forget(['otp', 'otp_created_at']);
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP has expired.'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP not found.'
            ]);
        }

        if ($otp == session('otp')) {
            Session::put('otp_verified', true);
            Session::forget(['otp', 'otp_created_at']);
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('admin.reset.password.form')
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid OTP. Please try again.'
        ]);
    }



    public function resetPasswordForm()
    {
        return view('admin.auth.reset_password');
    }
    public function resetPassword(Request $request)
    {

        $rules = [
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:new_password',
        ];

        $messages = [
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'The new password must be at least 6 characters.',
            'confirm_password.required' => 'Please confirm your new password.',
            'confirm_password.min' => 'The confirmation password must be at least 6 characters.',
            'confirm_password.same' => 'The confirmation does not match the new password.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();

            return redirect()->back()
                ->with('error', $firstError)
                ->withInput();
        }


        if (!Session::get('otp_verified')) {
            return redirect()->route('admin.forgot.password')->with(['error' => 'Unauthorized action']);
        }
        if (Session::get('loginField') == 'mobile') {
            $user = User::where('mobile', Session::get('email'))->first();
        } else {
            $user = User::where('email', Session::get('email'))->first();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Session::forget(['otp', 'email', 'otp_verified', 'loginField']);

        return redirect()->route('admin.login.form')->with('success', 'Password reset successfully');
    }
}
