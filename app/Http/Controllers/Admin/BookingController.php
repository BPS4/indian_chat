<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function bookings_list(Request $request)
    {
        try {
            $page_title = 'Bookings List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Hotel_list',
                    'url' => '',
                ],
            ];

            $perPage = $request->input('per_page', 25);

            $bookings = Booking::with(['payment', 'guests', 'rooms.roomType'])
                ->when($request->search, function ($q, $search) {
                    $q->whereHas('guests', function ($q2) use ($search) {
                        $q2->where('guest_name', 'LIKE', "%{$search}%");
                        // ->orWhere('email', 'LIKE', "%{$search}%")
                    })->orWhereHas('hotel', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    })->orWhere('id', $search);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })
                ->when($request->hotel, function ($q, $hotel) {
                    $q->whereHas('hotel', function ($q2) use ($hotel) {
                        $q2->where('name', $hotel);
                    });
                })
                ->when($request->date, function ($q, $date) {
                    if ($date === 'Today') {
                        $q->whereDate('created_at', now());
                    } elseif ($date === 'This Week') {
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    } elseif ($date === 'This Month') {
                        $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    }
                })
                ->latest('id')
                ->paginate($perPage)
                ->withQueryString();

            $booking_status = Booking::select('status')->distinct()->orderBy('status')
                ->pluck('status');

            $hotels = Hotel::select('name')->distinct()->orderBy('name')
                ->pluck('name');
            // dd($hotels);

            //   dd($bookings);
            return view('admin.pages.bookings.list', compact('page_title', 'page_description', 'breadcrumbs', 'bookings', 'booking_status', 'hotels'));
        } catch (\Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function add_booking()
    {
        try {
            $page_title = 'Add Booking';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Add Booking',
                    'url' => '',
                ],
            ];

            $users = User::orderBy('id', 'desc')->paginate(20);

            $hotels = Hotel::with('location', 'facilitiesNames', 'hotelReview')->get();

            return view('admin.pages.bookings.add', compact('page_title', 'page_description', 'breadcrumbs', 'users', 'hotels'));
        } catch (\Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Validate input
        $request->validate([
            'status' => 'required|in:confirmed,pending,cancelled',
        ]);

        // dd($request->status);
        try {
            $booking->status = $request->status;
            $booking->save();

            return response()->json([
                'success' => true,
                'new_status' => $booking->status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking status update failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not update booking status',
            ], 500);
        }
    }

    public function show($id)
    {
        $booking = Booking::with([
            'user',
            'guests',
            'rooms.roomType',
            'payment',
            'addons',
            'hotel',

        ])->findOrFail($id);

        //  dd($booking);

        return view('admin.pages.bookings.details', compact('booking'));
    }
}
