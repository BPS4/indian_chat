<?php

// namespace App\Helper;

use App\Models\BookingRoom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// class Helper
// {

function runTimeChecked($myId, $matchId)
{
    if ($myId == $matchId)
        return 'checked';
}

function getSystemRoles($role = null)
{
    $data = 'App\Models\Role'::when($role, function ($data) use ($role) {
        if ($role) {
            $data->where('id', '=',  $role);
        }
    })->get();
    return $data;
}

function runTimeSelection($myId, $matchId)
{
    if ($myId == $matchId)
        return 'selected';
}


function modulesList()
{
    return [
        [
            'id' => 1,
            'slug' => 'dashboard',
            'module_name' => 'Dashboard',
        ],
        [
            'id' => 2,
            'slug' => 'hotels',
            'module_name' => 'hotels',
        ],
        [
            'id' => 3,
            'slug' => 'customers',
            'module_name' => 'customers',
        ],
        [
            'id' => 4,
            'slug' => 'bookings',
            'module_name' => 'bookings',
        ],
        [
            'id' => 5,
            'slug' => 'Offers',
            'module_name' => 'Offers',
        ],
        [
            'id' => 6,
            'slug' => 'Payments',
            'module_name' => 'Payments',
        ],
        [
            'id' => 7,
            'slug' => 'location',
            'module_name' => 'Location',
        ],
        [
            'id' => 8,
            'slug' => 'locality',
            'module_name' => 'Locality',
        ],
        [
            'id' => 9,
            'slug' => 'addons',
            'module_name' => 'Addons',
        ],
        [
            'id' => 9,
            'slug' => 'facility-group',
            'module_name' => 'Facility Group',
        ],
        [
            'id' => 10,
            'slug' => 'facility',
            'module_name' => 'Facility',
        ],
        [
            'id' => 11,
            'slug' => 'coupons',
            'module_name' => 'Coupon',
        ],
        [
            'id' => 12,
            'slug' => 'profile',
            'module_name' => 'profile',
        ],
        [
            'id' => 13,
            'slug' => 'gift-card',
            'module_name' => 'Gift Card',
        ],
        [
            'id' => 14,
            'slug' => 'reviews',
            'module_name' => 'Reviews',
        ],
        [
            'id' => 15,
            'slug' => 'slider',
            'module_name' => 'Slider',
        ],
        [
            'id' => 16,
            'slug' => 'term',
            'module_name' => 'Term',
        ],
        [
            'id' => 16,
            'slug' => 'guest-photo',
            'module_name' => 'Guest Photo',
        ],

        [
            'id' => 17,
            'slug' => 'search',
            'module_name' => 'search',
        ],

        [
            'id' => 23,
            'slug' => 'admin-users',
            'module_name' => 'Admin Users',
        ],
        [
            'id' => 24,
            'slug' => 'role',
            'module_name' => 'Roles',
        ],
        [
            'id' => 25,
            'slug' => 'user',
            'module_name' => 'Users',
        ],
        [
            'id' => 26,
            'slug' => 'settings',
            'module_name' => 'Settings',
        ],


    ];
}


function SidebarModules()
{
    $data = 'App\Models\Module'::where('status', 1)
        ->where('is_show_in_menu', 1)
        ->orderBy('sort_order', 'asc')
        ->get();

    return $data;
}

function conditionalStatus($status)
{
    if ($status == '1') {
        $status = 1;
    }
    if ($status == '2') {
        $status = 0;
    }
    return $status;
}

function DivisionList()
{

    $data = 'App\Models\Division'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}


function ItemGroupList()
{
    $data = 'App\Models\ItemGroup'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}


function ItemCategoryList()
{
    $data = 'App\Models\ItemCategory'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}

function ItemList()
{
    $data = 'App\Models\Item'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}
