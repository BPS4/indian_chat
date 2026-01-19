<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityMaster;
use App\Models\Hotel;
use App\Models\HotelFacility;
use App\Models\HotelPhoto;
use App\Models\HotelPolicy;
use App\Models\Localty;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    public function message_list(Request $request)
    {
        try {
            $page_title = 'Messages List';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Message_list',
                    'url' => '',
                ],
            ];

            $messages = Message::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('message', 'LIKE', "%{$search}%")
                    ->orWhere('state', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('id', $search);
            })
                ->when($request->state, function ($query, $state) {
                    $query->where('state', $state);
                })
                ->when($request->city, function ($query, $city) {
                    $query->where('city', $city);
                })
                  ->orderBy('id', 'desc')
                ->paginate(25);

            return view('admin.pages.messages.list', compact('page_title', 'page_description', 'breadcrumbs', 'messages'));
        } catch (\Exception $e) {
            Log::error('Message list error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_message()
    {
        $page_title = 'Add Message';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Add Message',
                'url' => '',
            ],
        ];

        $city = User::select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->get();

        return view('admin.pages.messages.add', compact('page_title', 'page_description', 'breadcrumbs', 'city'));
    }

    public function edit_message($id)
    {


        try {
            $page_title = 'Edit Message';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Edit Message',
                    'url' => '',
                ],
            ];

            $message = Message::findOrFail($id);

            $states = [
                'Andhra Pradesh',
                'Arunachal Pradesh',
                'Assam',
                'Bihar',
                'Chhattisgarh',
                'Goa',
                'Gujarat',
                'Haryana',
                'Himachal Pradesh',
                'Jharkhand',
                'Karnataka',
                'Kerala',
                'Madhya Pradesh',
                'Maharashtra',
                'Manipur',
                'Meghalaya',
                'Mizoram',
                'Nagaland',
                'Odisha',
                'Punjab',
                'Rajasthan',
                'Sikkim',
                'Tamil Nadu',
                'Telangana',
                'Tripura',
                'Uttar Pradesh',
                'Uttarakhand',
                'West Bengal',
                'Delhi',
                'Jammu and Kashmir',
                'Ladakh',
                'Puducherry'
            ];

            return view('admin.pages.hotels.edit', compact('page_title', 'page_description', 'breadcrumbs', 'message', 'states'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update_message(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'media'           => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:10240',
                'youtube_link'    => 'nullable|url',
                'description'     => 'required|string',
                'calling_number'  => 'required|string|max:15',
                'website_link'    => 'nullable|url',
                'instagram_link'  => 'nullable|url',
                'facebook_link'   => 'nullable|url',
                'telegram_link'   => 'nullable|url',
                'state'           => 'required|string',
                'city'            => 'nullable|string',
                'total_users'     => 'nullable|integer',
            ]);

            DB::beginTransaction();

            $message = Message::findOrFail($id);

            $data = $request->only([
                'youtube_link',
                'description',
                'calling_number',
                'website_link',
                'instagram_link',
                'facebook_link',
                'telegram_link',
                'state',
                'city',
                'total_users',
            ]);

            $data['country'] = 'India';
            $data['auto_send'] = $request->has('auto_send');
            $data['message'] = $request->description;

            if ($request->hasFile('media')) {
                // Delete old media if exists
                if ($message->media && \Storage::disk('public')->exists($message->media)) {
                    \Storage::disk('public')->delete($message->media);
                }
                $data['media'] = $request->file('media')->store('messages', 'public');
            }

            $message->update($data);

            DB::commit();

            return redirect()->route('message.list')->with('success', 'Message updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Message update error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete_message($id)
    {
        try {
            $message = Message::findOrFail($id);

            // Delete media file if exists
            if ($message->media && \Storage::disk('public')->exists($message->media)) {
                \Storage::disk('public')->delete($message->media);
            }

            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Message delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    


    public function store(Request $request)
    {
        try {


            // =========================
            // POST REQUEST → SAVE DATA
            // =========================
            if ($request->isMethod('post')) {

                // dd($request->all());

                $validated = $request->validate([
                    'media'           => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:10240',
                    'youtube_link'    => 'nullable|url',
                    'description'     => 'required|string',
                    'calling_number'  => 'required|string|max:10',
                    'website_link'    => 'nullable|url',
                    'instagram_link'  => 'nullable|url',
                    'facebook_link'   => 'nullable|url',
                    'telegram_link'   => 'nullable|url',
                    'state'           => 'required|string',
                    'city'            => 'nullable|string',
                    'total_users'     => 'nullable|integer',
                ]);

                DB::beginTransaction();

                // Take all request data
                $data = $request->only([
                    'youtube_link',
                    'description',
                    'calling_number',
                    'website_link',
                    'instagram_link',
                    'facebook_link',
                    'telegram_link',
                    'state',
                    'city',
                    'total_users',
                ]);
                $data['country']   = 'India';
                $data['auto_send'] = $request->has('auto_send');

                if ($request->hasFile('media')) {
                    $data['media'] = $request->file('media')->store('messages', 'public');
                }

                Message::create($data);

                DB::commit();

                return redirect()->back()->with('success', 'Message send successfully');
            }


            $states = [
                'Andhra Pradesh',
                'Arunachal Pradesh',
                'Assam',
                'Bihar',
                'Chhattisgarh',
                'Goa',
                'Gujarat',
                'Haryana',
                'Himachal Pradesh',
                'Jharkhand',
                'Karnataka',
                'Kerala',
                'Madhya Pradesh',
                'Maharashtra',
                'Manipur',
                'Meghalaya',
                'Mizoram',
                'Nagaland',
                'Odisha',
                'Punjab',
                'Rajasthan',
                'Sikkim',
                'Tamil Nadu',
                'Telangana',
                'Tripura',
                'Uttar Pradesh',
                'Uttarakhand',
                'West Bengal',
                'Delhi',
                'Jammu and Kashmir',
                'Ladakh',
                'Puducherry'
            ];

            return view('admin.pages.hotels.add', compact('states'));
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function add_hotel(Request $request)
    {
        try {

            // dd('hi');
            // Handle POST request
            if ($request->isMethod('post')) {
                // dd($request->all());

                // Validation
                $request->validate([
                    'hotel_name' => 'required|string|max:255',
                    'hotel_address' => 'required|string|max:500',
                    'description' => 'required|string',

                    'location_id' => 'required|integer|exists:locations,id',
                    'locality_id' => 'required|integer|exists:localties,id',
                    'longitude' => 'nullable|string|max:50',
                    'latitude' => 'nullable|string|max:50',

                    'facilities' => 'nullable|array',
                    'facilities.*' => 'integer|exists:facility_masters,id',

                    'images' => 'required|array|max:4',
                    'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    'cover_image' => 'required|integer|between:1,4',

                    'check_in_time' => 'required|date_format:H:i',
                    'check_out_time' => 'required|date_format:H:i',
                    'cancellation_policy' => 'required|string',
                    'extra_bed_policy' => 'required|string',
                    'child_policy' => 'required|string',
                ]);

                // Wrap in transaction
                DB::beginTransaction();

                try {
                    // 1. Save Hotel Basic Info
                    $hotel = new Hotel;
                    $hotel->name = $request->hotel_name;
                    $hotel->address = $request->hotel_address;
                    $hotel->description = $request->description;
                    $hotel->location_id = $request->location_id;
                    $hotel->locality_id = $request->locality_id;
                    $hotel->longitude = $request->longitude;
                    $hotel->latitude = $request->latitude;
                    $hotel->status = 'active';

                    $hotel->save();

                    // 2. Save Hotel Policies
                    $policy = new HotelPolicy;
                    $policy->hotel_id = $hotel->id;
                    $policy->checkin_time = $request->check_in_time;
                    $policy->checkout_time = $request->check_out_time;
                    $policy->cancellation_policy = $request->cancellation_policy;
                    $policy->extra_bed_policy = $request->extra_bed_policy;
                    $policy->child_policy = $request->child_policy;
                    $policy->save();

                    // 3. Save Facilities (many-to-many) using HotelFacility model
                    if ($request->has('facilities')) {
                        foreach ($request->facilities as $facility_id) {
                            HotelFacility::create([
                                'hotel_id' => $hotel->id,
                                'facility_id' => $facility_id,
                            ]);
                        }
                    }

                    // 4. Save Images using HotelPhoto model
                    // Get existing images for indexing (0,1,2,3)
                    $existingPhotos = $hotel->hotelPhotos;

                    if ($request->hasFile('images')) {

                        foreach ($request->file('images') as $index => $image) {

                            if ($image) {

                                // ---- GENERATE CUSTOM NAME ---- //
                                $extension = $image->getClientOriginalExtension();
                                $filename = 'hotel_' . $hotel->id . '_' . ($index + 1) . '.' . $extension;

                                $path = public_path('images/hotels');

                                // Create folder if not exists
                                if (! file_exists($path)) {
                                    mkdir($path, 0777, true);
                                }

                                // ---- DELETE OLD IMAGE IF EXISTS ---- //
                                if (isset($existingPhotos[$index])) {

                                    $oldFile = public_path($existingPhotos[$index]->photo_url);

                                    if (file_exists($oldFile)) {
                                        unlink($oldFile);
                                    }

                                    // ---- MOVE NEW FILE ---- //
                                    $image->move($path, $filename);

                                    // ---- UPDATE DB RECORD ---- //
                                    $existingPhotos[$index]->update([
                                        'photo_url' => 'images/hotels/' . $filename,
                                        'is_cover' => ($index + 1) == $request->cover_image ? 1 : 0,
                                    ]);
                                } else {

                                    // ---- CREATE NEW PHOTO RECORD ---- //
                                    $image->move($path, $filename);

                                    HotelPhoto::create([
                                        'hotel_id' => $hotel->id,
                                        'photo_url' => 'images/hotels/' . $filename,
                                        'is_cover' => ($index + 1) == $request->cover_image ? 1 : 0,
                                    ]);
                                }
                            }
                        }
                    }

                    // DB::table('hotel_images')->insert($imagesData);

                    DB::commit();

                    return redirect()->back()->with('success', 'Hotel created successfully!');
                } catch (\Exception $e) {
                    DB::rollback();

                    return redirect()->back()->with('error', $e->getMessage());
                }
            }

            // Handle GET request: show the add hotel form
            $page_title = 'Add Hotel';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Add Hotel',
                    'url' => '',
                ],
            ];

            $locations = Location::with('locality')->get();
            $FacilityMaster = FacilityMaster::where('facility_for', 'hotel')->get();
            $facilities = [];

            return view('admin.pages.hotels.add', compact(
                'page_title',
                'page_description',
                'breadcrumbs',
                'locations',
                'facilities',
                'FacilityMaster'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $page_title = 'Edit Hotel';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Edit Hotel',
                'url' => '',
            ],
        ];

        $hotel = Hotel::findOrFail($id);
        $locations = Location::with('locality')->get();
        $FacilityMaster = FacilityMaster::where('facility_for', 'hotel')->get();
        $facilities = [];

        return view('admin.pages.hotels.edit', compact(
            'page_title',
            'page_description',
            'breadcrumbs',
            'locations',
            'facilities',
            'FacilityMaster',
            'hotel'
        ));
    }

    public function update(Request $request, $id)
    {
        // Validate required fields
        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'hotel_address' => 'required|string|max:500',
            'description' => 'required|string',
            'location_id' => 'required|integer',
            'locality_id' => 'nullable|integer',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'facilities' => 'nullable|array',
            'images' => 'nullable|array',
            'cover_image' => 'nullable',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
        ]);

        DB::beginTransaction();

        try {

            $hotel = Hotel::findOrFail($id);

            $hotel->name = $request->hotel_name;
            $hotel->address = $request->hotel_address;
            $hotel->description = $request->description;
            $hotel->location_id = $request->location_id;
            $hotel->locality_id = $request->locality_id;
            $hotel->longitude = $request->longitude;
            $hotel->latitude = $request->latitude;
            $hotel->save();

            if ($request->has('facilities')) {
                // Remove facilities not in the request
                $hotel->hotelFacilities()->whereNotIn('id', $request->facilities)->delete();

                // Add new ones
                foreach ($request->facilities as $facility_id) {
                    $hotel->hotelFacilities()->firstOrCreate(['facility_id' => $facility_id]);
                }
            } else {
                $hotel->hotelFacilities()->delete();
            }

            /** -------------------------------------------------------------
             * 3️⃣  UPDATE HOTEL IMAGES
             * -------------------------------------------------------------*/
            // $existingPhotos = $hotel->hotelPhotos()->get(); // collection of old images

            // if ($request->hasFile('images')) {

            //     foreach ($request->file('images') as $index => $image) {

            //         // ---- GENERATE CUSTOM NAME ---- //
            //         $extension = $image->getClientOriginalExtension();
            //         $filename = 'hotel_' . $hotel->id . '_' . ($index) . '.' . $extension;

            //         $path = public_path('images/hotels');

            //         // Create folder if not exists
            //         if (!file_exists($path)) {
            //             mkdir($path, 0777, true);
            //         }

            //         // dd($existingPhotos);

            //         // Get existing photo at this index
            //         $existingPhoto = $existingPhotos->get($index);

            //         if ($existingPhoto) {

            //             // ---- DELETE OLD IMAGE IF EXISTS ---- //
            //             $oldFile = public_path($existingPhoto->photo_url);
            //             if (file_exists($oldFile)) {
            //                 unlink($oldFile);
            //             }

            //             // dd($filename);
            //             // ---- MOVE NEW FILE ---- //
            //             $image->move($path, $filename);

            //             //  dd($request->cover_image);

            //             // ---- UPDATE DB RECORD ---- //
            //             $existingPhoto->update([
            //                 'photo_url' => 'images/hotels/' . $filename,
            //                 'is_cover' => ($index) == $request->cover_image ? 1 : 0,
            //             ]);
            //         } else {

            //             // ---- MOVE NEW FILE ---- //
            //             $image->move($path, $filename);

            //             // ---- CREATE NEW PHOTO RECORD ---- //
            //             HotelPhoto::create([
            //                 'hotel_id' => $hotel->id,
            //                 'photo_url' => 'images/hotels/' . $filename,
            //                 'is_cover' => ($index) == $request->cover_image ? 1 : 0,
            //             ]);
            //         }

            //         // Clear file cache (helps overwrite issues)
            //         clearstatcache();
            //     }
            // }

            // // ---- UPDATE COVER IMAGE FLAG IF NOT ALREADY SET ---- //
            // if ($request->cover_image) {
            //     foreach ($hotel->hotelPhotos as $i => $photo) {
            //         $photo->is_cover = (($i) == $request->cover_image) ? 1 : 0;
            //         $photo->save();
            //     }
            // }
            $existingPhotos = $hotel->hotelPhotos->keyBy('id');

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $key => $image) {

                    $extension = $image->getClientOriginalExtension();
                    $filename = 'hotel_' . $hotel->id . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                    $path = public_path('images/hotels');

                    if (! file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    // CASE 1: Update existing photo
                    if ($existingPhotos->has($key)) {

                        $existingPhoto = $existingPhotos[$key];

                        // delete old file
                        $old = public_path($existingPhoto->photo_url);
                        if (file_exists($old)) {
                            unlink($old);
                        }

                        // save new
                        $image->move($path, $filename);

                        // update DB record
                        $existingPhoto->update([
                            'photo_url' => 'images/hotels/' . $filename,
                            'is_cover' => ($request->cover_image == $key) ? 1 : 0,
                        ]);
                    } else {

                        // CASE 2: New photo
                        $image->move($path, $filename);

                        $newPhoto = HotelPhoto::create([
                            'hotel_id' => $hotel->id,
                            'photo_url' => 'images/hotels/' . $filename,
                            'is_cover' => ($request->cover_image == $key) ? 1 : 0,
                        ]);

                        // If frontend sent "new_3" etc., update cover_image to actual ID
                        if ($request->cover_image === $key) {
                            $request->merge(['cover_image' => $newPhoto->id]);
                        }
                    }
                }
            }

            if ($request->cover_image) {

                // remove cover from all
                HotelPhoto::where('hotel_id', $hotel->id)->update(['is_cover' => 0]);

                // set correct one
                HotelPhoto::where('id', $request->cover_image)->update(['is_cover' => 1]);
            }

            $policy = $hotel->hotelPolicies()->firstOrNew();

            $policy->checkin_time = $request->check_in_time;
            $policy->checkout_time = $request->check_out_time;
            $policy->cancellation_policy = $request->cancellation_policy;
            $policy->extra_bed_policy = $request->extra_bed_policy;
            $policy->child_policy = $request->child_policy;
            $policy->save();

            DB::commit();

            return redirect()
                ->route('hotels.list')
                ->with('success', 'Hotel updated successfully!');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function locality_check($location_id = null)
    {

        // dd($location_id);

        try {
            $localities = Localty::where('location_id', $location_id)->get();

            return response()->json($localities);
        } catch (\Exception $e) {
            Log::error('Error fetching locality: ' . $e->getMessage());

            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus(Hotel $hotel)
    {
        // dd('hi');
        $hotel->status = $hotel->status === 'active' ? 'inactive' : 'active';
        $hotel->save();

        return response()->json([
            'success' => true,
            'new_status' => $hotel->status,
        ]);
    }
}
