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
        'joining_bonus' => 'required|numeric|min:0',
        'referral_commision' => 'required|numeric|min:0',
    ]);

    // Check if commission already exists
    $commision = Commision::first();

    if ($commision) {
        // Update existing
        $commision->update([
            'joining_bonus' => $request->joining_bonus,
            'referral_commision' => $request->referral_commision,
        ]);

        return redirect()->back()
            ->with('success', 'Commission updated successfully');
    }

    // Create only if not exists
    Commision::create([
        'joining_bonus' => $request->joining_bonus,
        'referral_commision' => $request->referral_commision,
    ]);

    return redirect()->back()
        ->with('success', 'Commission added successfully');
}


    public function edit($id)
{
    $commision = Commision::findOrFail($id);


    // dd($commision);

    return view('admin.pages.refral_commision.edit', compact('commision'));
}


public function update(Request $request, $id)
{
    $commision = Commision::findOrFail($id);

    $request->validate([
        'joining_bonus' => 'required|numeric|min:0',
        'referral_commision' => 'required|numeric|min:0',
    ]);

    $commision->update([
        'joining_bonus' => $request->joining_bonus,
        'referral_commision' => $request->referral_commision,
    ]);

    return redirect()
        ->route('commision.index')
        ->with('success', 'Referral commission updated successfully');
}


   
}