// product
function ProductList()
{
    $data = 'App\Models\Product'::with('inventory')->where('status', 1)
        ->whereHas('inventory', function ($query) {
            // $query->where('qty', '>', 0);
        })->orderBy('id', 'asc')->get();

    return $data;
}

function DistributorsList()
{
    $data = 'App\Models\User'::where('role_id', 3)->where('status', 1)->orderBy('id', 'asc')->get();

    return $data;
}

function DoctorsList()
{
    $data = 'App\Models\User'::where('role_id', 16)->where('status', 1)->orderBy('id', 'asc')->get();

    return $data;
}

function HospitalList()
{
    $data = 'App\Models\User'::where('role_id', 17)->where('status', 1)->orderBy('id', 'asc')->get();

    return $data;
}




function ProductFinderList()
{

    $data = 'App\Models\product_finder'::orderBy('id', 'asc')->get();

    return $data;
}


function TerritoryList()
{
    $data = 'App\Models\Territory'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}
function RegionList()
{
    $data = 'App\Models\Region'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}

function getGeneralManager()
{
    $data = 'App\Models\User'::where('status', 1)->where('post', 1)->orderBy('id', 'desc')->get();

    return $data;
}
function getRegionalManager()
{
    $data = 'App\Models\User'::where('status', 1)->where('post', 2)->orderBy('id', 'desc')->get();

    return $data;
}

function getDesignation($id = null)
{
    $data = 'App\Models\Designation'::when($id, function ($data) use ($id) {
        if ($id) {
            $data->where('parent_id', '=',  $id);
        }
    })->where('status', 1)->orderBy('id', 'desc')->first();

    return $data;
}
function getAllStates()
{
    return 'App\Models\State'::all();
}

function CityList()
{
    $data = 'App\Models\City'::where('status', 1)->orderBy('id', 'desc')->get();

    return $data;
}

function getDivisionName($id)
{
    $data = 'App\Models\Division'::where('id', $id)->pluck('name')->first();
    return $data;
}

function getTerritoryName($id)
{
    $data = 'App\Models\Territory'::where('id', $id)->pluck('name')->first();
    return $data;
}

function getCityName($id)
{
    $data = 'App\Models\City'::where('id', $id)->pluck('name')->first();
    return $data;
}

function getGeneralManagersList($id)
{
    $data = 'App\Models\User'::where('general_manager_id', $id)->get();
    return $data;
}

function getIndividuals($id, $stateId = null)
{
    $state = 'App\Models\State'::where('id', $stateId)->first();
    if ($state) {
        $city = 'App\Models\City'::where('state_id', $state->id)->pluck('id')->toArray();
    } else {
        $city = [];
    }

    $data = 'App\Models\User'::where('regional_manager_id', $id)
        ->where('user_type', 0)
        ->where('post', 3)
        // ->when(!empty($city), function ($query) use ($city) {
        //     $query->whereIn('city_id', $city);
        // })
        ->when(!empty($city), function ($query) use ($city) {
            $query->where(function ($subQuery) use ($city) {
                foreach ($city as $cityId) {
                    $subQuery->orWhereRaw("FIND_IN_SET(?, city_id)", [$cityId]);
                }
            });
        })
        ->get();

    return $data;
}


// function getIndividuals($id,$stateId)
// {
//     $state = 'App\Models\State'::where('id', $stateId)->first();
//     $city = 'App\Models\City'::where('state_id', $state->id)->pluck('id')->toArray();
//     $city = implode(',', $city);

//     $data = 'App\Models\User'::where('regional_manager_id', $id)
//     ->where('user_type', 0)->where('city',$city)
//     ->where('post', 3)->get();

//     dd($data);
//     return $data;
// }

function postList()
{
    $post = collect([
        [
            'id' => 1,
            'name' => 'Assistant General Manager',
        ],
        [
            'id' => 2,
            'name' => 'Business Manager',
        ],
        [
            'id' => 3,
            'name' => 'Territory/Area Sales Manager',
        ],
        [
            'id' => 4,
            'name' => 'Admin',
        ],
        [
            'id' => 5,
            'name' => 'National',
        ],
        [
            'id' => 16,
            'name' => 'Hospital',
        ],
        [
            'id' => 18,
            'name' => 'Doctor',
        ],
    ]);

    return $post;
}


