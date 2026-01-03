<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Location List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Location_list',
                'url' => '',
            ],
        ];

        $search = $request->search;

        $locations = Location::when($search, function ($query) use ($search) {
            $query->where('city', 'LIKE', "%{$search}%")
                ->orWhere('state', 'LIKE', "%{$search}%")
                ->orWhere('country', 'LIKE', "%{$search}%")
                ->orWhere('zipcode', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.pages.location.list', compact('page_title', 'page_description', 'breadcrumbs', 'locations'));
    }

    public function add()
    {
        $page_title = 'Location List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Location_list',
                'url' => '',
            ],
        ];

        return view('admin.pages.location.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for image upload
        ]);

        $locationData = $request->only('city', 'state', 'country', 'zipcode');

        if ($request->hasFile('image')) {
            Log::info('Image file received.');

            try {

                $image = $request->file('image');
                $filename = 'location_'.time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('images/locations'), $filename);
                $path = 'images/locations/'.$filename;
                $locationData['image'] = $path;
            } catch (\Exception $e) {

                return back()->with('error', 'Failed to upload the image.');
            }
        }

        try {

            Location::create($locationData);
        } catch (\Exception $e) {

            Log::error('Failed to create location: '.$e->getMessage());

            return back()->with('error', 'Failed to create the location.');
        }

        return redirect()->route('location.list')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        return view('admin.pages.location.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for image upload
        ]);

        $locationData = $request->only('city', 'state', 'country', 'zipcode');

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($location->image) {
                 $path = public_path($location->image);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $image = $request->file('image');
            $filename = 'location_'.time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/locations'), $filename);
            $path = 'images/locations/'.$filename;
            $locationData['image'] = $path;
        }
        $location->update($locationData);

        return redirect()->route('location.list')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        // Delete the image from storage

        if ($location->image) {
            $path = public_path($location->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $location->delete();

        return redirect()->route('location.list')->with('success', 'Location deleted successfully.');
    }
}
