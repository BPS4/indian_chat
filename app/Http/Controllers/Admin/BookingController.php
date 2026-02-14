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
            $page_title = 'Withdrawal List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Hotel_list',
                    'url' => '',
                ],
            ];

            $perPage = $request->input('per_page', 25);

           
            // dd($hotels);

            //   dd($bookings);
            return view('admin.pages.withdrawal.list', compact('page_title', 'page_description', 'breadcrumbs', ));
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