function BudgetListByUser($id, $state_id = null, $city_id = null)
{
    // dd($id ,$state_id,$city_id);
    $data = 'App\Models\Budget'::where('created_by', $id)
        ->when($state_id, function ($query) use ($state_id) {
            $query->where('state_id', $state_id);
        })
        ->when($city_id, function ($query) use ($city_id) {
            $query->where('city_id', $city_id);
        })
        ->where('year', session('financialYear'))->first();
    return $data;
}
function getDivisionByUser($id)
{
    $divisionIds = explode(',', $id);
    $data = 'App\Models\Division'::whereIn('id', $divisionIds)->get();
    return $data;
}
// city
function getCityByUser($id)
{
    $cityIds = explode(',', $id);
    $data = 'App\Models\City'::whereIn('id', $cityIds)->get();
    return $data;
}

function getRole()
{
    $role_id = Auth::user()->role_id;
    $data = 'App\Models\Role'::where('id', $role_id)->first();
    $role = $data->role;
    return $role;
}


function getRoleById($id)
{
    $data = 'App\Models\Role'::where('id', $id)->first();
    // dd($data);
    return $data;
}

function OrderNameById($id)
{
    $data = 'App\Models\Order'::where('id', $id)->pluck('booking_id')->first();
    return $data;
}

function ConsignmentNameById($id)
{
    $data = 'App\Models\AvanaConsignment'::where('id', $id)->pluck('consignment_id')->first();
    // dd($data);
    return $data;
}

function ConsignmentDateById($id)
{
    $data = 'App\Models\AvanaConsignment'::where('id', $id)->pluck('created_at')->first();
    // dd($data);
    return $data;
}


function OrderStatusList($id = null)
{
    $data = collect([
        [
            'id' => 1,
            'name' => 'Order Booked',
        ],
        [
            'id' => 2,
            'name' => 'Preparing for Shipment',
        ],
        [
            'id' => 3,
            'name' => 'Shipped',
        ],
        [
            'id' => 4,
            'name' => 'Out for Delivery',
        ],
        [
            'id' => 5,
            'name' => 'Delivered',
        ],
    ]);

    if ($id) {
        $status = $data->firstWhere('id', $id);
        return $status ? $status['name'] : null;
    }

    return $data;
}


function consignment_return()
{
    $data = collect([
        [
            'id' => 1,
            'name' => 'Pending',
            'name' => 'Return initiated ',
        ],
        [
            'id' => 2,
            'name' => 'Approved',
            'name' => 'Preparing for Shipment',
        ],
        [
            'id' => 3,
            'name' => 'Rejected',
            'name' => 'Shipped',
        ],
        [
            'id' => 4,
            'name' => 'Delivered',
            'name' => 'Out for Delivery',
        ],
        [
            'id' => 5,
            'name' => 'Cancelled',
            'name' => 'Delivered',
        ],
    ]);

    return $data;
}



function updateBudgetPhasing($budget)
{
    // Extract and convert division IDs to integers
    $divisionIds = explode(',', $budget->division);

    if ($divisionIds) {
        $divisionIds = array_map(function ($divisionId) {
            return (int) $divisionId;
        }, $divisionIds);
    } else {
        $divisionIds = [];
    }

    // Build the phasing array
    $phasing = array_map(function ($divisionId) use ($budget) {
        return [
            'divisionId' => $divisionId,
            'bid' => $budget->id,
            'phasing' => [
                'q1' => 0,
                'q2' => 0,
                'q3' => 0,
                'q4' => 0,
            ],
        ];
    }, $divisionIds);

    // Update the budget's phasing field
    $budget->update(['phashing' => json_encode($phasing)]);
}

