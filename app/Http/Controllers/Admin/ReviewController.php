<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Review List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Review_list',
                'url' => '',
            ]
        ];
        $search = $request->search;

   $reviews = Review::when($search, function ($query) use ($search) {
        $query->whereHas('hotel', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        })
        ->orWhere('id', $search);
    })
    ->orderBy('id', 'DESC')
    ->paginate(25);
        return view('admin.pages.review.list', compact('page_title', 'page_description', 'breadcrumbs', 'reviews'));
    }

    public function show($id)
    {
        $page_title = 'Review List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Review_list',
                'url' => '',
            ]
        ];
        $review = Review::findOrFail($id);
        return view('admin.pages.review.view', compact('page_title', 'page_description', 'breadcrumbs', 'review'));
    }
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);
        $review = Review::findOrFail($id);
        $review->reply = $request->reply;
        $review->save();

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function updateStatus($id)
    {

        $review = Review::findOrFail($id);
        $review->is_approved = $review->is_approved ? 0 : 1;

        $review->save();

        return redirect()->back()->with('success', 'Status change successfully!');
    }
}
