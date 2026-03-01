<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet;
use App\Models\Commision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function sessionToken()
    {

        $uuid = (string) Str::uuid();

        $issuedAt = Carbon::now()->timestamp;
        $expiresAt = Carbon::now()->addMinutes(720)->timestamp; // integer

        $payload = JWTFactory::customClaims([
            'sub' => $uuid,
            'uuid' => $uuid,
            'type' => 'session',
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ])->make();




        $token = JWTAuth::encode($payload)->get();

        return response()->json(['session_token' => $token]);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10|unique:users,mobile',
            'referral_code' => 'nullable|exists:users,referral_code',
            'password' => 'required|string|min:6',
        ], [
            'mobile.unique' => 'This mobile number is already registered.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'referral_code.exists' => 'Invalid referral code.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        //  if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validator->errors()
        //     ], 422);
        // }

        $mobileNumber = $request->input('mobile');
        $password = $request->input('password'); // Use provided password or generate random one

        $sponsor = null;

        if ($request->filled('referral_code')) {
            $sponsor = User::where('referral_code', $request->referral_code)->first();
        } else {
            $sponsor = User::where('role_id', 1)->first();
        }



        $user = User::where('mobile', $mobileNumber)->first();

        // If not found, create new user
        if (!$user) {
            $user = User::create([
                'mobile' => $mobileNumber,
                'email'  => null,
                'role_id'  => User::CUSTOMER,
                'password' => Hash::make($request->password),
                'referral_code' => generateReferralCode(),
                'referred_by' => $sponsor?->id,
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            if ($user) {

                DB::transaction(function () use ($user) {

                    $refral_commision = Commision::select('referral_commision')->first();
                    $joining_bonus = Commision::select('joining_bonus')->first();

                    // Mark OTP as verified
                    // $auth->update(['is_verified' => true]);

                    // Create wallet if not exists
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0]
                    );



                    // 🔒 Apply bonus ONLY ON FIRST VERIFICATION
                    if ($wallet->balance == 0) {

                        // Joining bonus
                        $wallet->increment('balance', $joining_bonus->joining_bonus);

                        // Referral bonus to sponsor
                        if ($user->referred_by) {
                            Wallet::firstOrCreate(
                                ['user_id' => $user->referred_by],
                                ['balance' => 0]
                            )->increment('balance', $refral_commision->referral_commision);
                        }
                    }
                });
                // dd('hi');

                Auth::login($user);

                $accessToken = JWTAuth::fromUser($user);

                return response()->json([
                    'message' => 'OTP verified successfully',
                    'access_token' => $accessToken,
                    'user' => $user->load('wallet'),
                ]);
            }
        }

        // dd('hi');

        // UserAuth::updateOrCreate(
        //     ['user_id' => $user->id],
        //     [
        //         'otp' =>  Hash::make($request->password),
        //         'hash' => bin2hex(random_bytes(16)),
        //         'is_verified' => false,
        //         'expire_at' => now()->addMinutes(5),
        //     ]
        // );

        return response()->json(['message' => 'Register successfully']);
    }

    public function verifyOtp(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'mobile' => 'required',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            $login = $request->input('mobile');
            $password = $request->input('password'); // Use provided password or OTP

            // Determine if login is mobile or email
            if (is_numeric($login) && strlen($login) === 10) {
                $user = User::where('mobile', $login)
                    ->where('status', 1)
                    ->first();
                if (!$user || !Hash::check($password, $user->password)) {
                    return response()->json(['message' => 'Invalid credentials'], 401);
                }
            } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $login)->where('password',  Hash::make($password))->where('status', 1)->first();
            } else {
                return response()->json(['message' => 'Invalid login format'], 422);
            }

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // dd($user);
            // if ($user->status == 0) {
            //     return response()->json(['message' => 'Account is blocked/deleted'], 401);
            // }

            // Check OTP
            // $auth = UserAuth::where('user_id', $user->id)
            //     ->where('is_verified', false)
            //     ->where('expire_at', '>=', now())
            //     ->first();

            // if (!$auth || !Hash::check($password, $auth->otp)) {
            //     return response()->json(['message' => 'Invalid or expired OTP'], 400);
            // }




            // dd($refral_commision, $joining_bonus);


            DB::transaction(function () use ($user) {

                // $refral_commision = Commision::select('referral_commision')->first();
                // $joining_bonus = Commision::select('joining_bonus')->first();

                // // Mark OTP as verified
                // // $auth->update(['is_verified' => true]);

                // // Create wallet if not exists
                // $wallet = Wallet::firstOrCreate(
                //     ['user_id' => $user->id],
                //     ['balance' => 0]
                // );



                // // 🔒 Apply bonus ONLY ON FIRST VERIFICATION
                // if ($wallet->balance == 0) {

                //     // Joining bonus
                //     $wallet->increment('balance', $joining_bonus->joining_bonus);

                //     // Referral bonus to sponsor
                //     if ($user->referred_by) {
                //         Wallet::firstOrCreate(
                //             ['user_id' => $user->referred_by],
                //             ['balance' => 0]
                //         )->increment('balance', $refral_commision->referral_commision);
                //     }
                // }
            });
            // dd('hi');

            Auth::login($user);

            $accessToken = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'OTP verified successfully',
                'access_token' => $accessToken,
                'user' => $user->load('wallet'),
            ]);
        } catch (\Exception $e) {
            Log::error(['verifyOtp_error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function forgetPassword_Request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email address'
            ], 422);
        }

        try {

            DB::beginTransaction();

            $user = User::where('email', $request->email)->firstOrFail();

            // Generate 6-digit OTP
            $otp = random_int(100000, 999999);

            // Store / Update OTP record
            UserAuth::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp'         => $otp,
                    'hash'        => bin2hex(random_bytes(32)),
                    'is_verified' => false,
                    'expire_at'   => now()->addMinutes(5),
                ]
            );

            // 🔹 Send OTP via email (Example)
            Mail::raw("Your OTP is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset OTP');
            });

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->firstOrFail();

            $userAuth = UserAuth::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('expire_at', '>', now())
                ->first();

            if (!$userAuth) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP'
                ], 422);
            }

            // Update user password
            $user->update([
                'password' => $request->new_password
            ]);

            // Mark OTP as verified
            $userAuth->update(['is_verified' => true]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }




    public function logout(Request $request)
    {
        try {
            // Invalidate the JWT token
            $token = JWTAuth::getToken();

            if ($token) {
                JWTAuth::invalidate($token);
            }


            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function admin_login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
        ]);

        // Get admin user
        $user = User::where('email', $request->email)
            ->where('role_id', 1)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Log the user in (without password)
        Auth::login($user);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Admin login successful',
            'token'   => $token,
            'user'    => $user
        ]);
    }
}
