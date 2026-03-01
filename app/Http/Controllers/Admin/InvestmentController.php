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
use App\Models\Wallet as RefralWallet;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;




class InvestmentController extends Controller
{
    public function investment_list()
    {
        $investments = Investment::with('user')->orderBy('created_at', 'desc')->paginate(50);
        // dd($investments);
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
                'payment_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:20048',
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
          $refral_wallet = RefralWallet::where('user_id', $user->id)
        ->get();

        if (!$wallet) {
            return response()->json([
                'status' => true,
                'investment_wallet' => 0,
                'refral_wallet' => $refral_wallet->sum('balance'),
                'today_roi' => 0,
                'total_amount' => 0
            ]);
        }

        // Today's ROI
        $todayROI = (string) RoiGenerator::where('user_id', $user->id)
            ->whereDate('roi_date', Carbon::today())
            ->sum('roi_amount');
        // dd($todayROI);

        // Total ROI earned
        $totalROI = RoiGenerator::where('user_id', $user->id)
            ->sum('roi_amount');

     
        // dd('test');



        return response()->json([
            'status' => true,
            'investment_wallet' => $wallet->investment_balance,
            'refral_wallet' => $refral_wallet->sum('balance'),
            'roi_wallet' => $wallet->roi_balance,
            'today_roi' => $todayROI,
            'total_amount' => $wallet->investment_balance + $wallet->roi_balance,
            'test' => 'test'
        ]);
    }

    public function withdrawRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'wallet_type' => 'required|in:investment,roi,refral',
            'withdraw_amount' => 'required|numeric|min:100'
        ], [
            'withdraw_amount.min' => 'Minimum withdrawal amount is ₹100.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }


        $wallet_type     = $request->wallet_type; // investment | roi | refral
        $withdraw_amount = (float) $request->withdraw_amount;
        $user            = Auth::user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if ($withdraw_amount <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid withdrawal amount'
            ], 400);
        }

        try {

            DB::transaction(function () use ($user, $wallet_type, $withdraw_amount, &$wallet) {

                // Lock wallet row to prevent double withdrawal
                if ($wallet_type == 'investment') {

                    $wallet = Investment_wallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    // if ($withdraw_amount > $wallet->investment_balance) {
                    //     throw new \Exception('Insufficient investment balance');
                    // }

                    // $wallet->decrement('investment_balance', $withdraw_amount);
                    throw new \Exception('Investment wallet withdrawal is currently not allowed');
                } elseif ($wallet_type == 'roi') {

                    $wallet = Investment_wallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    // if ($withdraw_amount > $wallet->roi_balance) {
                    //     throw new \Exception('Insufficient ROI balance');
                    // }

                    // $wallet->decrement('roi_balance', $withdraw_amount);
                    throw new \Exception('ROI wallet withdrawal is currently not allowed');
                } elseif ($wallet_type == 'refral') {

                    // dd($user->id);
                    $wallet = RefralWallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($withdraw_amount > $wallet->balance) {
                        throw new \Exception('Insufficient referral balance');
                    }

                    $wallet->decrement('balance', $withdraw_amount);
                } else {
                    throw new \Exception('Invalid wallet type');
                }

                // Create withdrawal record
                Withdrawal::create([
                    'user_id'    => $user->id,
                    'amount'     => $withdraw_amount,
                    'wallet_type' => $wallet_type,
                    'status'     => 'pending'
                ]);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Withdrawal request submitted successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function withdrawList()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $withdrawals
        ]);
    }
}
