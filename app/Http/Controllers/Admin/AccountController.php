<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class AccountController extends Controller
{


   public function updateBankAccount(Request $request)
{
    $userId = auth()->id();

    // ===============================
    // POST → Create or Update
    // ===============================
    if ($request->isMethod('post')) {

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            // max is in kilobytes; 5120 KB = 5 MB
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $bankAccount = BankAccount::firstOrNew([
            'user_id' => $userId
        ]);

        // dd($bankAccount);

        $bankAccount->bank_name = $request->bank_name;
        $bankAccount->account_number = $request->account_number;
        $bankAccount->ifsc_code = $request->ifsc_code;
        $bankAccount->account_holder_name = $request->account_holder_name;

        // ===============================
        // Handle File Upload
        // ===============================
        if ($request->hasFile('document')) {

            // Delete old file
            if ($bankAccount->document &&
                file_exists(public_path($bankAccount->document))) {

                unlink(public_path($bankAccount->document));
            }

            // dd($request->file('document'));
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('images/documents');

            // Create directory if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            $bankAccount->document = 'images/documents/' . $filename;
        }

        $bankAccount->save();

        return response()->json([
            'success' => true,
            'message' => 'Bank account saved successfully',
            'data' => $bankAccount
        ]);
    }

    // ===============================
    // GET → Fetch Data
    // ===============================
    $bankAccount = BankAccount::where('user_id', $userId)->first();

    if (!$bankAccount) {
        return response()->json([
            'success' => false,
            'message' => 'Bank account not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $bankAccount
    ]);
}
}
