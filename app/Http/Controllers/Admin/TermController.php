<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TermController extends Controller
{
    public function index(Request $request)
    {
        try {

            $page_title = 'Term & Conditions';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Term & Conditions',
                    'url' => '',
                ],
            ];
            $search = $request->search;

            $terms = Term::when($search, function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%") // replace 'title' with the correct column
                    ->orWhere('id', $search);
            })
                ->orderBy('id', 'DESC')
                ->paginate(15);

            return view('admin.pages.term.list', compact('page_title', 'page_description', 'breadcrumbs', 'terms'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $page_title = 'Term & Conditions';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Term & Conditions',
                'url' => '',
            ],
        ];

        return view('admin.pages.term.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $termData = $request->only('title', 'content', 'is_active');

        try {

            Term::create($termData);
        } catch (\Exception $e) {

            Log::error('Failed to create slider: '.$e->getMessage());

            return back()->with('error', 'Failed to create the slider.');
        }

        return redirect()->route('term.index')->with('success', 'Term created successfully.');
    }

    public function edit(Term $term)
    {
        return view('admin.pages.term.edit', compact('term'));
    }

    public function update(Request $request, Term $term)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $termData = $request->only('title', 'content', 'is_active');
        $term->update($termData);

        return redirect()->route('term.index')->with('success', 'Term updated successfully.');
    }
}
