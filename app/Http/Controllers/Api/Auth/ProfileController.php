<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response(['message' => 'Profile fetch successlly !', 'user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'           => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'email'          => 'nullable|email',
            // 'mobile'         => 'nullable|string|max:15|unique:users,mobile,' . $user->id,
            'at_whatsapp'    => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $user->name           = $request->input('name', $user->name);
        $user->dob            = $request->input('dob', $user->dob);
        $user->gender         = $request->input('gender', $user->gender);
        $user->email          = $request->input('email', $user->email);
        $user->mobile         = $request->input('mobile', $user->mobile);
        $user->at_whatsapp    = $request->input('at_whatsapp', $user->at_whatsapp);
        $user->martial_status = $request->input('martial_status', $user->martial_status);


        // if ($request->hasFile('profile_pic')) {
        //     if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
        //         Storage::disk('public')->delete($user->profile_pic);
        //     }

        //     $path = $request->file('profile_pic')->store('profile_pics', 'public');
        //     $user->profile_pic = $path;
        // }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user
        ], 200);
    }
}
