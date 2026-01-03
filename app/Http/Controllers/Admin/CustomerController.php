<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customer_list(Request $request)
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

            $users = User::with(['bookings', 'booking_payments'])
                ->where('role_id', 2) // only customers
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                })
                ->when($request->status, function ($query, $status) {
                    if ($status === 'Active') {
                        $query->where('status', 1);
                    } elseif ($status === 'Inactive') {
                        $query->where('status', 0);
                    }
                })

                ->when($request->location, function ($query, $location) {
                    $query->where('city', $location);
                })
                ->orderBy('id', 'desc')
                ->paginate(25)
                ->withQueryString(); // preserves filters in pagination links

            $locations = User::whereNotNull('city')
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->pluck('city');

            return view('admin.pages.customers.list', compact('page_title', 'page_description', 'breadcrumbs', 'users', 'locations'));
        } catch (\Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function customer_view($id)
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

            // $users = User::query()
            //     ->select(
            //         'users.id as user_id',
            //         'users.name',
            //         'users.email',
            //         'users.mobile',
            //         'users.status',
            //      'users.created_at',

            //         // Booking details
            //         'booking.booking_id as booking_id',
            //         'booking.checkin_date',
            //         'booking.checkout_date',
            //         'booking.status as booking_status',

            //         // Payment details
            //         'booking_payments.amount as payment_amount',
            //         'booking_payments.transaction_id as transaction_id',
            //         'booking_payments.payment_status as payment_status',

            //         // Room details
            //         'booking_rooms.room_type_id',
            //         'room_types.room_name as room_type_name'
            //     )
            //     ->leftJoin('booking', 'booking.user_id', '=', 'users.id')
            //     ->leftJoin('booking_payments', 'booking_payments.booking_id', '=', 'booking.booking_id')
            //     ->leftJoin('booking_rooms', 'booking_rooms.booking_id', '=', 'booking.id')
            //     ->leftJoin('room_types', 'room_types.id', '=', 'booking_rooms.room_type_id')
            //     ->where('users.id', $id)
            //     ->orderByDesc('users.id')
            //     ->paginate(50);

            $user = User::with([
                'bookings',
                'bookings.payment',
                'bookings.rooms.roomType',
            ])
                ->findOrFail($id);

            // dd($user);

            return view('admin.pages.customers.view', compact('page_title', 'page_description', 'breadcrumbs', 'user'));
        } catch (\Exception $e) {
            dd($e);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_customer(Request $request)
    {
        try {

            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|unique:users,mobile',
            ], [
                'name.required' => 'User name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Enter a valid email address.',
                'email.unique' => 'This email is already taken.',
                'mobile.required' => 'Mobile number is required.',
                'mobile.unique' => 'This mobile number is already in use.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            // Prepare data
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'city' => $request->city,
                'role_id' => 2,
                'status' => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ];

            // Create user
            $user = User::create($data);

            DB::commit();

            // JSON response with ID
            return response()->json([
                'status' => true,
                'message' => 'User added successfully.',
                'user' => $user,

            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
