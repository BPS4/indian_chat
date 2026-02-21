<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;

class WithdrawalController extends Controller
{
    public function withdrawal_list()
    {
        $withdrawals = Withdrawal::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.pages.withdrawal.list', compact('withdrawals'));
    }


    public function add_withdrawal()
    {
        return view('admin.pages.withdrawal.add');
    }

    public function updateStatus(Request $request, $withdrawalId)
    {
        // Logic to update the status of the withdrawal request
        // You can use the $withdrawalId to find the specific withdrawal and update its status
        // Example:
        // $withdrawal = Withdrawal::findOrFail($withdrawalId);
        // $withdrawal->status = $request->input('status');
        // $withdrawal->save();

        return response()->json(['message' => 'Withdrawal status updated successfully']);
    }

    public function show($id)
    {
        // Logic to show details of a specific withdrawal request
        // Example:
        // $withdrawal = Withdrawal::with('user')->findOrFail($id);
        // return view('admin.pages.withdrawal.show', compact('withdrawal'));

        return view('admin.pages.withdrawal.show');
    }



    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Already processed.');
        }

        $withdrawal->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Withdrawal approved successfully.');
    }

    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Already processed.');
        }

        // OPTIONAL: refund wallet amount here if you deducted earlier

        $withdrawal = Withdrawal::where('id', $id)->first();
        $amount = $withdrawal->amount;
        $withdrawal_type = $withdrawal->wallet_type;
        $user = $withdrawal->user;

        





        $user = $withdrawal->user;
        $user->wallet()->increment('balance', $amount);


        $withdrawal->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Withdrawal rejected successfully.');
    }
}
