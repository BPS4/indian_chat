<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;

class ContactController extends Controller
{
    public function list()
    {

    $contacts = Contact::all();
        return view('admin.pages.contact_details.list', compact('contacts'));
    }   


    public function add(Request $request)
    {
        // We allow ONLY ONE contact row
        $contacts = Contact::first();

        // 👉 POST request (Add / Update)
        if ($request->isMethod('post')) {

            $request->validate([
                'Primary'   => 'required|string|max:15',
                'Secondary' => 'nullable|string|max:15',
            ]);

            if ($contacts) {
                // Update existing
                $contacts->update([
                    'primary'   => $request->Primary,
                    'secondary' => $request->Secondary,
                ]);

                $message = 'Contact numbers updated successfully';
            } else {
                // Create new
                Contact::create([
                    'primary'   => $request->Primary,
                    'secondary' => $request->Secondary,
                ]);

                $message = 'Contact numbers added successfully';
            }

            return redirect()
                ->route('contacts.index')
                ->with('success', $message);
        }

        // dd($contacts);
        // 👉 GET request (View form)
        return view('admin.pages.contact_details.add', compact('contacts'));
    }

    public function contact_details(Request $request)
    {
        $contacts = Contact::first();

        if ($contacts) {
            return response()->json([
                'status' => 'success',
                'data' => $contacts
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact details not found'
            ], 404);
        }
    }


    public function total_users(Request $request)
    {
        $contactsCount = User::where('role_id', 2 )->count();

        return response()->json([
            'status' => 'success',
            'total_users' => $contactsCount
        ], 200);
    }
}
