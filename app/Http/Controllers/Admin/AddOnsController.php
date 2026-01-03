<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

class AddOnsController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Addons List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Addons_list',
                'url' => '',
            ],
        ];
        $search = $request->search;

        // dd($search);

        $addOns = Addon::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('id', $search);
        })
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('admin.pages.addons.list', compact('page_title', 'page_description', 'breadcrumbs', 'addOns'));
    }

    public function create()
    {
        $page_title = 'Addons List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Addons_list',
                'url' => '',
            ],
        ];

        return view('admin.pages.addons.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        AddOn::create($validated);

        return redirect()->route('addons.index')->with('success', 'Add-on created successfully!');
    }

    public function edit(AddOn $addon)
    {
        return view('admin.pages.addons.edit', compact('addon'));
    }

    public function update(Request $request, AddOn $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $addon->update($validated);

        return redirect()->route('addons.index')->with('success', 'Add-on updated successfully!');
    }

    public function destroy(AddOn $addon)
    {
        $addon->delete();

        return redirect()->route('addons.index')->with('success', 'Add-on deleted successfully!');
    }
}
