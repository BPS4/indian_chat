<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commision;

class commisionController extends Controller
{
    public function refral_commision()
    {

    $commisions = Commision::all(); 
        return view('admin.pages.refral_commision.list', compact('commisions'));
    }

    public function add()
    {

        return view('admin.pages.refral_commision.add');
    }


    public function store(Request $request)
    {
        $request->validate([
            'joining_bonus' => 'required|string|max:255',
            'referral_commision' => 'required|string|max:255',
        ]);

        Commision::create([
            'joining_bonus' => $request->joining_bonus,
            'referral_commision' => $request->referral_commision,
        ]);

        return redirect()->back()->with('success', 'Commission added successfully');
    }
}