function getBudgetById($id)
{
    $data = 'App\Models\Budget'::where('id', $id)->first();
    return $data;
}

function getDivisionIdsByUser($id)
{
    $data = 'App\Models\User'::where('id', $id)->pluck('division_id')->first();
    $data = explode(',', $data);
    return $data;
}
function getRegionIdByUser($id)
{
    $data = 'App\Models\User'::where('id', $id)->pluck('territory_id')->first();
    return $data;
}

function BudgetStatusByLoginUser($budgetId, $userId)
{
    $data = 'App\Models\BudgetStatus'::where('bid', $budgetId)->where('user_id', $userId)->pluck('status')->first();
    return $data;
}

function getDistributorList()
{
    $data = 'App\Models\User'::where('user_type', 1)->orderBy('id', 'desc')->get();
    return $data;
}
function getHospitalList()
{
    $data = 'App\Models\Hospital'::where('status', 1)->orderBy('id', 'desc')->get();
    return $data;
}
function getDoctorList()
{
    $data = 'App\Models\Doctor'::where('status', 1)->orderBy('id', 'desc')->get();
    return $data;
}

function getCurrency($amount)
{
    $data = "₹ " . number_format($amount, 2);
    return $data;
}

function getTotalOrdersCount()
{
    $role = auth()->user()->role->role;

    $id = auth()->user()->id;


    if ($role == 'Distributor' || $role == 'Doctor' || $role == 'Hospital') {
        // dd($id);
        $data = \App\Models\Order::where('created_by', $id)->count();
        return $data;
    } else {

        $data = 'App\Models\Order'::count();
        return $data;
    }
}

function getRegularBookingCount()
{

    $role = auth()->user()->role->role;
    $id = auth()->user()->id;


    if ($role == 'Distributor' || $role == 'Doctor' || $role == 'Hospital') {
        // dd($id);
        $data = \App\Models\Order::where('order_type', 0)->where('created_by', $id)->count();
        return $data;
    } else {

        $data = 'App\Models\Order'::where('order_type', 0)->count();
        return $data;
    }
}

function getSpecialPriceRequestCount()
{

    $role = auth()->user()->role->role;
    $id = auth()->user()->id;


    if ($role == 'Distributor' || $role == 'Doctor' || $role == 'Hospital') {
        // dd($id);


        $data = 'App\Models\Order'::where('order_type', 1)->where('created_by', $id)->count();
        return $data;
    } else {

        $data = 'App\Models\Order'::where('order_type', 1)->count();
        return $data;
    }
}

function getTotalBacklogOrdersCount()
{

    $role = auth()->user()->role->role;
    $id = auth()->user()->id;


    if ($role == 'Distributor' || $role == 'Doctor' || $role == 'Hospital') {
        // dd($id);

        $data = 'App\Models\Order'::where('is_backlog', 3)->where('created_by', $id)->count();
        return $data;
    } else {

        $data = 'App\Models\Order'::where('is_backlog', 3)->count();
        return $data;
    }


    $data = 'App\Models\Order'::where('is_backlog', 3)->count();
    return $data;
}


function getTotalSalesCount($division = null)
{
    $query = \App\Models\OrderItem::join('products', 'products.id', '=', 'order_items.product_id');

    if (!is_null($division)) {
        $query->where('products.division_id', $division);
    }

    return $query->sum('order_items.total');
}



function sendMail($to, $subject, $message)
{
    $headers = "From: " . env('MAIL_FROM_NAME') . " <" . env('MAIL_FROM_ADDRESS') . ">\r\n";
    $headers .= "Reply-To: " . env('MAIL_FROM_ADDRESS') . "\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    mail($to, $subject, $message, $headers);
}

function getRegionName($id)
{
    $data = 'App\Models\Region'::where('id', $id)->pluck('name')->first();
    return $data;
}

