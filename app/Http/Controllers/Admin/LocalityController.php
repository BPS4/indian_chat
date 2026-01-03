<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Localty;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocalityController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Locality List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Locality_list',
                'url' => '',
            ]
        ];

       $search = $request->input('search');

    $localities = Localty::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");

        })
        ->orderBy('id', 'DESC')
        ->paginate(15);

        return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities'));
    }

    public function add()
    {
        $page_title = 'Locality List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Locality_list',
                'url' => '',
            ]
        ];

        $locations = Location::get();
        return view('admin.pages.locality.add', compact('page_title', 'page_description', 'breadcrumbs', 'locations'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required',
        ]);

        $LocalityData = $request->only('name', 'location_id');


        try {

            Localty::create($LocalityData);
        } catch (\Exception $e) {

            Log::error('Failed to create location: ' . $e->getMessage());
            return back()->with('error', 'Failed to create the locality.');
        }


        return redirect()->route('locality.list')->with('success', 'Locality created successfully.');
    }


    public function edit(Localty $localty)
    {
        $locations = Location::get();

        return view('admin.pages.locality.edit', compact('localty','locations'));
    }

    public function update(Request $request, Localty $localty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required',
        ]);

        $localityData = $request->only('name', 'location_id');


        $localty->update($localityData);

        return redirect()->route('locality.list')->with('success', 'Locality updated successfully.');
    }

    public function destroy(Localty $localty)
    {
        $localty->delete();

        return redirect()->route('locality.list')->with('success', 'Locality deleted successfully.');
    }
}
