<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Coupons List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Coupons_list',
                'url' => '',
            ],
        ];

        $search = $request->input('search');
        $coupons = Coupon::when($search, function ($query) use ($search) {
            $query->where('code', 'LIKE', "%{$search}%");
        })
            ->orderBy('coupon_id', 'DESC')
            ->paginate(15);

        return view('admin.pages.coupon.list', compact('page_title', 'page_description', 'breadcrumbs', 'coupons'));
    }

    public function create()
    {
        $page_title = 'Coupons List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Coupons_list',
                'url' => '',
            ],
        ];

        return view('admin.pages.coupon.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:flat,percent',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date|before_or_equal:valid_to',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);
        Coupon::create($validatedData);

        return redirect()->route('coupons.index')->with('success', 'Coupon created successfully!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.pages.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validatedData = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon->coupon_id, 'coupon_id'),
            ],
            'discount_type' => 'required|in:flat,percent',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $coupon->update($validatedData);

        return redirect()->route('coupons.index')->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Coupon deleted successfully!');
    }
}