function getRegionalManagerName($id)
{
    $data = 'App\Models\User'::where('id', $id)->pluck('name')->first();
    return $data;
}

function getUserName($id)
{
    $data = \App\Models\User::where('id', $id)->pluck('name')->first();
    return $data; // $data is already the name string

}

function getGeneralManagerName($id)
{
    $data = 'App\Models\User'::where('id', $id)->pluck('name')->first();
    return $data->name;
}

function getGeneralManagerByRegionalManager($id)
{
    $regionalManager = 'App\Models\User'::find($id);
    if ($regionalManager) {
        $generalManager = 'App\Models\User'::where('id', $regionalManager->general_manager_id)->pluck('name')->first();
        return $generalManager;
    }
    return null;
}

function getDistributorsListByUser($id, $stateId = null)
{
    $state = 'App\Models\State'::where('id', $stateId)->first();
    if ($state) {
        $city = 'App\Models\City'::where('state_id', $state->id)->pluck('id')->toArray();
    } else {
        $city = [];
    }
    // dump($city);
    $data = 'App\Models\User'::where('id', $id)->pluck('distributor_id')->first();
    $data = explode(',', $data);
    $data = 'App\Models\User'::whereIn('id', $data)
        ->when(!empty($city), function ($query) use ($city) {
            $query->where(function ($subQuery) use ($city) {
                foreach ($city as $cityId) {
                    $subQuery->orWhereRaw("FIND_IN_SET(?, city_id)", [$cityId]);
                }
            });
        })
        ->get();
    // dd($data);

    return $data;
}
// function getDistributorsListByUser($id)
// {
//     $data = 'App\Models\User'::where('id', $id)->pluck('distributor_id')->first();
//     $data = explode(',', $data);
//     $data = 'App\Models\User'::whereIn('id', $data)->get();
//     return $data;
// }
function getUserById($id)
{
    $data = 'App\Models\User'::where('id', $id)->first();
    return $data;
}
function getStateListByCity($id)
{
    $cityIds = explode(',', $id);
    $data = 'App\Models\City'::whereIn('id', $cityIds)->get();
    $stateIds = [];
    foreach ($data as $city) {
        $stateIds[] = $city->state_id;
    }
    $stateIds = array_unique($stateIds);
    $stateIds = array_map('intval', $stateIds);
    $data = 'App\Models\State'::whereIn('id', $stateIds)->get();
    return $data;
}

function getCityListByState($city_id, $state_id)
{
    $cityIds = explode(',', $city_id);
    $data = 'App\Models\City'::where('state_id', $state_id)->whereIn('id', $cityIds)->get();
    return $data;
}

// getIndividualsByCity
function getIndividualsByCity($id, $cityId)
{

    $data = 'App\Models\User'::where('regional_manager_id', $id)
        ->where('user_type', 0)
        ->where('post', 3)
        ->when($cityId, function ($query) use ($cityId) {
            $query->whereRaw("FIND_IN_SET(?, city_id)", [$cityId]);
        })
        ->get();

    return $data;
}

function TerritoryManagerList()
{
    $data = 'App\Models\User'::where('status', 1)->where('post', 3)->orderBy('id', 'desc')->get();
    return $data;
}
function ApplicationSpecialistList()
{
    $data = 'App\Models\User'::where('status', 1)->where('role_id', 16)->orderBy('id', 'desc')->get();
    return $data;
}


// secondary sales
function CurrentYearSecondarySales($userId, $item_id)
{
    $users = getDistributorsUsers($userId);
    $data = 'App\Models\DistributorSalesProduct'::groupBy('item_id')
        ->selectRaw('sum(total_price) as total_price')
        ->where('item_id', $item_id)
        ->whereIn('distributor_sales_id', function ($query) {
            $query->select('id')
                ->from('distributor_sales')
                ->where('fy_year', date('Y'));
        })
        ->whereIn('distributor_sales_id', function ($query) use ($users) {
            $query->select('id')
                ->from('distributor_sales')
                ->whereIn('distributor_id', $users);
        })
        ->first();

    $totalPrice = $data ? $data->total_price : 0;

    return $totalPrice;
}

