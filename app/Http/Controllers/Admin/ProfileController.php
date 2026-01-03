<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    // 'business_name' => 'required',
                    // 'business_email' => 'required|email',
                    // 'vendor_type' => 'required',
                    // 'vendor_code' => 'required',
                    // 'contact_person_name' => 'required',
                    // 'contact_person_email' => 'required|email',
                    // 'city' => 'required',

                ], [
                    'business_name.required' => 'Enter business name.',
                    'business_email.required' => 'Enter business email.',
                    'business_email.email' => 'Enter valid email.',
                    'vendor_type.required' => 'Enter vendor type.',
                    'vendor_code.required' => 'Enter vendor code.',
                    'contact_person_name.required' => 'Enter contact person name.',
                    'contact_person_email.required' => 'Enter contact person email.',
                    'contact_person_email.email' => 'Enter valid email.',
                    'city.required' => 'Enter city.',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                $array = [
                    'business_name' => $request->business_name,
                    'business_email' => $request->business_email,
                    'vendor_type' => $request->vendor_type,
                    'vendor_code' => $request->vendor_code,
                    'contact_person_name' => $request->contact_person_name,
                    'contact_person_email' => $request->contact_person_email,
                    'city' => $request->city,
                ];

                $update = Auth::user()->update($array);
                return redirect()->back()->with('success', 'Profile updated successfully');
            }

            $page_title = 'Profile';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Profile',
                    'url' => '',
                ]
            ];
            $details = Auth::user();
            return view('admin.pages.profile.profile', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // upload
    public function upload(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'profile_image' => 'required',
                ], [
                    'profile_image.required' => 'Select image.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }
                $array = [];
                if ($request->hasFile('profile_image')) {
                    $image = $request->file('profile_image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    // create a user folder if not exist and store image in it
                    $user = Auth::user();

                    // also remove old image
                    $oldImage = public_path('uploads/profile/' . $user->id . '/' . $user->profile_image);
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                    // unlink(D:\abym projects\avana laravel admin panel\avana_backend\public\uploads/profile/1/): Is a directory



                    $path = public_path('uploads/profile/' . $user->id); // create folder in public/uploads/profile
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $image->move($path, $imageName);
                    $array['profile_image'] = $imageName;
                }
                $update = Auth::user()->update($array);
                return redirect()->back()->with('success', 'Profile image updated successfully');
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
