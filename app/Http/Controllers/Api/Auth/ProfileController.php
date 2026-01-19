<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user()->load('wallet', 'sponsor');
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
        $user->country        = $request->input('country', $user->country);
        $user->state          = $request->input('state', $user->state);
        $user->city           = $request->input('city', $user->city);
        $user->country_code = $request->input('country_code', $user->country_code);
        $user->mobile         = $request->input('mobile', $user->mobile);
        $user->at_whatsapp    = $request->input('at_whatsapp', $user->at_whatsapp);
        $user->martial_status = $request->input('martial_status', $user->martial_status);


        // ✅ Profile Picture Upload
       if ($request->hasFile('profile_pic') && $request->file('profile_pic')->isValid()) {

    // Delete old image from PUBLIC folder
    if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
        unlink(public_path($user->profile_pic));
    }

    $file = $request->file('profile_pic');

    // Clean & unique filename
    $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

    // Move to public/profile_pics
    $file->move(public_path('profile_pics'), $filename);

    // Save relative path in DB
    $user->profile_pic = 'profile_pics/' . $filename;
}




        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'mobile'          => $user->mobile,
                'country'         => $user->country,
                'state'           => $user->state,
                'city'            => $user->city,
                'country_code'    => $user->country_code,
                'dob'             => $user->dob,
                'gender'          => $user->gender,
                'martial_status'  => $user->martial_status,
                'at_whatsapp'     => $user->at_whatsapp,
                'profile_pic'     => $user->profile_pic,
                'profile_pic_url' => $user->profile_pic
                    ? asset('storage/' . $user->profile_pic)
                    : null,
            ]
        ], 200);
    }
}