function LastYearSecondarySales($userId, $item_id)
{
    $users = getDistributorsUsers($userId);
    $data = 'App\Models\DistributorSalesProduct'::groupBy('item_id')
        ->selectRaw('sum(total_price) as total_price')
        ->where('item_id', $item_id)
        ->whereIn('distributor_sales_id', function ($query) {
            $query->select('id')
                ->from('distributor_sales')
                ->where('fy_year', date('Y') - 1);
        })
        ->whereIn('distributor_sales_id', function ($query) use ($users) {
            $query->select('id')
                ->from('distributor_sales')
                ->whereIn('distributor_id', $users);
        })
        ->first();

    $totalPrice = $data ? $data->total_price : 0;

    return $totalPrice;
}

// FY26 Current Month Sales (Secondary)
function CurrentMonthCurrentYearSecondarySales($userId, $item_id)
{
    $users = getDistributorsUsers($userId);
    $data = 'App\Models\DistributorSalesProduct'::groupBy('item_id')
        ->selectRaw('sum(total_price) as total_price')
        ->where('item_id', $item_id)
        ->whereIn('distributor_sales_id', function ($query) {
            $query->select('id')
                ->from('distributor_sales')
                ->where('fy_year', date('Y'))
                ->whereMonth('created_at', date('m'));
        })
        ->whereIn('distributor_sales_id', function ($query) use ($users) {
            $query->select('id')
                ->from('distributor_sales')
                ->whereIn('distributor_id', $users);
        })

        ->first();

    $totalPrice = $data ? $data->total_price : 0;

    return $totalPrice;
}

// FY25 Current Month Sales (Secondary)
function CurrentMonthLastYearSecondarySales($userId, $item_id)
{
    $users = getDistributorsUsers($userId);
    $data = 'App\Models\DistributorSalesProduct'::groupBy('item_id')
        ->selectRaw('sum(total_price) as total_price')
        ->where('item_id', $item_id)
        ->whereIn('distributor_sales_id', function ($query) {
            $query->select('id')
                ->from('distributor_sales')
                ->where('fy_year', date('Y') - 1)
                ->whereMonth('created_at', date('m'));
        })
        ->whereIn('distributor_sales_id', function ($query) use ($users) {
            $query->select('id')
                ->from('distributor_sales')
                ->whereIn('distributor_id', $users);
        })
        ->first();

    $totalPrice = $data ? $data->total_price : 0;

    return $totalPrice;
}


// primary sales

function CurrentYearPrimarySales($userId, $item_id)
{
    $data = 'App\Models\PrimarySales'::groupBy('item_id')
        ->selectRaw('sum(total) as total')
        ->where('item_id', $item_id)
        ->where('date', date('Y'))
        ->first();

    $totalPrice = $data ? $data->total : 0;

    return $totalPrice;
}

function LastYearPrimarySales($userId, $item_id)
{
    $data = 'App\Models\PrimarySales'::groupBy('item_id')
        ->selectRaw('sum(total) as total')
        ->where('item_id', $item_id)
        ->where('date', date('Y') - 1)
        ->first();

    $totalPrice = $data ? $data->total : 0;

    return $totalPrice;
}

function CurrentMonthCurrentYearPrimarySales($userId, $item_id)
{
    $data = 'App\Models\PrimarySales'::groupBy('item_id')
        ->selectRaw('sum(total) as total')
        ->where('item_id', $item_id)
        ->where('date', '>=', date('Y-m-01'))
        ->first();

    $totalPrice = $data ? $data->total : 0;

    return $totalPrice;
}

