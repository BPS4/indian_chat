<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            $page_title = 'Dashboard';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Dashboard',
                    'url' => '',
                ],
            ];
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }
      
            $users = User::whereIn('role_id', [2,12])->get();
            $customers = $users->count();

            $totle_admin = User::where('role_id', 12)->get();
            $admin = $totle_admin->count();

            $messages = Message::get();
            $message = $messages->count();

            $city_users = User::whereNotNull('city')->get();
            $cities = $city_users->pluck('city')->unique();
            $city = $cities->count();

            // dd($customers,$customers,$admin,$message, $city,);   

            return view('admin.pages.dashboard.list', compact('page_title', 'page_description', 'breadcrumbs', 'customers', 'admin', 'message', 'city'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function common_search(Request $request)
    {

        $q = $request->q;

        //   dd($q);

        // Search Hotels
        $hotels = Hotel::where('name', 'LIKE', "%{$q}%")->get();

        if ($hotels->count() > 0) {
            $page_title = 'Hotels List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Hotel_list',
                    'url' => '',
                ],
            ];

            $hotels = Hotel::with([
                'roomTypes',
                'roomTypes.inventories', 'bookings', 'booking_payments',
            ])->where('name', 'LIKE', "%{$q}%")->paginate(10);
            $locations = Hotel::with('location')
                ->get()
                ->pluck('location.city')
                ->unique()
                ->sort()
                ->values();

            return view('admin.pages.hotels.list', compact('page_title', 'page_description', 'breadcrumbs', 'hotels', 'locations'));
        }

        // Search Customers / Guests
        $users = User::where('name', 'LIKE', "%{$q}%")->get();

        if ($users->count() > 0) {
            $page_title = 'Customers List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Customer_list',
                    'url' => '',
                ],
            ];

            $guests = User::where('name', 'LIKE', "%{$q}%")->paginate(10);

            $users = User::with(['bookings', 'booking_payments'])->where('role_id', 2)->where('name', 'LIKE', "%{$q}%")
                ->orderBy('id', 'desc')->paginate();
            $locations = User::whereNotNull('city')
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->pluck('city');

            return view('admin.pages.customers.list', compact('page_title', 'page_description', 'breadcrumbs', 'users', 'locations'));

        }

        // Search Bookings
        $bookings = Booking::where('booking_id', $q)
            ->get();

        if ($bookings->count() > 0) {

            $page_title = 'Bookings List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Hotel_list',
                    'url' => '',
                ],
            ];

            $perPage = $request->input('per_page', 20);
            $bookings = Booking::with(['payment', 'guests', 'rooms.roomType'])
                ->where('booking_id', $q) // same as orderBy('id', 'desc')
                ->paginate($perPage);

            $booking_status = Booking::select('status')->distinct()->orderBy('status')
                ->pluck('status');

            $hotels = Hotel::select('name')->distinct()->orderBy('name')
                ->pluck('name');
            // dd($hotels);

            //   dd($bookings);
            return view('admin.pages.bookings.list', compact('page_title', 'page_description', 'breadcrumbs', 'bookings', 'booking_status', 'hotels'));

        }

        return view('admin.pages.search.list');

    }
}
