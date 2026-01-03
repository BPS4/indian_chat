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
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid mobile number'], 422);
        }

        $mobileNumber = $request->input('mobile');
        // $otp = rand(100000, 999999);  // Generate a 6-digit OTP
        $otp = 1234;  // Generate a 4-digit OTP



        $response = Helper::sendOtpToPhone($mobileNumber, $otp);
        if ($response['success']) {
            $user = User::where('mobile', $mobileNumber)->first();

            // If not found, create new user
            if (!$user) {
                $user = User::create([
                    'mobile' => $mobileNumber,
                    'email'  => null,
                    'role_id'  => User::CUSTOMER,
                    'password' => bcrypt(Str::random(10)),
                ]);
            }
            UserAuth::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp' => $otp,
                    'hash' => bin2hex(random_bytes(16)),
                    'is_verified' => false,
                    'expire_at' => now()->addMinutes(5),
                ]
            );

            return response()->json(['message' => 'OTP sent successfully']);
        } else {
            return response()->json(['message' => 'Failed to send OTP', 'details' => $response['error']], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'login' => 'required',
                'otp' => 'required|digits:4',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }

            $login = $request->input('login');
            $otp = $request->input('otp');

            // Determine if login is mobile or email
            if (is_numeric($login) && strlen($login) === 10) {
                $user = User::where('mobile', $login)->first();
            } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $login)->first();
            } else {
                return response()->json(['message' => 'Invalid login format. Use 10-digit mobile or valid email'], 422);
            }

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            if ($user->status == 0) {
                return response()->json(['message' => 'Account is blocked/deleted. Please contact to admin'], 401);
            }
            // Check OTP
            $auth = UserAuth::where('user_id', $user->id)
                ->where('otp', $otp)
                ->where('is_verified', false)
                ->where('expire_at', '>=', now())
                ->first();

            if (!$auth) {
                return response()->json(['message' => 'Invalid or expired OTP'], 400);
            }

            // Mark OTP as used
            $auth->is_verified = true;
            $auth->save();


            Auth::login($user);

            // $token = $user->createToken('mobile-login')->plainTextToken;
            $accessToken  = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'OTP verified successfully',
                // 'hash' => $auth->hash,
                'access_token' => $accessToken,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error(['message' => $e->getMessage()]);
            return response(['message' => 'something went wrong', 'error' => $e->getMessage()], 500);
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
