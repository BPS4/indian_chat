<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityGroup;
use Illuminate\Http\Request;

class FacilityGroupController extends Controller
{
    public function index()
    {
        $groups = FacilityGroup::withCount('facilities')->get();
        return response()->json($groups);
    }

    // Add new group
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|unique:facility_groups,group_name'
        ]);

        $group = FacilityGroup::create(['group_name' => $request->group_name]);
        return response()->json($group);
    }

    // Delete a group
    public function destroy($id)
    {
        $group = FacilityGroup::findOrFail($id);
        $group->delete();
        return response()->json(['success' => true]);
    }
}
