<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityGroup;
use App\Models\FacilityMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Facility List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Facility_list',
                'url' => '',
            ],
        ];

         $search = $request->search;

        //  dd($search);

        $facilities = FacilityMaster::when($search, function ($query) use ($search) {
            $query->where('facility_name', 'LIKE', "%{$search}%")
                ->orWhere('id', $search);
        })
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.pages.facility.list', compact('page_title', 'page_description', 'breadcrumbs', 'facilities'));
    }

    public function create()
    {
        $page_title = 'Facility List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Facility_list',
                'url' => '',
            ],
        ];

        $facilityGroups = FacilityGroup::get();

        return view('admin.pages.facility.add', compact('page_title', 'page_description', 'breadcrumbs', 'facilityGroups'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'group_id' => 'required',
            'facility_name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facility_for' => 'required|string|max:20',
        ]);

        $facilityData = $request->only('group_id', 'facility_name', 'facility_for');

        if ($request->hasFile('icon')) {
            Log::info('icon file received.');

            try {
                $image = $request->file('icon');
                $filename = 'icon_'.time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('images/facility'), $filename);
                $path = 'images/facility/'.$filename;

                $facilityData['icon'] = $path;
            } catch (\Exception $e) {

                return back()->with('error', 'Failed to upload the image.');
            }
        }

        try {

            FacilityMaster::create($facilityData);
        } catch (\Exception $e) {

            Log::error('Failed to create facility: '.$e->getMessage());

            return back()->with('error', 'Failed to create the facility.');
        }

        return redirect()->route('facility.index')->with('success', 'Facility created successfully.');
    }

    public function edit(FacilityMaster $facility)
    {
        $facilityGroups = FacilityGroup::get();

        return view('admin.pages.facility.edit', compact('facility', 'facilityGroups'));
    }

    public function update(Request $request, FacilityMaster $facility)
    {
        $request->validate([
            'group_id' => 'required',
            'facility_name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facility_for' => 'required|string|max:20',
        ]);

        $facilityData = $request->only('group_id', 'facility_name', 'facility_for');

        if ($request->hasFile('icon')) {
            // Delete the old icon if it exists
            if ($facility->icon) {
                $path = public_path($facility->icon);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $image = $request->file('icon');
            $filename = 'icon_'.time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/facility'), $filename);
            $path = 'images/facility/'.$filename;
            $facilityData['icon'] = $path;
        }
        $facility->update($facilityData);

        return redirect()->route('facility.index')->with('success', 'Facility updated successfully.');
    }

    public function destroy(FacilityMaster $facility)
    {
        // Delete the image from storage
        if ($facility->icon) {
            $path = public_path($facility->icon);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $facility->delete();

        return redirect()->route('facility.index')->with('success', 'Facility deleted successfully.');
    }
}
