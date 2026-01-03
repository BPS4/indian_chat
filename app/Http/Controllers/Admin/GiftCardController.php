<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Gift Card List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Gift_card_list',
                'url' => '',
            ]
        ];
           $search = $request->search;
       $giftcards = GiftCard::when($search, function ($query) use ($search) {
        $query->where('code', 'LIKE', "%{$search}%")
              ;
    })
    ->orderBy('giftcard_id', 'DESC')
    ->paginate(15);

        return view('admin.pages.gift_card.list', compact('page_title', 'page_description', 'breadcrumbs', 'giftcards'));
    }

    public function create()
    {
        $page_title = 'Gift Card List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Gift_card_list',
                'url' => '',
            ]
        ];

        return view('admin.pages.gift_card.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'balance_amount' => 'required',
            'expiry_date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);
        GiftCard::create($validatedData);

        return redirect()->route('gift-card.index')->with('success', 'Gift Card created successfully!');
    }



    public function edit(GiftCard $giftCard)
    {
        return view('admin.pages.gift_card.edit', compact('giftCard'));
    }

    public function update(Request $request, GiftCard $giftCard)
    {

        $validatedData = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('gift_cards', 'code')->ignore($giftCard->giftcard_id, 'giftcard_id'),
            ],
            'balance_amount' => 'required',
            'expiry_date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        $giftCard->update($validatedData);

        return redirect()->route('gift-card.index')->with('success', 'Gift Card updated successfully!');
    }

    public function destroy(GiftCard $giftCard)
    {
        $giftCard->delete();

        return redirect()->route('gift-card.index')->with('success', 'Gift Card deleted successfully!');
    }
}
