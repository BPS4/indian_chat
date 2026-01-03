<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\ProfileController;

class AuthController extends Controller
{
    public function  showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string',
        ]);
        $checkUser = User::where('email', $request->email)->first();
        if ($checkUser->status == 1) {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $id = $user->id;
                $email = $user->email;
                $name = $user->name;
                $role = $user->role_id;
                $systemRoles = getSystemRoles($role)->toArray();

                if (count($systemRoles)) {
                    $systemRoles = json_decode($systemRoles[0]['permission'], 1);
                }

                Session::put(['id' => $id, 'name' => $name, 'email' => $email, 'role' => $role, 'access_name' => 'admin', 'system_roles' => $systemRoles, 'financialYear' => date('Y') . '-' . (date('Y') + 1)]);
                return redirect(url('admin/dashboard'));
            }

            return redirect()->back()->with(['error' => 'Invalid credentials.']);
        } else {
            return redirect()->back()->with(['error' => 'Your Account is suspended . Please contact to Admin']);
        }
    }

    public function showRegisterForm()
    {
        $roles = Role::where('status', 1)
            ->orderBy('id', 'asc')
            ->get()
            ->slice(1);

        return view('admin.auth.register', compact('roles'));
    }

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|alpha|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $request->role ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.login.form')->with('success', 'Registration successful. Please login.');
    }





public function changePassword(Request $request)
{
    try {

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        // Check old password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Current password is incorrect');
        }

        // Update new password
        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully.');

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

}
