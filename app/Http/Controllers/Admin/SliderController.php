<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Slider List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Slider_list',
                'url' => '',
            ],
        ];

        $search = $request->search;

        $sliders = Slider::when($search, function ($query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%") // replace 'title' with the correct column
                ->orWhere('id', $search);
        })
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.pages.slider.list', compact('page_title', 'page_description', 'breadcrumbs', 'sliders'));
    }

    public function create()
    {
        $page_title = 'Slider List';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Slider_list',
                'url' => '',
            ],
        ];

        return view('admin.pages.slider.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $sliderData = $request->only('title', 'subtitle', 'link', 'button_text', 'is_active');

        if ($request->hasFile('image')) {
            Log::info('Image file received.');

            try {

                $image = $request->file('image');
                $filename = 'slider_'.time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('images/sliders'), $filename);
                $path = 'images/sliders/'.$filename;
                $sliderData['image_path'] = $path;
            } catch (\Exception $e) {

                return back()->with('error', 'Failed to upload the image.');
            }
        }

        try {

            Slider::create($sliderData);
        } catch (\Exception $e) {

            Log::error('Failed to create slider: '.$e->getMessage());

            return back()->with('error', 'Failed to create the slider.');
        }

        return redirect()->route('slider.index')->with('success', 'Slider created successfully.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.pages.slider.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $sliderData = $request->only('title', 'subtitle', 'link', 'button_text', 'is_active');

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($slider->image) {
                $path = public_path($slider->icon);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $image = $request->file('image');
            $filename = 'slider_'.time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/sliders'), $filename);
            $path = 'images/sliders/'.$filename;
            $sliderData['image_path'] = $path;
        }
        $slider->update($sliderData);

        return redirect()->route('slider.index')->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        // Delete the image from storage
        if ($slider->image_path) {
            $path = public_path($slider->image_path);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $slider->delete();

        return redirect()->route('slider.index')->with('success', 'Slider deleted successfully.');
    }
}
