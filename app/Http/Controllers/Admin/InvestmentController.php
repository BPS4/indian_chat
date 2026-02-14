<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Investment_wallet as Wallet;
use Illuminate\Support\Facades\DB;
use App\Models\Investment_wallet;
use App\Models\RoiGenerator;
use Carbon\Carbon;


class InvestmentController extends Controller
{
    public function investment_list()
    {
        $investments = Investment::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.pages.investment.list', compact('investments'));
    }

    public function add_investment(Request $request)
    {
        try {

            // Check Auth
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'payment_receipt' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 422);
            }

            // File Upload
            $filename = null;

            if ($request->hasFile('payment_receipt')) {
                $file = $request->file('payment_receipt');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/investments'), $filename);
            }

            // Save Investment
            $investment = Investment::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'payment_receipt' => 'uploads/investments/' . $filename,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Investment request submitted successfully',
                'data' => $investment
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }







    public function rejectInvestment($id)
    {
        $investment = Investment::findOrFail($id);

        if ($investment->status !== 'pending') {
            return redirect()->back()->with('error', 'Investment already processed.');
        }

        $investment->update([
            'status' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Investment rejected successfully.');
    }



    public function approveInvestment($id)
    {
        $investment = Investment::findOrFail($id);

        if ($investment->status !== 'pending') {
            return redirect()->back()->with('error', 'Investment already processed.');
        }

        DB::beginTransaction();

        try {

            // Update investment
            $investment->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);


            // Create or update wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $investment->user_id],
                [
                    'investment_balance' => 0,
                    'roi_balance' => 0
                ]
            );

            // dd($wallet);
            // Credit principal amount
            $wallet->increment('investment_balance', $investment->amount);


            DB::commit();

            return redirect()->back()->with('success', 'Investment approved & wallet credited successfully.');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function myWallet()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get wallet
        $wallet = Investment_wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json([
                'status' => true,
                'investment_wallet' => 0,
                'roi_wallet' => 0,
                'today_roi' => 0,
                'total_roi_earned' => 0
            ]);
        }

        // Today's ROI
        $todayROI = RoiGenerator::where('user_id', $user->id)
            ->whereDate('roi_date', Carbon::today())
            ->sum('roi_amount');

        // Total ROI earned
        $totalROI = RoiGenerator::where('user_id', $user->id)
            ->sum('roi_amount');

        return response()->json([
            'status' => true,
            'investment_wallet' => $wallet->investment_balance,
            'roi_wallet' => $wallet->roi_balance,
            'today_roi' => $todayROI,
            'total_roi_earned' => $totalROI
        ]);
    }
}
