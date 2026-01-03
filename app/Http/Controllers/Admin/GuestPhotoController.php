<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestPhoto;
use App\Models\Hotel;
use Illuminate\Http\Request;

class GuestPhotoController extends Controller
{
    // Show all photos
    public function index(Request $request)
    {

        $page_title = 'Photo List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'photo_list',
                'url' => '',
            ],
        ];
        $search = $request->search;

        $search = $request->search;

        $photos = GuestPhoto::with('hotel')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('hotel', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhere('id', $search);
            })
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('admin.pages.guest_photo.list', compact('photos'));
    }

    // Show upload form
    public function create()
    {
        $hotels = Hotel::get();

        return view('admin.pages.guest_photo.add', compact('hotels'));
    }

    // Store photos
    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'photo_url.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo_url')) {

            foreach ($request->file('photo_url') as $index => $photo) {

                // Create custom filename
                $filename = 'guest_photo_'.time().'_'.$index.'.'.$photo->getClientOriginalExtension();

                // Move to public/guest_photo/
                $photo->move(public_path('images/guest_photo'), $filename);

                // Path to store in DB
                $photoPath = 'images/guest_photo/'.$filename;

                // Save DB entry
                GuestPhoto::create([
                    'hotel_id' => $request->hotel_id,
                    'photo_url' => $photoPath,
                ]);
            }
        }

        return redirect()->route('guest-photo.index')
            ->with('success', 'Guest photos uploaded successfully.');
    }
    // Show edit form
    // public function edit($id)
    // {
    //     $photo = GuestPhoto::findOrFail($id);
    //     return view('admin.pages.guest_photo.edit', compact('photo'));
    // }

    // // Update photo
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'hotel_id' => 'required|exists:hotels,id',
    //         'photo_url' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    //     ]);

    //     $photo = GuestPhoto::findOrFail($id);

    //     // If a new image uploaded, replace old one
    //     if ($request->hasFile('photo_url')) {

    //         // Delete old file
    //         if ($photo->photo_url && file_exists(public_path($photo->photo_url))) {
    //             unlink(public_path($photo->photo_url));
    //         }

    //         $image = $request->file('photo_url');

    //         // Custom file name
    //         $filename = 'guest_photo_' . time() . '.' . $image->getClientOriginalExtension();

    //         // Move to public folder
    //         $image->move(public_path('guest_photo'), $filename);

    //         // Update DB path
    //         $photo->photo_url = 'guest_photo/' . $filename;
    //     }

    //     // Update hotel ID
    //     $photo->hotel_id = $request->hotel_id;
    //     $photo->save();

    //     return redirect()->route('guest-photo.index')
    //         ->with('success', 'Guest photo updated successfully.');
    // }

    // Delete photo
    public function destroy($id)
    {
        $photo = GuestPhoto::findOrFail($id);

        // Delete physical file
        if ($photo->photo_url && file_exists(public_path($photo->photo_url))) {
            unlink(public_path($photo->photo_url));
        }

        // Delete DB row
        $photo->delete();

        return back()->with('success', 'Guest photo deleted successfully.');
    }
}