function CurrentMonthLastYearPrimarySales($userId, $item_id)
{
    $data = 'App\Models\PrimarySales'::groupBy('item_id')
        ->selectRaw('sum(total) as total')
        ->where('item_id', $item_id)
        ->where('date', '>=', date('Y-m-01', strtotime('-1 year')))
        ->first();

    $totalPrice = $data ? $data->total : 0;

    return $totalPrice;
}


function getDistributorsUsers($user_id)
{

    $user = User::where('id', $user_id)->first();

    $users = [];

    if ($user) {
        switch ($user->post) {
            case 1:
                // general manager login
                // dd("general manager login");
                $users = User::where('general_manager_id', $user->id)->pluck('id');
                $users = User::whereIn('regional_manager_id', $users)->pluck('id');
                $users = User::whereIn('id', $users)->pluck('distributor_id');
                $users = $users->filter(function ($value) {
                    return !is_null($value);
                });
                $users = explode(',', $users->implode(','));
                break;
            case 2:
                // regional manager login
                // dd("regional manager login");
                $users = User::where('regional_manager_id', $user->id)->pluck('id');
                $users = User::whereIn('id', $users)->pluck('distributor_id');
                $users = $users->filter(function ($value) {
                    return !is_null($value);
                });
                $users = explode(',', $users->implode(','));
                break;
            case 3:
                // territory manager login
                // dd("territory manager login");
                $users = User::where('id', $user->id)->pluck('distributor_id');
                $users = $users->filter(function ($value) {
                    return !is_null($value);
                });
                $users = explode(',', $users->implode(','));
                break;
            case 4:
                // admin
                // dd("admin");
                $generalIds = User::where('post', 1)->where('user_type', 0)->get()->pluck('id');
                $users = User::whereIn('general_manager_id', $generalIds)->pluck('id');
                $users = User::whereIn('regional_manager_id', $users)->pluck('id');
                $users = User::whereIn('id', $users)->pluck('distributor_id');
                $users = $users->filter(function ($value) {
                    return !is_null($value);
                });
                $users = explode(',', $users->implode(','));
                break;

            case 5:
                // national
                // dd("national");
                $users = User::where('post', 1)->where('division_id', $user->division_id)->where('user_type', 0)->get()->pluck('id');
                $users = User::whereIn('general_manager_id', $users)->pluck('id');
                $users = User::whereIn('regional_manager_id', $users)->pluck('id');
                $users = User::whereIn('id', $users)->pluck('distributor_id');
                $users = $users->filter(function ($value) {
                    return !is_null($value);
                });
                $users = explode(',', $users->implode(','));
                break;
            default:
                // distributor
                // dd("distributor");
                $users = User::where('id', $user->id)->pluck('id');
                break;
        }
    }


    return $users;
}
function InstrumentList()
{
    $data = 'App\Models\InstrumentMaster'::where('status', 1)->orderBy('id', 'asc')->get();

    return $data;
}

function getAvanaInstrumentProductSetDetails()
{
    // Retrieve distinct set_details as groups of instrument_product
    $data = 'App\Models\AvanaInstrumentProduct'::whereNotNull('set_details')
        ->where('set_details', '!=', '')
        ->distinct()
        ->pluck('set_details');

    return $data;
}

function formatIndianCurrency($number)
{
    $number = (int) $number;
    $num = preg_replace('/\D/', '', $number);
    $len = strlen($num);
    if ($len > 3) {
        $last3 = substr($num, -3);
        $rest = substr($num, 0, $len - 3);
        $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
        return $rest . "," . $last3;
    } else {
        return $num;
    }
}


