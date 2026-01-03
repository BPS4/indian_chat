<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index(Request $request)
    {

        try {
            if ($request->isMethod('post')) {
                // dd($request->all());

            }
            $page_title = 'Admin Settings';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Settings',
                    'url' => '',
                ]
            ];


            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }
            return view('admin.pages.settings.settings', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
