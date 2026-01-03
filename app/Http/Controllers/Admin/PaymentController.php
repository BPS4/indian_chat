<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingPayment;
use Illuminate\Http\Request;
use PDF;

class PaymentController extends Controller
{
    public function payments_list(Request $request)
    {
        try {
            $page_title = 'Hotels List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Hotel_list',
                    'url' => '',
                ],
            ];

            $search = $request->input('search');

            $search = $request->input('search');
            $status = $request->input('payment_status');
            $method = $request->input('payment_method');
            $dateFilter = $request->input('date_filter');

            $payments = BookingPayment::with(['booking', 'booking.guests'])

                // 🔍 SEARCH FILTER
                ->when($search, function ($query) use ($search) {
                    $query->where('id', $search)                                 // Payment ID
                        ->orWhere('transaction_id', $search)                     // Transaction ID
                        ->orWhereHas('booking.guests', function ($q) use ($search) {
                            $q->where('guest_name', 'LIKE', "%{$search}%");      // Guest Name
                        })
                       ;
                })

                // ✔ PAYMENT STATUS FILTER (0 / 1)
                ->when($status !== null && $status !== '', function ($query) use ($status) {
                    $query->where('payment_status', $status);
                })

                // ✔ PAYMENT METHOD FILTER (Cash / Online)
                ->when($method, function ($query) use ($method) {
                    $query->where('payment_method', $method);
                })

                // ✔ DATE FILTER (today / week / month)
                ->when($dateFilter, function ($query) use ($dateFilter) {

                    if ($dateFilter === 'today') {
                        $query->whereDate('created_at', today());
                    }

                    if ($dateFilter === 'week') {
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                    }

                    if ($dateFilter === 'month') {
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    }
                })

                ->orderBy('id', 'DESC')
                ->paginate(25)
                ->appends(request()->query()); // keep filters

            $booking_status = BookingPayment::select('payment_status')->distinct()->orderBy('payment_status')
                ->pluck('payment_status');

                // dd($booking_status);

            $payment_method = BookingPayment::select('payment_method')->distinct()->orderBy('payment_method')
                ->pluck('payment_method');

            // dd($booking_status, $payment_method);
            $totalPayments = $this->totalPayment();
            $successfulPayments = $this->successfulPayment();
            $failedPayments = $this->failedPayment();
            $refundPayments = $this->refundPayment();

            return view('admin.pages.payments.list', compact('page_title', 'page_description', 'breadcrumbs', 'payments', 'totalPayments', 'successfulPayments', 'failedPayments', 'refundPayments', 'booking_status', 'payment_method'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function totalPayment()
    {
        $currentMonth = BookingPayment::where('payment_status', '1')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $previousMonth = BookingPayment::where('payment_status', '1')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        $growth = $previousMonth > 0
          ? (($currentMonth - $previousMonth) / $previousMonth) * 100
          : 100;

        return [
            'amount' => $currentMonth,
            'growth' => round($growth, 2),
        ];
    }

    private function successfulPayment()
    {
        $totalTransactions = BookingPayment::count();

        $successful = BookingPayment::where('payment_status', '1')->count();

        $percent = $totalTransactions > 0
          ? ($successful / $totalTransactions) * 100
          : 0;

        return [
            'percentage' => round($percent, 2),
            'success_count' => $successful,
            'total_transactions' => $totalTransactions,
        ];
    }

    private function failedPayment()
    {
        $failedCount = BookingPayment::where('payment_status', '0')->count();

        $failedValue = BookingPayment::where('payment_status', '0')->sum('amount');

        return [
            'failed_count' => $failedCount,
            'failed_value' => $failedValue,
        ];
    }

    private function refundPayment()
    {
        $refundAmount = BookingPayment::where('payment_status', '2')
            ->sum('amount');

        $refundThisMonth = BookingPayment::where('payment_status', '2')
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'refund_amount' => $refundAmount,
            'refund_this_month' => $refundThisMonth,
        ];
    }

    public function payment_receipt_download($id)
    {
        $payment = BookingPayment::findOrFail($id);

        $pdf = PDF::loadView('admin.pages.payments.receipt', compact('payment'));

        return $pdf->download('payment_receipt_'.$payment->transaction_id.'.pdf');
    }
}