function bookingAnalytics()
{
    // Total bookings (confirmed)
    $totalBookings = 'App\Models\Booking'::where('status', 'confirmed')->count();

    // Last month bookings
    $lastMonthBookings = 'App\Models\Booking'::where('status', 'confirmed')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();

    // This month bookings
    $thisMonthBookings = 'App\Models\Booking'::where('status', 'confirmed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    // Calculate percentage change
    if ($lastMonthBookings > 0) {
        $percentChange = (($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100;
    } else {
        $percentChange = 0; // Avoid division by zero
    }

    return [
        'total_booking' => $totalBookings,
        'change' => $percentChange

    ];
}

function paymentAnalytics()
{
    $thisMonthRevenue = 'App\Models\BookingPayment'::where('payment_status', 1)
        ->whereMonth('payment_date', now()->month)
        ->whereYear('payment_date', now()->year)
        ->sum('amount');

    $lastMonthRevenue = 'App\Models\BookingPayment'::where('payment_status', 1)
        ->whereMonth('payment_date', now()->subMonth()->month)
        ->whereYear('payment_date', now()->subMonth()->year)
        ->sum('amount');

    if ($lastMonthRevenue > 0) {
        $revenueChange = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
    } else {
        $revenueChange = 0; // prevent division by zero
    }

    return [
        'amount' => $thisMonthRevenue,
        'change' => $revenueChange
    ];
}

function activeCustomerAnalytics()
{
    $thisMonthActive = 'App\Models\BookingPayment'::where('payment_status', 1)
        ->whereMonth('payment_date', now()->month)
        ->whereYear('payment_date', now()->year)
        ->whereHas('booking') // ensure it has a booking
        ->get()
        ->pluck('booking.user_id')
        ->unique()
        ->count();



    $lastMonthActive = 'App\Models\BookingPayment'::where('payment_status', 1)
        ->whereMonth('payment_date', now()->subMonth()->month)
        ->whereYear('payment_date', now()->subMonth()->year)
        ->whereHas('booking')
        ->get()
        ->pluck('booking.user_id')
        ->unique()
        ->count();

    if ($lastMonthActive > 0) {
        $activeChange = (($thisMonthActive - $lastMonthActive) / $lastMonthActive) * 100;
    } else {
        $activeChange = 0;
    }

    $totalCustomers = 'App\Models\User'::count();
    $activePercent = $totalCustomers > 0
        ? ($thisMonthActive / $totalCustomers) * 100
        : 0;

    return [
        'percentage' => round($activePercent),
        'change' => $activeChange
    ];
}

function paymentAnalyticsChart()
{
    // Return revenue for the last 6 months
    $labels = [];
    $data = [];

    for ($i = 5; $i >= 0; $i--) {

        $month = now()->subMonths($i);

        $labels[] = $month->format('M'); // Jan, Feb, Mar, ...

        $data[] = \App\Models\BookingPayment::where('payment_status', 1)
            ->whereMonth('payment_date', $month->month)
            ->whereYear('payment_date', $month->year)
            ->sum('amount');
    }

    return [
        'labels' => $labels,
        'data'   => $data
    ];
}
function bookingAnalyticsChart()
{
    $labels = [];
    $data = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);

        $labels[] = $month->format('M');

        $data[] = \App\Models\Booking::where('status', 'confirmed')
            ->whereMonth('checkin_date', $month->month)
            ->whereYear('checkin_date', $month->year)
            ->count();
    }

    return [
        'labels' => $labels,
        'data'   => $data
    ];
}


function occupacyRate()
{
    $current = BookingRoom::whereMonth('created_at', now()->month)
        ->sum('quantity');

    $previous = BookingRoom::whereMonth('created_at', now()->subMonth()->month)
        ->sum('quantity');


    $growth = $previous > 0
        ? (($current - $previous) / $previous) * 100
        : 0;


    return [
        'current' => round($current),
        'growth' => $growth
    ];
}

function roomAnalyticsChart()
{
    return BookingRoom::join('room_types', 'room_types.id', '=', 'booking_rooms.room_type_id')
        ->selectRaw('room_types.room_name as room_type, SUM(booking_rooms.quantity) as total')
        ->groupBy('room_types.room_name')
        ->orderBy('room_types.room_name')
        ->get();
}
