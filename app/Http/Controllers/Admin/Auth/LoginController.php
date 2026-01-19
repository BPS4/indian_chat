<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Redirect;
use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    /**
     * Load admin login page
     * @method index
     * @param  null
     *
     */
     public function main()
    {
 
        return view('index');
    }

    public function index()
    {

        // dd('hi');


        // return view('admin.pages.auth.mobile-otp');
        return view('admin.auth.login');
    }

    public function verify()
    {
        return view('admin.pages.auth.verify-otp');
    }



    /**
     * Admin login and their employee
     * @method login
     * @param null
     */
    // public function login(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return redirect("admin/")->withErrors($validator);
    //     }
    //     $userData = array(
    //         'email' => $req->get('email'),
    //         'password' => $req->get('password')
    //     );

    //     if (Auth::attempt($userData)) {


    //         $user = Auth::user();
    //         $id = $user->id;
    //         $email = $user->email;
    //         $name = $user->name;
    //         $role = $user->role_id;
    //         if ($user->status == 1) {
    //             $systemRoles = getSystemRoles($role)->toArray();

    //             if (count($systemRoles)) {
    //                 $systemRoles = json_decode($systemRoles[0]['permission'], 1);
    //             }
    //             Session::put('id', $id);
    //             Session::put('name', $name);
    //             Session::put('email', $email);
    //             Session::put('role', $role);
    //             Session::put('access_name', 'admin');
    //             Session::put('system_roles', $systemRoles);
    //             /**
    //              * Update last login and last login IP
    //              */
    //             $this->authenticated($req, $user);
    //             return redirect("admin/dashboard");
    //         } else {
    //             Auth::logout();

    //             Session::flash('status', "This user has been deactivated.");
    //             return redirect("admin?error=This user has been deactivated.")->withError(['error' => 'This user has been deactivated.']);
    //         }
    //     } else {

    //         Auth::logout();
    //         Session::flush();
    //         Session::flash('status', "Invalid Login");
    //         return redirect("admin/")->withErrors(['error' => 'Invalid Email and Password.']);
    //     }
    // }

    // public function login(Request $request)
    // {

    //     try {
    //         if ($request->isMethod('post')) {

    //             $validator = Validator::make($request->all(), [
    //                 // 'email' => 'required|email',
    //                 'mobile' => 'required|digits:10',
    //             ], [
    //                 'mobile.required' => 'Mobile number is required.',
    //                 'mobile.digits' => 'Mobile number should be 10 digits.',
    //                 'email.required' => 'Email is required.',
    //                 'email.email' => 'Email should be in email format.',
    //             ]);
    //             if ($validator->fails()) {
    //                 // return redirect("admin/")->withErrors($validator);
    //                 return redirect('admin/')
    //                     ->withErrors($validator)
    //                     ->withInput();
    //             } else {

    //                 $mobile = $request->mobile;

    //                 // Generate a random 6-digit OTP
    //                 // $otp = rand(100000, 999999);
    //                 $otp = 123456;

    //                 // Save or update user
    //                 $user = User::where('mobile', $mobile)->first();


    //                 if ($user) {
    //                     $userAuth = UserAuth::create([
    //                         'user_id' => $user->user_id,
    //                         'otp' => $otp,
    //                         'hash' => bin2hex(random_bytes(16)),
    //                         'status' => 'active',
    //                         'status_expiry' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
    //                     ]);

    //                     $phone = $user->mobile;
    //                     //  $result = $this->sendOtp($phone, $otp);
    //                     //  dd($result);

    //                     // if ($result['success']) {
    //                     //     echo "OTP sent successfully!";
    //                     // } else {
    //                     //     echo "Failed to send OTP: " . $result['error'];
    //                     // }


    //                     return view('admin.pages.auth.verify-otp', [
    //                         'hash' => $userAuth->hash,
    //                         'mobile' => $mobile,
    //                         'status_expiry' => $userAuth->status_expiry
    //                     ])->with('success', 'OTP sent successfully.');
    //                 } else {
    //                     return redirect("admin/")->withErrors(['error' => 'Mobile number not found.']);
    //                 }
    //             }
    //             return view('admin.pages.auth.mobile-otp')->with('success', 'OTP sent successfully.');
    //         } else {
    //             return view('admin.pages.auth.mobile-otp')->with('error', 'Invalid request.');
    //         }
    //     } catch (\Exception $e) {
    //         dd($e);
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }


    public function login(Request $request)
    {
        $input = $request->input('email');
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);

        $loginField = $isEmail ? 'email' : 'mobile';
        $loginInput = $input;

        $rules = [
            'email' => ['required'],
            'password' => ['required', 'string'],
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
            'email.exists' => 'This credential is not registered in our system.',
            'email.digits' => 'Mobile number must be exactly 10 digits.',
            'password.required' => 'Password cannot be empty.',
            'password.string' => 'Invalid password format.',
        ];

        $request->validate($rules, $messages);

        // Fetch user
        $checkUser = User::where($loginField, $loginInput)->first();
        if (!$checkUser) {
            return redirect()->back()->with(['error' => 'No user found with this credential.']);
        }

        if ($checkUser->status == 0) {
            return redirect()->back()->with(['error' => 'Your account is suspended. Please contact the Admin.']);
        }

        $credentials = [
            $loginField => $loginInput,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $id = $user->id;
            $email = $user->email;
            $name = $user->name;
            $role = $user->role_id;

            $systemRoles = getSystemRoles($role)->toArray();
            if (count($systemRoles)) {
                $systemRoles = json_decode($systemRoles[0]['permission'], true);
            }

            Session::put([
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'access_name' => 'admin',
                'system_roles' => $systemRoles,
                'financialYear' => date('Y') . '-' . (date('Y') + 1),
            ]);

            return redirect(url('admin/dashboard'));
        }

        return redirect()->back()->with(['error' => 'Invalid credentials.']);
    }


    function sendOtp($phoneNumber, $otp)
    {
        $apiUrl = 'https://sms.aanviwireless.com/api/sms/send';
        $token = '3856647f5ada2251447b39acaa5beeec';
        $sender = 'AVANA';
        $templateId = '1707175014877153767';

        $message = "Your one-time password (OTP) for login in the Avana One is $otp . Please enter this OTP to complete your login in Avana One Portal. Thank you! - Avana One";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post($apiUrl, [
            'api_key'     => $token,
            'sender_name'     => $sender,
            'phone_number' => $phoneNumber,
            'message'    => $message,
            'template_id' => $templateId,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'response' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->body()];
        }
    }

    public function verifyOtp(Request $request)
    {
        try {

            if ($request->isMethod('post')) {

                $validator = Validator::make($request->all(), [
                    'hash' => 'required',
                    'otp' => 'required|array|size:6',
                ], [
                    'hash.required' => 'Hash is required.',
                    'otp.required' => 'OTP is required.',
                    'otp.array' => 'OTP should be an array.',
                ]);

                if ($validator->fails()) {
                    return redirect("admin/")->withErrors($validator);
                } else {


                    $hash = $request->hash;
                    $otp = implode('', $request->otp);


                    $userAuth = UserAuth::where('hash', $hash)->where('otp', $otp)
                        ->where('status_expiry', '>=', date('Y-m-d H:i:s'))->first();

                    if ($userAuth) {
                        // dd($userAuth);
                        $user = User::where('user_id', $userAuth->user_id)->first();

                        Auth::login($user);

                        $id = $user->id;
                        $email = $user->email;
                        $name = $user->name;
                        $role = $user->role_id;
                        if ($user->status == 1) {
                            $systemRoles = getSystemRoles($role)->toArray();

                            if (count($systemRoles)) {
                                $systemRoles = json_decode($systemRoles[0]['permission'], 1);
                            }
                            Session::put('id', $id);
                            Session::put('name', $name);
                            Session::put('email', $email);
                            Session::put('role', $role);
                            Session::put('access_name', 'admin');
                            Session::put('system_roles', $systemRoles);
                            Session::put('financialYear', date('Y') . '-' . (date('Y') + 1));
                            /**
                             * Update last login and last login IP
                             */
                            $this->authenticated($request, $user);
                            return redirect("admin/dashboard");
                        } else {
                            Auth::logout();

                            Session::flash('status', "This user has been
                            deactivated.");
                            return redirect("admin?error=This user has been deactivated.")->withError(['error' => 'This user has been
                            deactivated.']);
                        }
                    } else {
                        // dd($request->all());
                        //  dd($request->mobile);
                        // return redirect("admin/auth/login")->withErrors(['error' => 'Invalid OTP.']);
                        return view('admin.pages.auth.verify-otp', [
                            'hash' => $request->hash ?? null,
                            'mobile' => $request->mobile ?? null,
                            'status_expiry' => $userAuth->status_expiry ?? null,
                        ])->withErrors(['Invalid OTP']);
                    }
                }
                return view('admin.pages.auth.mobile-otpmobile-otp')->with('success', 'OTP sent successfully.');
            } else {
                return view('admin.pages.auth.mobile-otp')->with('error', 'Invalid request.');
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return  redirect()->route('admin.login.form');
    }
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    function authenticated($request, $user)
    {
        $user->update([
            'last_login' => date('Y-m-d H:i:s'),
            'last_login_ip' => $request->getClientIp()
        ]);
    }

    public function resendOtp(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                // dd($request->all());

                $validator = Validator::make($request->all(), [
                    'hash' => 'required',
                ], [
                    'hash.required' => 'Hash is required.',
                ]);

                if ($validator->fails()) {
                    return redirect("admin/")->withErrors($validator);
                } else {

                    $hash = $request->hash;

                    // dd($hash);

                    $userAuth = UserAuth::where('hash', $hash)->first();

                    if ($userAuth) {
                        // Generate a random 6-digit OTP
                        // $otp = rand(100000, 999999);
                        $otp = 123456;

                        $userAuth->update([
                            'otp' => $otp,
                            'status_expiry' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
                        ]);

                        $user = User::where('user_id', $userAuth->user_id)->first();
                        $phone = $user->mobile;

                        //    dd($phone);
                        //  $result = $this->sendOtp($phone, $otp);

                        return view('admin.pages.auth.verify-otp', [
                            'hash' => $userAuth->hash,
                            'mobile' => $phone,
                            'status_expiry' => $userAuth->status_expiry
                        ])->with('success', 'OTP sent successfully.');
                    } else {
                        return redirect("admin/")->withErrors(['error' => 'Invalid hash.']);
                    }
                }
                return view('admin.pages.auth.mobile-otp')->with('success', 'OTP sent successfully.');
            } else {
                return view('admin.pages.auth.mobile-otp')->with('error', 'Invalid request.');
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
